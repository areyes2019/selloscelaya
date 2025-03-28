<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PedidosModel;
use App\Models\ProveedoresModel;
use App\Models\ArticulosModel;
use App\Models\DetallePedidosModel;
use App\Models\InventarioModel;
use Dompdf\Dompdf;
class Compras extends BaseController
{
	public function index()
	{
		$proveedor = new ProveedoresModel();
		$db = \Config\Database::connect();

		$builder = $db->table('sellopro_pedidos');
		$builder->join('sellopro_proveedores','sellopro_proveedores.id_proveedor = sellopro_pedidos.proveedor');
		$resultado = $builder->get()->getResultArray();
		
		$data['proveedor'] = $proveedor->findAll();
		$data['pedidos'] = $resultado;

		return view('Panel/pedidos',$data);

	}
	
	public function pedido($id)
	{
		$query = new PedidosModel();
		$resultado = $query->where('pedidos_id',$id)->findAll();
		return json_encode($resultado);
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
   			'caduca'=>$caduca
   		];
   		$nuevo_registro->insert($data);
   		return redirect()->to(base_url('pagina_orden/'.$slug));
		
	}
	public function pagina($slug)
	{
		//vamos a buscar la cotizacion

		$proveedor = new PedidosModel();
		$proveedor->where('slug',$slug);
		$resultado = $proveedor->findAll();

		$id = $resultado[0]['slug'];
		$pedido = $resultado[0]['pedidos_id'];
	
		//return json_encode($pedido);

		//buscamos los ariticulos
		$articulos = new ArticulosModel();
		
		$db = \Config\Database::connect();
		$builder = $db->table('sellopro_pedidos');
		$builder->where('slug',$id);
		$builder->join('sellopro_proveedores','sellopro_proveedores.id_proveedor = sellopro_pedidos.proveedor');
		$query['proveedor'] = $builder->get()->getResultArray();
		$query['pedidos_id']= $pedido;
		$query['articulo'] = $articulos->findAll();
		$query['pedido'] = $resultado;

		return view('Panel/nueva_compra', $query);
		
	}
	public function agregar()
	{
		$db = \Config\Database::connect();
		$query = new ArticulosModel();
		$model = new DetallePedidosModel();

		$request = \Config\Services::Request();
		$articulo = $request->getvar('id_articulo');
		$cantidad = $request->getvar('cantidad');
		$pedido = $request->getvar('pedidos_id');
		
		//verificamos si el producto ya esta agregado
		$doble = $db->table('sellopro_detalles_pedido');
		$doble->where('pedido_id',$pedido);
		$doble->where('id_articulo',$articulo);
		$es_duplicado = $doble->countAllResults();

		//return json_encode($es_duplicado);
		
		if ($es_duplicado > 0) {
			//esta duplicado
			return "1";
		}else{

			//sacar el precio
			$query->where('idArticulo',$articulo);
			$resultado = $query->findAll();

			$precio = $resultado[0]['precio_prov'];
			$total = $precio * $cantidad;
			
			$data = [
			    'id_articulo' => $request->getvar('id_articulo'),
			    'cantidad' => $request->getvar('cantidad'),
			    'p_unitario'=>$precio,
			    'total'=>$total,
			    'pedido_id'=>$pedido,
			];

			$model->insert($data);

			//actualizamos el total
			$builder = $db->table('sellopro_detalles_pedido');
			$builder->where('pedido_id',$pedido);
			$builder->selectSum('total');
			$sum = $builder->get()->getResultArray();
			$suma_total = $sum[0]['total'];
			$total = new PedidosModel();
			$datos=[
				'total'=>$suma_total,
			];
			$total->update($pedido,$datos);
		}
	}
	public function mostrar_detalles($id)
	{
		//encontrar el articulo completo
		$db = \Config\Database::connect();
		$builder = $db->table('sellopro_detalles_pedido');
		$builder->where('pedido_id',$id);
		$builder->join('sellopro_articulos','sellopro_articulos.idArticulo = sellopro_detalles_pedido.id_articulo');
		$resultado = $builder->get()->getResultArray();

		//mostrar totales
		$total = new PedidosModel();
		$total->where('pedidos_id',$id);
		$suma_total = $total->findAll();
		$porcenteje = 16;
		$monto = $suma_total[0]['total'];
		$iva = $monto*($porcenteje/100);
		$pago_total = $monto+$iva;
		$pago = $suma_total[0]['pagado'];		

		
		$data=[
			'articulo'=>$resultado,
			'sub_total'=> number_format($monto,2),
			'iva'=> $iva,
			'total'=>number_format($pago_total,2),
			'pagado'=>$pago
		];

		return json_encode($data);
		
	}
	public function borrar_linea($id)
	{
		
		$modelo = new DetallePedidosModel();
		//sacamos el numero de cotizacion
		$modelo->where('pedido_detalle_id',$id);
		$ver_modelo = $modelo->findAll();
		$numero = $ver_modelo[0]['pedido_id'];
		$modelo->delete($id);

		$fun = $this->totales($numero);
		return $fun;

	}
	public function totales($numero)
	{
		$db = \Config\Database::connect();
		$builder = $db->table('sellopro_detalles_pedido');
		$builder->where('pedido_id',$numero);
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
		$db = \Config\Database::connect();
		$request = \Config\Services::Request();
		$id = $request->getvar('id');
		$cant = $request->getvar('cantidad');
		
		//sacamos el precio del articulo
		$linea = new DetallePedidosModel();
		$linea->where('pedido_detalle_id',$request->getvar('id'));
		$resultado = $linea->findAll();
		$numero = $resultado[0]['pedido_id'];
		$precio = $resultado[0]['p_unitario'];

		$datos['cantidad'] = $cant;
		$datos['total'] = $precio * $cant;
		$linea->update($id,$datos);

		$fun = $this->totales($numero);
		return $fun;

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
		$proveedor_query->where('pedidos_id',$id);
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
		$request = \Config\Services::Request();
		$pedido = $request->getvar('id');
		$pago = 1;
		$query = new PedidosModel();
		$query->where('pedidos_id',$pedido);
		$data['pagado'] = $pago;
		$query->update($pedido,$data);
		if ($query) {
			return 1;
		}else{
			return 0;
		}
	}
	public function recibida()
	{
		$request = \Config\Services::Request();
		/*$detalles = new DetallePedidosModel();
		$resultado = $detalles->select(['id_articulo','cantidad'])
		->where('pedido_id',$request->getvar('pedido'))
		->findAll();*/

		$db = \Config\Database::connect();
		$builder = $db->table('sellopro_detalles_pedido');
		$builder->where('pedido_id',$request->getvar('pedido'));
		$builder->join('sellopro_articulos','sellopro_articulos.idArticulo = sellopro_detalles_pedido.id_articulo');
		$resultado = $builder->get()->getResultArray();

		$modelo = new InventarioModel();

		foreach ($resultado as $articulo) {
			$existencia = $modelo->obtenerProducto($articulo['id_articulo']);
			if ($existencia) {
				//si el producto ya existe solo incrementa la cantidad
				$modelo->incrementarCantidad($existencia['id_entrada'],$articulo['cantidad'],$articulo['precio_prov']);
			}else{
				
				//si el producto no existe, crea uno nuevo
				//$modelo->save($articulo);

				// Si el producto no existe, crea uno nuevo y calcula el precio total
                $producto['id_articulo'] = $articulo['id_articulo'];
                $producto['cantidad'] = $articulo['cantidad'];
                $producto['total'] = $articulo['precio_prov'] * $articulo['cantidad'];
                unset($articulo['precio_prov']);
                $modelo->save($producto);
			}
		}

		$actualizar_pedido = new PedidosModel();
		$actualizar_pedido->where('pedidos_id',$request->getvar('pedido'))->findAll();
		$recibido['recibido'] = 1;
		$actualizar_pedido->update($request->getvar('pedido'),$recibido);


	}
}