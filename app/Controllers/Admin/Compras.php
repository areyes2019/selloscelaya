<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PedidosModel;
use App\Models\ProveedoresModel;
use App\Models\ArticulosModel;
use App\Models\DetallePedidosModel;
use App\Models\GastosModel;
use App\Models\InventarioModel;
use Dompdf\Dompdf;
use CodeIgniter\API\ResponseTrait;
class Compras extends BaseController
{
	use ResponseTrait;
    public function index()
	{
		$db= \Config\Database::connect();
		$proveedor = new ProveedoresModel();
		
		$builder = $db->table('sellopro_pedidos');
		$builder->select('sellopro_pedidos.*, sellopro_proveedores.empresa');
		$builder->join('sellopro_proveedores', 'sellopro_proveedores.id_proveedor = sellopro_pedidos.proveedor');
		$pedidos = $builder->get()->getResultArray();

		$data['proveedor'] = $proveedor->findAll();
		$data['pedidos'] = $pedidos;

		return view('Panel/compras',$data);

	}
	
	public function pedido($id)
	{
		$query = new PedidosModel();
        $resultado = $query->select('pagado, entregada')
                       ->where('id_pedido', $id)
                       ->first();
    
        return json_encode($resultado ?: ['pagada' => null, 'entregada' => null]);
	}
	public function nueva($id)
	{
		//vamops a guardar un slgu y un cliente

		$caracteres_permitidos = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
   		$longitud = 12;
   		$slug = substr(str_shuffle($caracteres_permitidos), 0, $longitud);

   		$hoy = date("Y-m-d");
   		$caduca = date("Y-m-d",strtotime($hoy."+ 30 days"));
   		$nuevo_registro = new PedidosModel();
   		$data=[
   			'slug'=>$slug,
   			'proveedor'=>$id,
   			'fecha'=>$hoy,
   			'caduca'=>$caduca,
   			'pagado'=> 0
   		];
   		$nuevo_registro->insert($data);
   		return redirect()->to(base_url('pagina_orden/'.$slug));
		
	}
	public function pagina($slug)
	{
		$db = \Config\Database::connect();
		$proveedor = new ProveedoresModel();
		$pedido = new PedidosModel();
		$articulos = new ArticulosModel();

		//vamos a buscar el pedido
		$pedido->where('slug',$slug);
		$resultado = $pedido->findAll();

		//buscamos al proveedor
		$proveedor->where('id_proveedor',$resultado[0]['proveedor']);
		$resultado_proveedor = $proveedor->findAll();

		//buscamos los articulos para el modal
		$modal = $articulos->findAll();

		//lineas de detalle
		$builder = $db->table('sellopro_detalles_pedido');
		$builder->where('id_detalle_pedido',$resultado[0]['id_pedido']);
		$builder->join('sellopro_articulos','sellopro_articulos.id_articulo = sellopro_detalles_pedido.id_articulo');
		$detalles = $builder->get()->getResultArray();

		$data = [
			'pedido'	=> $resultado,	
			'proveedor' => $resultado_proveedor, 
			'articulos' => $modal,
			'detalles'  => $detalles,
			'pedidos_id'=> $resultado[0]['id_pedido'],
            'suma_total'=> $resultado[0]['total']
		];
		return view('Panel/nueva_compra', $data);
		
	}
	
    public function agregar()
    {
        $db = \Config\Database::connect();
        $query = new ArticulosModel();
        $model = new DetallePedidosModel();

        $request = \Config\Services::request();
        
        // Obtener parámetros con validación básica
        $articulo = $request->getVar('id_articulo');
        $pedido = (int)$request->getVar('pedidos_id');
        $cantidad = (int)$request->getVar('cantidad') ?: 1; // Si no viene cantidad, default 1
        
        // Validar cantidad mínima
        if ($cantidad <= 0) {
            return "2"; // Podrías usar códigos diferentes para distintos errores
        }

        // Verificar si el producto ya está agregado
        $doble = $db->table('sellopro_detalles_pedido');
        $doble->where('id_pedido', $pedido);
        $doble->where('id_articulo', $articulo);
        $es_duplicado = $doble->countAllResults();
        
        if ($es_duplicado > 0) {
            return "1"; // Está duplicado
        }

        // Obtener información del artículo
        $query->where('id_articulo', $articulo);
        $resultado = $query->findAll();

        if (empty($resultado)) {
            return "3"; // Artículo no encontrado
        }

        $precio = $resultado[0]['precio_prov'];
        $total = $precio * $cantidad;
        
        // Preparar datos para inserción
        $data = [
            'id_articulo' => $articulo,
            'p_unitario'  => $precio,
            'cantidad'    => $cantidad,
            'total'       => $total,
            'id_pedido'   => $pedido,
        ];

        // Insertar en la base de datos
        if (!$model->insert($data)) {
            return "4"; // Error al insertar
        }

        // Actualizar el total del pedido
        $builder = $db->table('sellopro_detalles_pedido');
        $builder->where('id_pedido', $pedido);
        $builder->selectSum('total');
        $sum = $builder->get()->getResultArray();
        $suma_total = $sum[0]['total'];

        $totalModel = new PedidosModel();
        $datos = ['total' => $suma_total];
        $totalModel->update($pedido, $datos);

        return "0"; // Éxito
    }
	public function mostrar_detalles($id)
	{
		//encontrar el articulo completo
		$db = \Config\Database::connect();
		$builder = $db->table('sellopro_detalles_pedido');
		$builder->where('id_pedido',$id);
		$builder->join('sellopro_articulos','sellopro_articulos.id_articulo = sellopro_detalles_pedido.id_articulo');
		$resultado = $builder->get()->getResultArray();

		//mostrar totales
		$total = new PedidosModel();
		$total->where('id_pedido',$id);
		$suma_total = $total->findAll();
		$porcenteje = 16;
		$monto = $suma_total[0]['total'];
		$iva = $monto*($porcenteje/100);
		$pago_total = $monto+$iva;

		
		$data=[
			'articulo'=>$resultado,
			'sub_total'=> number_format($monto,2),
			'iva'=> number_format($iva,2),
			'total'=>number_format($pago_total,2),
		];

		return json_encode($data);
		
	}
	public function borrar_linea($id)
	{
		
		$modelo = new DetallePedidosModel();
		//sacamos el numero de cotizacion
		$modelo->where('id_detalle_pedido',$id);
		$ver_modelo = $modelo->findAll();
		$numero = $ver_modelo[0]['id_pedido'];
		$modelo->delete($id);

		$fun = $this->totales($numero);
		return $fun;

	}
	public function totales($numero)
	{
		$db = \Config\Database::connect();
		$builder = $db->table('sellopro_detalles_pedido');
		$builder->where('id_pedido',$numero);
		$builder->selectSum('total');
		$sum = $builder->get()->getResultArray();

		$suma_total = $sum[0]['total'];
		$total = new PedidosModel();
		$datos=[
			'total'=>$suma_total,
		];
		$total->update($numero,$datos);
	}
	public function modificar_cantidad()
    {
      $request = \Config\Services::request();
      $id = $request->getVar('id'); // Mejor usar getPost para datos POST
      $cantidad = $request->getVar('cantidad');
      
      if (!$id || !$cantidad) {
        return $this->response->setJSON(['error' => 'Datos incompletos']);
      }

      $lineaModel = new DetallePedidosModel();
      
      // Obtener la línea actual
      $linea = $lineaModel->find($id);
      if (!$linea) {
        return $this->response->setJSON(['error' => 'Línea no encontrada']);
      }

      // Actualizar datos
      $datos = [
        'cantidad' => $cantidad,
        'total' => $linea['p_unitario'] * $cantidad
      ];

      if (!$lineaModel->update($id, $datos)) {
        return $this->response->setJSON(['error' => 'Error al actualizar']);
      }

      // Recalcular totales
      return $this->totales($linea['id_pedido']);
    }
	public function eliminar($id)
	{
		$db = \Config\Database::connect();

		$modelo = new CotizacionesModel();
		$modelo->delete($id);

		$builder = $db->table('sellopro_detalles');
		$builder->where('id_cotizacion',$id);
		$builder->delete();

		return redirect()->to('/cotizaciones');

	}
	public function cotizacion_pdf($id)
	{
		$db = \Config\Database::connect();

		//datos del proveedor
		$proveedor_query = new PedidosModel();
		$proveedor_query->where('id_pedido',$id);
		$resultado_cotizacion = $proveedor_query->findAll();

		$proveedor = new ProveedoresModel();
		$proveedor->where('id_proveedor',$resultado_cotizacion[0]['proveedor']);
		$resultado = $proveedor->findAll();

		//mostrar los articulos
		$builder = $db->table('sellopro_detalles_pedido');
		$builder->where('pedido_id',$id);
		$builder->join('sellopro_articulos','sellopro_articulos.idArticulo = sellopro_detalles_pedido.id_articulo');
		$resultado_lineas = $builder->get()->getResultArray();

		//sacamos los totales 

		//actualizamos el total
		$sum = $db->table('sellopro_detalles_pedido');
		$sum->where('pedido_id',$id);
		$sum->selectSum('total');
		$result = $sum->get()->getResultArray();
		$total_sum = $result[0]['total'];
		$porcenteje = 16;
		$iva = $total_sum*($porcenteje/100);
		$total = $total_sum + $iva;	
			

		$data = [
			'proveedor'=>$resultado,
			'id_pedido'=>$resultado_cotizacion,
			'detalles'=>$resultado_lineas,
			'sub_total'=>$total_sum,
			'iva'=>$iva,
			'total'=>$total,
		];
		//return view('Panel/PDF',$data);
		$doc = new Dompdf();
		$html = view('Panel/PDF_orden',$data);
		//return $html;
		$doc->loadHTML($html);
		$doc->setPaper('A4','portrait');
		$doc->render();
		$doc->stream("OC-".$id.".pdf");
	}
	public function enviar_pdf($id)
	{
		$db = \Config\Database::connect();

		//datos del cliente
		$cliente_query = new CotizacionesModel();
		$cliente_query->where('idQt',$id);
		$resultado_cotizacion = $cliente_query->findAll();

		$cliente = new ClientesModel();
		$cliente->where('idCliente',$resultado_cotizacion[0]['cliente']);
		$resultado = $cliente->findAll();

		//mostrar los articulos
		$builder = $db->table('sellopro_detalles');
		$builder->where('id_cotizacion',$id);
		$builder->join('sellopro_articulos','sellopro_articulos.idArticulo = sellopro_detalles.id_articulo');
		$resultado_lineas = $builder->get()->getResultArray();

		//sacamos los totales 

		//actualizamos el total
		$sum = $db->table('sellopro_detalles');
		$sum->where('id_cotizacion',$id);
		$sum->selectSum('total');
		$result = $sum->get()->getResultArray();
		$total_sum = $result[0]['total'];
		$porcenteje = 16;
		$iva = $total_sum*($porcenteje/100);
		$total = $total_sum + $iva;	
			

		$data = [
			'cliente'=>$resultado,
			'id_cotizacion'=>$resultado_cotizacion,
			'detalles'=>$resultado_lineas,
			'sub_total'=>$total_sum,
			'iva'=>$iva,
			'total'=>$total,
		];
		//return view('Panel/PDF',$data);
		$doc = new Dompdf();
		$html = view('Panel/PDF',$data);
		//return $html;
		$doc->loadHTML($html);
		$doc->setPaper('A4','portrait');
		$doc->render();
		$salida = $doc->output();
		$nombre = "QT-".$id.".pdf";
		$email = \Config\Services::email();
		$email->setFrom('ventas@sellopronto.com.mx','Sello Pronto');
		$email->setTo('reyesabdias@gmail.com');
		$email->setSubject('Cusrsos');
		$email->setMessage('Este es un mensaje de prueba');
		$email->attach('img/40.png');
		$email->send();
	}
	public function pago()
    {
        $request = \Config\Services::request();
        $pedido = $request->getVar('id');
        $monto_total = $request->getVar('monto_total');
        
        // Actualizar el estado de pago en la tabla pedidos
        $pedidosModel = new PedidosModel();
        $pedidosModel->where('id_pedido', $pedido);
        $data['pagado'] = 1;
        $pedidosModel->update($pedido, $data);
        
        // Insertar el gasto en la tabla de gastos
        $gastosModel = new GastosModel();
        $gastoData = [
            'descripcion' => 'Compra de suministros',
            'monto' => $monto_total,
            'fecha_gasto' => date('Y-m-d') // Fecha actual
        ];
        
        $gastosModel->insert($gastoData);
        
        if ($pedidosModel && $gastosModel){
            return $this->response->setJSON([
                'status'=>'error',
                'message'=>'Sa marco esta Orden de Compra como pagada',
                'flag'=>1
            ]);
        }else{
            return $this->response->setJSON([
                'status'=>'error',
                'message'=>'No se hizo la opercion',
                'flag'=>0
            ]);
        }
    }
    
	public function recibida()
    {
        $request = \Config\Services::request();
        
        // Validación del ID de pedido
        $pedidoId = $request->getVar('pedido');
        if(!$pedidoId) {
            return $this->response->setJSON(['error' => 'ID de pedido no proporcionado'])->setStatusCode(400);
        }

        // Inicialización de modelos
        $db = \Config\Database::connect();
        $pedidosModel = new PedidosModel();
        $inventarioModel = new InventarioModel();
        $detallesPedidoModel = new DetallePedidosModel();

        // 1. Verificar que el pedido existe
        $pedido = $pedidosModel->find($pedidoId);
        if (!$pedido){
            return $this->response->setJSON([
                'status'=>'error',
                'message'=>'Pedido no encontrado',
                'flag' => 0
            ]);
        }
        
        // Verificar si ya está recibido
        if($pedido['entregada'] == 1) {
            return $this->response->setJSON([
                'status'=>'error',
                'message'=>'El pedido ya fue marcado como recibido',
                'flag' => 0
            ]);
        }

        // 2. Obtener artículos del pedido
        $articulosPedido = $detallesPedidoModel
            ->select('sellopro_detalles_pedido.id_articulo, sellopro_detalles_pedido.cantidad, sellopro_articulos.precio_prov')
            ->join('sellopro_articulos', 'sellopro_articulos.id_articulo = sellopro_detalles_pedido.id_articulo')
            ->where('id_pedido', $pedidoId)
            ->findAll();

        if(empty($articulosPedido)) {
            return $this->response->setJSON(['error' => 'No se encontraron artículos para este pedido']);
        }

        // 3. Procesar en transacción
        $db->transStart();
        
        try {
            // Actualizar estado del pedido
            $pedidosModel->update($pedidoId, ['entregada' => 1]);
            
           foreach ($articulosPedido as $articulo) {
                $idArticulo = $articulo['id_articulo'];
                $cantidad = $articulo['cantidad'];
                
                // Buscar si ya existe el artículo en inventario
                $existencia = $inventarioModel->where('id_articulo', $idArticulo)->first();
                
                if($existencia) {
                    // Si existe, actualizamos la cantidad sumando la nueva
                    $nuevaCantidad = $existencia['cantidad'] + $cantidad;
                    $inventarioModel->update($existencia['id_entrada'], [
                        'cantidad' => $nuevaCantidad
                    ]);
                } else {
                    // Si no existe, creamos un nuevo registro
                    $inventarioModel->insert([
                        'id_articulo' => $idArticulo,
                        'cantidad' => $cantidad,
                    ]);
                }
            }
            
            $db->transComplete();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pedido marcado como recibido e inventario actualizado',
                'flag' => 1
            ]);
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error al procesar pedido: '.$e->getMessage());
            return $this->response->setJSON(['error' => 'Error al procesar el pedido'])->setStatusCode(500);
        }
    }
}