<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PedidosModel;
use App\Models\ProveedoresModel;
use App\Models\ArticulosModel;
use App\Models\DetallePedidosModel;
use App\Models\GastosModel;
use App\Models\InventarioModel;
use App\Models\CuentasModel;
use Dompdf\Dompdf;
use CodeIgniter\API\ResponseTrait;
class Compras extends BaseController
{
	use ResponseTrait;
   	public function index()
	{
	    $db = \Config\Database::connect();
	    $proveedor = new ProveedoresModel();

	    // Obtener el primer día del mes pasado
	    $primerDiaMesPasado = date('Y-m-01', strtotime('first day of last month'));

	    // Obtener el último día del mes actual
	    $ultimoDiaMesActual = date('Y-m-t');

	    $builder = $db->table('sellopro_pedidos');
	    $builder->select('sellopro_pedidos.*, sellopro_proveedores.empresa, sellopro_proveedores.telefono as telefono_proveedor');
	    $builder->join('sellopro_proveedores', 'sellopro_proveedores.id_proveedor = sellopro_pedidos.proveedor');

	    // Filtrar desde el primer día del mes pasado hasta el último día del mes actual
	    $builder->where('sellopro_pedidos.created_at >=', $primerDiaMesPasado);
	    $builder->where('sellopro_pedidos.created_at <=', $ultimoDiaMesActual . ' 23:59:59');

	    // Ordenar por fecha descendente
	    $builder->orderBy('sellopro_pedidos.created_at', 'DESC');

	    $pedidos = $builder->get()->getResultArray();

	    $data['proveedor'] = $proveedor->findAll();
	    $data['pedidos'] = $pedidos;
	    $data['mes_actual'] = date('F Y', strtotime('first day of last month')) . ' - ' . date('F Y');

	    return view('Panel/compras', $data);
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
	    $cuentas = new CuentasModel(); // Instanciamos el modelo de cuentas

	    // Buscamos el pedido
	    $pedido->where('slug', $slug);
	    $resultado = $pedido->findAll();

	    // Buscamos al proveedor
	    $proveedor->where('id_proveedor', $resultado[0]['proveedor']);
	    $resultado_proveedor = $proveedor->findAll();

	    // Buscamos los articulos para el modal
	    $modal = $articulos->findAll();

	    // Obtenemos todas las cuentas bancarias
	    $cuentas_bancarias = $cuentas->findAll();

	    // Lineas de detalle
	    $builder = $db->table('sellopro_detalles_pedido');
	    $builder->where('id_detalle_pedido', $resultado[0]['id_pedido']);
	    $builder->join('sellopro_articulos', 'sellopro_articulos.id_articulo = sellopro_detalles_pedido.id_articulo');
	    $detalles = $builder->get()->getResultArray();

	    $data = [
	        'pedido'          => $resultado,    
	        'proveedor'       => $resultado_proveedor, 
	        'articulos'       => $modal,
	        'detalles'        => $detalles,
	        'pedidos_id'      => $resultado[0]['id_pedido'],
	        'suma_total'      => $resultado[0]['total'],
	        'cuentas_bancarias' => $cuentas_bancarias // Agregamos las cuentas bancarias
	    ];
	    
	    return view('Panel/nueva_compra', $data);
	}
    public function agregar()
	{
		$db = \Config\Database::connect();
		$query = new ArticulosModel();
		$model = new DetallePedidosModel();

		$request = \Config\Services::request();
		
		$articulo = $request->getVar('id_articulo');
		$pedido = (int)$request->getVar('pedidos_id');
		$cantidad = (int)$request->getVar('cantidad') ?: 1;
		
		if ($cantidad <= 0) {
			return "2";
		}

		// Verificar duplicado
		$doble = $db->table('sellopro_detalles_pedido');
		$doble->where('id_pedido', $pedido);
		$doble->where('id_articulo', $articulo);
		if ($doble->countAllResults() > 0) {
			return "1";
		}

		// Obtener artículo
		$articuloData = $query->find($articulo);
		if (!$articuloData) {
			return "3";
		}

		// 🔥 PRECIO YA LIMPIO (SIN IVA)
		$precio = (float)$articuloData['precio_prov'];

		// Calcular total
		$total = $precio * $cantidad;

		// Guardar
		$data = [
			'id_articulo' => $articulo,
			'p_unitario'  => round($precio, 2),
			'cantidad'    => $cantidad,
			'total'       => round($total, 2),
			'id_pedido'   => $pedido,
		];

		if (!$model->insert($data)) {
			return "4";
		}

		// Recalcular total del pedido
		$builder = $db->table('sellopro_detalles_pedido');
		$builder->where('id_pedido', $pedido);
		$builder->selectSum('total');
		$sum = $builder->get()->getRow();

		$suma_total = $sum->total ?? 0;

		$totalModel = new PedidosModel();
		$totalModel->update($pedido, ['total' => $suma_total]);

		return "0";
	}
	public function mostrar_detalles($id)
	{
		$db = \Config\Database::connect();

		// 🔥 Detalles
		$builder = $db->table('sellopro_detalles_pedido');
		$builder->where('id_pedido', $id);
		$builder->join(
			'sellopro_articulos',
			'sellopro_articulos.id_articulo = sellopro_detalles_pedido.id_articulo'
		);
		$resultado = $builder->get()->getResultArray();

		// 🔥 Pedido
		$pedidoModel = new PedidosModel();
		$pedido = $pedidoModel
			->where('id_pedido', $id)
			->first();

		if (!$pedido) {
			return json_encode(['error' => 'Pedido no encontrado']);
		}

		// 🔥 TOTAL BASE (SIEMPRE SIN IVA)
		$sub_total = (float)$pedido['total'];

		$porcentaje = 16;

		// 🔥 SIEMPRE AGREGAR IVA
		$iva = $sub_total * ($porcentaje / 100);
		$total = $sub_total + $iva;

		return json_encode([
			'articulo' => $resultado,
			'sub_total' => number_format($sub_total, 2),
			'iva' => number_format($iva, 2),
			'total' => number_format($total, 2),
		]);
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

		$modelo = new PedidosModel();
		$modelo->delete($id);

		$builder = $db->table('sellopro_detalles');
		$builder->where('id_cotizacion',$id);
		$builder->delete();

		return redirect()->to('/compras');

	}
	public function cotizacion_pdf($id)
	{
		$db = \Config\Database::connect();

		// 🔥 Pedido
		$pedidoModel = new PedidosModel();
		$pedido = $pedidoModel
			->where('id_pedido', $id)
			->first();

		if (!$pedido) {
			return;
		}

		// 🔥 Proveedor
		$proveedorModel = new ProveedoresModel();
		$proveedor = $proveedorModel->find($pedido['proveedor']);

		// 🔥 Detalles
		$builder = $db->table('sellopro_detalles_pedido');
		$builder->where('id_pedido', $id);
		$builder->join(
			'sellopro_articulos',
			'sellopro_articulos.id_articulo = sellopro_detalles_pedido.id_articulo'
		);
		$detalles = $builder->get()->getResultArray();

		// 🔥 Subtotal (SIEMPRE sin IVA)
		$sub_total = (float)$pedido['total'];

		// 🔥 IVA
		$porcentaje = 16;
		$iva = $sub_total * ($porcentaje / 100);

		// 🔥 Total
		$total = $sub_total + $iva;

		$data = [
			'proveedor' => [$proveedor],
			'id_pedido' => [$pedido],
			'detalles'  => $detalles,
			'sub_total' => $sub_total,
			'iva'       => $iva,
			'total'     => $total,
		];

		$doc = new Dompdf();
		$html = view('Panel/PDF_orden', $data);

		$doc->loadHTML($html);
		$doc->setPaper('A4', 'portrait');
		$doc->render();
		$doc->stream("OC-".$id.".pdf");
	}
	public function enviar_pdf($id)
	{

	    $db = \Config\Database::connect();
	    $email = \Config\Services::email();

	    // Obtener datos del proveedor
	    $proveedor_query = new PedidosModel();
	    $proveedor_query->where('id_pedido', $id);
	    $resultado_cotizacion = $proveedor_query->findAll();

	    $proveedor = new ProveedoresModel();
	    $proveedor->where('id_proveedor', $resultado_cotizacion[0]['proveedor']);
	    $resultado = $proveedor->findAll();

	    // Obtener artículos
	    $builder = $db->table('sellopro_detalles_pedido');
	    $builder->where('id_pedido', $id);
	    $builder->join('sellopro_articulos', 'sellopro_articulos.id_articulo = sellopro_detalles_pedido.id_articulo');
	    $resultado_lineas = $builder->get()->getResultArray();

	    // Calcular totales
	    $sum = $db->table('sellopro_detalles_pedido');
	    $sum->where('id_pedido', $id);
	    $sum->selectSum('total');
	    $result = $sum->get()->getResultArray();
	    $total_sum = $result[0]['total'];
	    $porcenteje = 16;
	    $iva = $total_sum * ($porcenteje / 100);
	    $total = $total_sum + $iva;

	    // Datos para el PDF
	    $data_pdf = [
	        'proveedor' => $resultado,
	        'id_pedido' => $resultado_cotizacion,
	        'detalles' => $resultado_lineas,
	        'sub_total' => $total_sum,
	        'iva' => $iva,
	        'total' => $total,
	    ];

	    // Generar PDF
	    $doc = new Dompdf();
	    $html_pdf = view('Panel/PDF_orden', $data_pdf);
	    $doc->loadHTML($html_pdf);
	    $doc->setPaper('A4', 'portrait');
	    $doc->render();
	    $pdf_content = $doc->output();
	    // Configurar el correo electrónico
	    $email_destino = $resultado[0]['correo'];
	    
	    // Ruta absoluta al logo
	    $logo_path = ROOTPATH . '/public/img/logo2.png';
	    
	    // Generar un CID único para la imagen
	    $cid = 'logo_' . uniqid();
	    
	    // Leer la imagen y codificarla en base64
	    $logo_data = base64_encode(file_get_contents($logo_path));
	    $logo_mime = mime_content_type($logo_path); // ej. 'image/png'
	    
	    // Datos para la plantilla de correo
	    $data_email = [
	        'id' => $id,
	        'cid_logo' => $cid,
	        'logo_data' => $logo_data,
	        'logo_mime' => $logo_mime
	    ];
	    
	    // Renderizar la plantilla de correo
	    $html_email = view('Emails/oc', $data_email);
	    
	    // Configurar y enviar el correo
	    $email->setFrom('ventas@sellopronto.com.mx', 'Sello Pronto');
	    $email->setTo($email_destino);
	    $email->setSubject('Orden de Compra #' . $id);
	    $email->setMessage($html_email);
	    $email->setMailType('html');
	    
	    // Adjuntar PDF
	    $email->attach($pdf_content, 'attachment', 'OC-' . $id . '.pdf', 'application/pdf');
	    
	    // Enviar el correo
	    if ($email->send()) {
	        return redirect()->to(base_url('/compras?alert_type=success&alert_message=' . urlencode('Correo con orden de compra enviado correctamente al proveedor')));
	    } else {
	        return redirect()->to(base_url('/compras?alert_type=danger&alert_message=' . urlencode('Error al enviar el correo: ' . $email->printDebugger())));
	    }
    
	}
	public function pago()
	{
	    $request = \Config\Services::request();
	    $pedidoId = $request->getVar('id');
	    $bancoId = $request->getVar('banco');

	    // Cargar modelos
	    $pedidosModel = new \App\Models\PedidosModel();
	    $gastosModel = new \App\Models\GastosModel();
	    $detalleModel = new \App\Models\DetallePedidosModel();
	    $cuentasModel = new \App\Models\CuentasModel();
	    $db = \Config\Database::connect();

	    // 1. Obtener el pedido y calcular el monto total
	    $pedido = $pedidosModel->find($pedidoId);
	    if (!$pedido) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'El pedido no existe'
	        ]);
	    }

	    // Calcular monto total del pedido
	    $query = $db->table('sellopro_detalles_pedido d')
	        ->select('SUM(d.cantidad * d.p_unitario) as monto_total')
	        ->join('sellopro_articulos a', 'a.id_articulo = d.id_articulo')
	        ->where('d.id_pedido', $pedidoId)
	        ->get();

	    $resultado = $query->getRow();
	    $montoTotal = $resultado->monto_total ?? 0;

	    if ($montoTotal <= 0) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'El pedido no tiene un monto válido para pagar'
	        ]);
	    }

	    // 2. Verificar fondos en la cuenta bancaria
	    $cuenta = $cuentasModel->find($bancoId);
	    
	    if (!$cuenta) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'La cuenta bancaria seleccionada no existe'
	        ]);
	    }

	    if ($cuenta['saldo'] < $montoTotal) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Fondos insuficientes en la cuenta seleccionada. Saldo disponible: $'.number_format($cuenta['saldo'], 2)
	        ]);
	    }

	    // Iniciar transacción
	    $db->transStart();

	    try {
	        // 3. Registrar el gasto principal del pago
	        $gastoPagoData = [
	            'descripcion' => 'Pago de la OC '.$pedidoId,
	            'entrada' => 0,
	            'salida' => $montoTotal,
	            'cuenta_origen' => $bancoId,
	            'cuenta_destino' => 0,
	            'fecha_gasto' => date('Y-m-d')
	        ];
	        $gastosModel->insert($gastoPagoData);

	        // 4. Registrar gastos adicionales (si aplica)
	        $queryGastos = $db->table('sellopro_detalles_pedido d')
	            ->select('d.cantidad, d.p_unitario')
	            ->join('sellopro_articulos a', 'a.id_articulo = d.id_articulo')
	            ->where('d.id_pedido', $pedidoId)
	            ->where('a.venta', 0)
	            ->get();

	        $detalles = $queryGastos->getResultArray();
	        
	        $montoGastos = 0;
	        $hayGastos = false;

	        if (!empty($detalles)) {
	            foreach ($detalles as $detalle) {
	                $montoGastos += $detalle['cantidad'] * $detalle['p_unitario'];
	            }

	            $gastoData = [
	                'descripcion' => 'Compra de suministros de la OC No. '.$pedidoId,
	                'entrada' => 0,
	                'salida' => $montoGastos,
	                'cuenta_origen' => $bancoId,
	                'cuenta_destino' => 0,
	                'fecha_gasto' => date('Y-m-d')
	            ];
	            $gastosModel->insert($gastoData);
	            $hayGastos = true;
	        }

	        // 5. Actualizar saldo de la cuenta
	        $nuevoSaldo = $cuenta['saldo'] - $montoTotal;
	        $cuentasModel->update($bancoId, ['saldo' => $nuevoSaldo]);

	        // 6. Marcar pedido como pagado
	        $pedidosModel->update($pedidoId, [
	            'pagado' => 1,
	            'cuenta_pago' => $bancoId,
	            'fecha_pago' => date('Y-m-d H:i:s'),
	            'monto_pagado' => $montoTotal // Guardar el monto pagado
	        ]);

	        $db->transComplete();

	        // Respuesta
	        if ($hayGastos) {
	            return $this->response->setJSON([
	                'status' => 'ok',
	                'message' => 'Orden de compra pagada correctamente con registro de gastos. Nuevo saldo: $'.number_format($nuevoSaldo, 2),
	                'monto_total' => $montoTotal,
	                'flag' => 1
	            ]);
	        } else {
	            return $this->response->setJSON([
	                'status' => 'ok',
	                'message' => 'Orden de compra pagada correctamente. Nuevo saldo: $'.number_format($nuevoSaldo, 2),
	                'monto_total' => $montoTotal,
	                'flag' => 2
	            ]);
	        }
	    } catch (\Exception $e) {
	        $db->transRollback();
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Error al procesar el pago: '.$e->getMessage()
	        ]);
	    }
	}
	public function recibida()
	{
		$request = \Config\Services::request();
		
		$pedidoId = $request->getJSON()->pedido?? null;

		if(!$pedidoId) {
			return $this->handleResponse([
				'status' => 'error',
				'message' => 'ID de pedido no proporcionado',
				'flag' => 0
			], 400);
		}

		$db = \Config\Database::connect();
		$pedidosModel = new \App\Models\PedidosModel();
		$detallesPedidoModel = new \App\Models\DetallePedidosModel();
		$inventarioService = new \App\Services\InventarioService();

		// 🔥 VALIDAR PEDIDO
		$pedido = $pedidosModel->find($pedidoId);

		if (!$pedido){
			return $this->handleResponse([
				'status' => 'error',
				'message' => 'Pedido no encontrado',
				'flag' => 0
			], 404);
		}

		// 🚨 VALIDACIÓN CLAVE
		if($pedido['pagado'] != 1){
			return $this->handleResponse([
				'status' => 'error',
				'message' => 'No puedes recibir una orden que no ha sido pagada',
				'flag' => 0
			], 400);
		}

		if($pedido['entregada'] == 1){
			return $this->handleResponse([
				'status' => 'error',
				'message' => 'El pedido ya fue recibido anteriormente',
				'flag' => 0
			], 400);
		}

		// 🔍 OBTENER ARTÍCULOS
		$articulosPedido = $detallesPedidoModel
			->where('id_pedido', $pedidoId)
			->findAll();

		if(empty($articulosPedido)) {
			return $this->handleResponse([
				'status' => 'error',
				'message' => 'No hay artículos en este pedido',
				'flag' => 0
			], 400);
		}

		$db->transStart();

		try {

			// 🔥 1. MARCAR COMO RECIBIDO
			$pedidosModel->update($pedidoId, [
				'entregada' => 1
			]);

			// 🔥 2. ENTRAR A INVENTARIO (USANDO TU SERVICE NUEVO)
			foreach ($articulosPedido as $item) {

				$inventarioService->entrada(
					$item['id_articulo'],
					$item['cantidad'],
					$pedidoId,
					'COMPRA OC #' . $pedidoId
				);
			}

			$db->transComplete();

			return $this->handleResponse([
				'status' => 'success',
				'message' => 'Pedido recibido e inventario actualizado correctamente',
				'flag' => 1
			], 200);

		} catch (\Exception $e) {

			$db->transRollback();

			return $this->handleResponse([
				'status' => 'error',
				'message' => 'Error al procesar: '.$e->getMessage(),
				'flag' => 0
			], 500);
		}
	}

	/**
	 * Maneja la respuesta según si es AJAX o no
	 */
	private function handleResponse($data, $statusCode)
	{
	    $request = \Config\Services::request();
	    
	    if ($request->isAJAX()) {
	        return $this->response->setJSON($data)->setStatusCode($statusCode);
	    } else {
	        // Para formularios tradicionales, redirigir con mensaje flash
	        $session = session();
	        if ($data['status'] === 'success') {
	            $session->setFlashdata('alert_type', 'success');
	        } else {
	            $session->setFlashdata('alert_type', 'danger');
	        }
	        $session->setFlashdata('alert_message', $data['message']);
	        
	        return redirect()->to(base_url('compras'));
	    }
	}
   	public function select($id)
	{
	    $modelo = new ArticulosModel();
	    $builder = $modelo->builder();
	    
	    $builder->select('*')
	            ->select("CONCAT(nombre, ' (', modelo, ')') as nombre_completo", false);
	    
	    // Aplicar filtro solo si se proporciona un ID válido
	    if ($id !== null && $id !== '') {
	        $builder->where('proveedor', $id);
	    }
	    
	    $query = $builder->get();
	    
	    return json_encode($query->getResult());
	}
}