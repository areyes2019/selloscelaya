<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\CotizacionesModel;
use App\Models\ClientesModel;
use App\Models\ArticulosModel;
use App\Models\InventarioModel;
use App\Models\CuentasModel;
use App\Models\GastosModel;
use App\Models\DetalleModel;
use App\Models\VentasModel;
use Dompdf\Dompdf;
class Cotizaciones extends BaseController
{
	public function index()
	{
		$db = \Config\Database::connect();

		$builder = $db->table('sellopro_cotizaciones');
		$builder->join('sellopro_clientes','sellopro_clientes.id_cliente = sellopro_cotizaciones.cliente');
		$resultado = $builder->get()->getResultArray();

		//return view('Panel/cotizaciones');
		$cliente = new ClientesModel();
		$data['cotizaciones'] = $resultado;
		$data['clientes']  = $cliente->findAll();
		return view('Panel/cotizaciones', $data);
	}
	public function nueva($id)
	{
	    // Validar el tipo de venta
	    $tipoVenta = $this->request->getGet('tipo_venta') ?? '1'; // Default a Sello completo
	    
	    // Generar slug único
	    $caracteres_permitidos = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $longitud = 12;
	    $slug = substr(str_shuffle($caracteres_permitidos), 0, $longitud);
	    
	    // Insertar nuevo registro
	    $nuevo_registro = new CotizacionesModel();
	    $data = [
	        'slug' => $slug,
	        'cliente' => $id,
	        'tipo_venta' => ($tipoVenta == '1') ? 1 : 2 // Ajusta según tu ENUM
	    ];
	    
	    $nuevo_registro->insert($data);
	    return redirect()->to(base_url('pagina_cotizador/'.$slug));
	}
	public function pagina($slug)
	{
		$cotizacionesModel = new CotizacionesModel();
	    $cotizacion = $cotizacionesModel->where('slug', $slug)->findAll();

	    $ClientesModel = new ClientesModel();
	    $cliente = $ClientesModel->where('id_cliente', $cotizacion[0]['cliente'])->findAll();
	    
	    // Obtenemos la lista de bancos
	    $cuentasModel = new CuentasModel();
	    $bancos = $cuentasModel->findAll();
	    // Ahora pasamos un array asociativo a la vista
	    return view('Panel/nueva_cotizacion', [
	        'data'    => $cotizacion,
	        'cliente' => $cliente,
	        'bancos'  => $bancos
	    ]);
		
	}
	public function editar()
	{
		return view('Panel/editar_cotizacion');
	}
	public function agregar()
	{
	    // Conexión y modelos
	    $db = \Config\Database::connect();
	    $query = new ArticulosModel();
	    $model = new DetalleModel();
	    $cot = new CotizacionesModel();
	    $request = \Config\Services::request();

	    // Datos del request
	    $articulo = $request->getVar('id_articulo');
	    $cantidad = $request->getVar('cantidad');
	    $cotizacion = $request->getVar('id_cotizacion');

	    // Obtener información de la cotización para saber el tipo de venta
	    $cotizacionData = $cot->find($cotizacion);
	    if (!$cotizacionData) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Cotización no encontrada',
	            'flag' => 0
	        ]);
	    }

	    $tipoVenta = $cotizacionData['tipo_venta'] ?? 1; // Por defecto 1 si no está definido

	    // Verificar si el producto ya está agregado
	    $existe = $model->where('id_cotizacion', $cotizacion)
	                   ->where('id_articulo', $articulo)
	                   ->countAllResults() > 0;
	    if ($existe) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Este artículo ya está agregado',
	            'flag' => 0
	        ]);
	    }

	    // Obtener el artículo
	    if (!empty($articulo)) {
	        $query_articulo = $query->where('id_articulo', $articulo)->first();
	    }

	    if (!$query_articulo) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Artículo no encontrado',
	            'flag' => 0
	        ]);
	    }

	    // Seleccionar el precio según el tipo de venta
	    $precioBase = ($tipoVenta == 2) ? $query_articulo['precio_dist'] : $query_articulo['precio_pub'];
	    
	    // Cálculos de precios
	    $precioConDescuento = $precioBase / 1.16; // Precio sin IVA (16%)
	    $subtotal = $precioConDescuento * $cantidad;
	    $iva = $subtotal * 0.16; // Calculamos el IVA (16%)
	    $total = $subtotal + $iva;

	    // Datos para insertar en detalles
	    $data = [
	        'cantidad' => $cantidad,
	        'id_articulo' => $query_articulo['id_articulo'],
	        'p_unitario' => $precioConDescuento,
	        'total' => $subtotal, // Guardamos el subtotal (sin IVA) en detalles
	        'id_cotizacion' => $cotizacion,
	        'descripcion' => $query_articulo['nombre'] . " " . $query_articulo['modelo']
	    ];

	    // Validar campos vacíos
	    foreach ($data as $key => $value) {
	        if (empty($value)) {
	            return $this->response->setJSON([
	                'status' => 'error',
	                'message' => "El campo '$key' no puede estar vacío",
	                'flag' => 0
	            ]);
	        }
	    }

	    $db->transStart();
	    try {
	        // Insertar en detalles
	        $model->insert($data);

	        // Calcular sumatorias de todos los artículos de la cotización
	        $detalles = $model->where('id_cotizacion', $cotizacion)->findAll();
	        
	        $sumaSubtotal = 0;
	        foreach ($detalles as $detalle) {
	            $sumaSubtotal += $detalle['total'];
	        }
	        
	        $sumaIva = $sumaSubtotal * 0.16;
	        $sumaTotal = $sumaSubtotal + $sumaIva;

	        // Actualizar la cotización con los totales
	        $datosActualizar = [
	            'subtotal' => $sumaSubtotal,
	            'iva' => $sumaIva,
	            'total' => $sumaTotal,
	        ];

	        $cot->update($cotizacion, $datosActualizar);

	        $db->transComplete();

	        return $this->response->setJSON([
	            'status' => 'success',
	            'message' => 'Datos guardados y tabla actualizada',
	            'flag' => 1,
	            'data' => $datosActualizar // Opcional: devolver los totales calculados
	        ]);

	    } catch (\Exception $e) {
	        $db->transRollback();
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Error al guardar: ' . $e->getMessage(),
	            'flag' => 0
	        ]);
	    }
	}
	public function modificar_cantidad()
	{
	    $db = \Config\Database::connect();
	    $request = \Config\Services::request();
	    $detalleModel = new DetalleModel();
	    $cotizacionModel = new CotizacionesModel();
	    $articuloModel = new ArticulosModel();

	    $id = $request->getVar('id');
	    $cantidad = (int)$request->getVar('cantidad');

	    try {
	        $db->transStart();

	        // 1. Validar y obtener el detalle
	        $detalle = $detalleModel->find($id);
	        if (!$detalle) {
	            throw new \Exception('Detalle no encontrado');
	        }

	        // 2. Validar y obtener el artículo
	        $articulo = $articuloModel->find($detalle['id_articulo']);
	        if (!$articulo) {
	            throw new \Exception('Artículo no encontrado');
	        }

	        // 3. Calcular nuevos valores para el detalle
	        $nuevoTotal = $detalle['p_unitario'] * $cantidad;
	        $nuevaInversion = $articulo['precio_prov'] * $cantidad;

	        // 4. Actualizar el detalle
	        $detalleModel->update($id, [
	            'cantidad' => $cantidad,
	            'total' => $nuevoTotal,
	            'inversion' => $nuevaInversion
	        ]);

	        // 5. Obtener todos los detalles para recalcular
	        $detalles = $detalleModel->where('id_cotizacion', $detalle['id_cotizacion'])->findAll();
	        $sumaSubtotal = array_sum(array_column($detalles, 'total'));

	        // 6. Obtener descuento actual
	        $cotizacion = $cotizacionModel->find($detalle['id_cotizacion']);
	        $descuento = (float)$cotizacion['descuento'];

	        // 7. Calcular nuevos totales con descuento
	        $subtotalConDescuento = $sumaSubtotal - $descuento;
	        $iva = $subtotalConDescuento * 0.16;
	        $total = $subtotalConDescuento + $iva;

	        // 8. Actualizar la cotización
	        $cotizacionModel->update($detalle['id_cotizacion'], [
	            'subtotal' => $sumaSubtotal,
	            'iva' => $iva,
	            'total' => $total
	            // Mantiene el descuento existente
	        ]);

	        $db->transComplete();

	        return $this->response->setJSON([
	            'status' => 'success',
	            'message' => 'Cantidad actualizada y totales recalculados',
	            'data' => [
	                'linea' => [
	                    'id' => $id,
	                    'nuevo_total' => number_format($nuevoTotal, 2)
	                ],
	                'cotizacion' => [
	                    'subtotal' => number_format($sumaSubtotal, 2),
	                    'descuento' => number_format($descuento, 2),
	                    'iva' => number_format($iva, 2),
	                    'total' => number_format($total, 2)
	                ]
	            ]
	        ]);

	    } catch (\Exception $e) {
	        $db->transRollback();
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Error: ' . $e->getMessage()
	        ]);
	    }
	}
	public function agregar_ind()
	{
		$model = new DetalleModel();
		$request = \Config\Services::Request();
		$db = \Config\Database::connect();

		$data['descripcion'] = $request->getvar('descripcion');
		$data['id_articulo'] = 0;
		$data['cantidad'] = $request->getvar('cantidad');
		$data['p_unitario'] = $request->getvar('p_unitario');
		$data['id_cotizacion'] = $request->getvar('id_cotizacion');
		$data['total'] = $request->getvar('p_unitario') * $request->getvar('cantidad');

		//return json_encode($data);
		$model->insert($data);

		//actualizamos el total
		$builder = $db->table('sellopro_detalles');
		$builder->where('id_cotizacion',$request->getvar('id_cotizacion'));
		$builder->selectSum('total');
		$sum = $builder->get()->getResultArray();
		$suma_total = $sum[0]['total'];
		$total = new CotizacionesModel();
		$datos=[
			'total'=>$suma_total,
		];
		$total->update($request->getvar('id_cotizacion'),$datos);

	}
	public function calcularTotales($id_cotizacion)
	{
	    // 1. Cargar el modelo
	    $cotizacionModel = new CotizacionesModel();
	    
	    // 2. Obtener los datos usando el modelo
	    $cotizacion = $cotizacionModel->find($id_cotizacion);
	    
	    // 3. Verificar si existe
	    if (!$cotizacion) {
	        return [
	            'status' => 'error',
	            'message' => 'Cotización no encontrada'
	        ];
	    }

	    // 4. Calcular el saldo (total - anticipo)
	    $total = (float)$cotizacion['total'];
	    $anticipo = (float)$cotizacion['anticipo'];
	    $saldo = $total - $anticipo;

	    // 5. Preparar los datos de respuesta
	    $response = [
	        'status' => 'success',
	        'data' => [
	            'cotizacion' => $cotizacion,
	            'totales' => [
	                'total' => number_format($total, 2),
	                'anticipo' => number_format($anticipo, 2),
	                'saldo' => number_format($saldo, 2),
	                'saldo_sin_formato' => $saldo // Para posibles cálculos
	            ]
	        ]
	    ];
	    
	    return json_encode($response);
	}
	public function mostrar_detalles($id)
	{
		//encontrar el articulo completo
		$db = \Config\Database::connect();
		$builder = $db->table('sellopro_detalles');
		$builder->where('id_cotizacion',$id);
		$builder->join('sellopro_articulos','sellopro_articulos.id_articulo = sellopro_detalles.id_articulo');
		$resultado = $builder->get()->getResultArray();

		/*$data={
			'cantidad'=>$resultado[0][''],
			'nombre'=>$resultado[0][''],
			'modelo'=>$resultado[0][''],
			'p_unitario'=>$resultado[0][''],
			'descripcion'=>$resultado[0][''],
		}*/

		return json_encode($resultado);
		
	}
	public function descuento()
	{
	    $request = \Config\Services::request();
	    $cotizacionModel = new CotizacionesModel();
	    $detalleModel = new DetalleModel(); // Asumiendo que tienes este modelo

	    $id_cotizacion = $request->getVar('id_cotizacion');
	    $porcentajeDescuento = $request->getVar('descuento');

	    // 1. Obtener el subtotal base (suma de todos los artículos sin descuento)
	    $detalles = $detalleModel->where('id_cotizacion', $id_cotizacion)->findAll();
	    $subtotalBase = array_sum(array_column($detalles, 'total'));

	    // 2. Calcular el descuento en monto
	    $descuentoMonto = $subtotalBase * ($porcentajeDescuento / 100);

	    // 3. Calcular nuevos valores
	    $nuevoSubtotal = $subtotalBase - $descuentoMonto;
	    $iva = $nuevoSubtotal * 0.16; // IVA del 16%
	    $nuevoTotal = $nuevoSubtotal + $iva;

	    // 4. Preparar datos para actualizar
	    $data = [
	        'subtotal' => $nuevoSubtotal,
	        'descuento' => $descuentoMonto,
	        'iva' => $iva,
	        'total' => $nuevoTotal
	    ];

	    // 5. Actualizar la cotización
	    $update = $cotizacionModel->update($id_cotizacion, $data);

	    if ($update) {
	        return $this->response->setJSON([
	            'status' => 'success',
	            'message' => 'Descuento aplicado y totales actualizados',
	            'flag' => 1,
	            'data' => [
	                'subtotal' => number_format($nuevoSubtotal, 2),
	                'descuento' => number_format($descuentoMonto, 2),
	                'iva' => number_format($iva, 2),
	                'total' => number_format($nuevoTotal, 2)
	            ]
	        ]);
	    }

	    return $this->response->setJSON([
	        'status' => 'error',
	        'message' => 'Error al aplicar el descuento',
	        'flag' => 0
	    ]);
	}
	public function descuento_dinero()
	{
	    $request = \Config\Services::request();
	    $cotizacionModel = new CotizacionesModel();
	    $detalleModel = new DetalleModel();

	    $id_cotizacion = $request->getVar('id_cotizacion');
	    $descuento_fijo = (float)$request->getVar('descuento');

	    // 1. Obtener el subtotal base (suma de todos los artículos sin descuento)
	    $detalles = $detalleModel->where('id_cotizacion', $id_cotizacion)->findAll();
	    $subtotalBase = array_sum(array_column($detalles, 'total'));

	    // 2. Obtener datos actuales de la cotización
	    $cotizacionActual = $cotizacionModel->find($id_cotizacion);
	    
	    if (!$cotizacionActual) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Cotización no encontrada',
	            'flag' => 0
	        ]);
	    }

	    $descuento_actual = (float)$cotizacionActual['descuento'];

	    // 3. Eliminar descuento si se envía 0
	    if ($descuento_fijo == 0) {
	        $iva = $subtotalBase * 0.16;
	        $total = $subtotalBase + $iva;

	        $data = [
	            'subtotal' => $subtotalBase,
	            'descuento' => 0,
	            'iva' => $iva,
	            'total' => $total
	        ];
	        
	        $update = $cotizacionModel->update($id_cotizacion, $data);
	        
	        return $this->response->setJSON([
	            'status' => 'success',
	            'message' => 'Descuento eliminado',
	            'flag' => 1,
	            'data' => $data
	        ]);
	    }

	    // 4. Calcular nuevo descuento (sumar al existente)
	    $nuevo_descuento = $descuento_actual + $descuento_fijo;

	    // 5. Validar que el descuento no exceda el subtotal
	    if ($nuevo_descuento > $subtotalBase) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'El descuento no puede ser mayor al subtotal ('.number_format($subtotalBase, 2).')',
	            'flag' => 0,
	            'maximo_permitido' => $subtotalBase - $descuento_actual
	        ]);
	    }

	    // 6. Calcular nuevos valores con descuento
	    $nuevoSubtotal = $subtotalBase - $nuevo_descuento;
	    $iva = $nuevoSubtotal * 0.16;
	    $nuevoTotal = $nuevoSubtotal + $iva;

	    // 7. Actualizar todos los campos
	    $data = [
	        'subtotal' => $nuevoSubtotal,
	        'descuento' => $nuevo_descuento,
	        'iva' => $iva,
	        'total' => $nuevoTotal
	    ];
	    
	    $update = $cotizacionModel->update($id_cotizacion, $data);
	    
	    if ($update) {
	        return $this->response->setJSON([
	            'status' => 'success',
	            'message' => 'Descuento aplicado correctamente',
	            'flag' => 1,
	            'data' => [
	                'subtotal' => number_format($nuevoSubtotal, 2),
	                'descuento' => number_format($nuevo_descuento, 2),
	                'iva' => number_format($iva, 2),
	                'total' => number_format($nuevoTotal, 2),
	                'descuento_agregado' => number_format($descuento_fijo, 2)
	            ]
	        ]);
	    }
	    
	    return $this->response->setJSON([
	        'status' => 'error',
	        'message' => 'Error al aplicar el descuento',
	        'flag' => 0
	    ]);
	}
	public function borrar_linea($id)
	{
	    $db = \Config\Database::connect();
	    $modelo = new DetalleModel();
	    $cotizacionModel = new CotizacionesModel();

	    try {
	        $db->transStart(); // Iniciar transacción

	        // 1. Obtener el registro a eliminar
	        $linea = $modelo->find($id);
	        
	        if (!$linea) {
	            return $this->response->setJSON([
	                'status' => 'error',
	                'message' => 'El registro no existe',
	                'flag' => 0
	            ]);
	        }

	        $id_cotizacion = $linea['id_cotizacion'];

	        // 2. Eliminar el registro
	        $modelo->delete($id);

	        // 3. Calcular nuevos totales desde los detalles restantes
	        $detalles = $modelo->where('id_cotizacion', $id_cotizacion)->findAll();
	        
	        // 4. Calcular sumatorias
	        $sumaSubtotal = array_sum(array_column($detalles, 'total'));
	        $descuento = 0; // Se mantiene el descuento existente (o 0 si no hay artículos)
	        
	        // Si no quedan artículos, limpiar todo
	        if (empty($detalles)) {
	            $datosActualizar = [
	                'subtotal' => 0,
	                'descuento' => 0,
	                'iva' => 0,
	                'total' => 0,
	                'anticipo' => 0
	            ];
	        } else {
	            // Calcular IVA y total con descuento existente
	            $subtotalConDescuento = $sumaSubtotal - (float)$cotizacionModel->find($id_cotizacion)['descuento'];
	            $iva = $subtotalConDescuento * 0.16;
	            $total = $subtotalConDescuento + $iva;
	            
	            $datosActualizar = [
	                'subtotal' => $sumaSubtotal,
	                'iva' => $iva,
	                'total' => $total
	                // Mantiene el descuento existente
	            ];
	        }

	        // 5. Actualizar la cotización
	        $cotizacionModel->update($id_cotizacion, $datosActualizar);

	        $db->transComplete(); // Confirmar transacción

	        return $this->response->setJSON([
	            'status' => 'success',
	            'message' => 'Línea eliminada y totales actualizados',
	            'data' => [
	                'subtotal' => number_format($datosActualizar['subtotal'], 2),
	                'descuento' => number_format($datosActualizar['descuento'] ?? 0, 2),
	                'iva' => number_format($datosActualizar['iva'], 2),
	                'total' => number_format($datosActualizar['total'], 2),
	                'articulos_restantes' => count($detalles)
	            ],
	            'flag' => 1
	        ]);

	    } catch (\Exception $e) {
	        $db->transRollback(); // Revertir en caso de error
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Error al eliminar: ' . $e->getMessage(),
	            'flag' => 0
	        ]);
	    }
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
	
	//Esta funcion es para descargar PDF
	public function cotizacion_pdf($id) 
	{
	    $db = \Config\Database::connect();
	    
	    // Obtener cotización
	    $cotizacionesModel = new CotizacionesModel();
	    $cotizacion = $cotizacionesModel->where('id_cotizacion', $id)->first();

	    if (!$cotizacion) {
	        throw new \CodeIgniter\Exceptions\PageNotFoundException("Cotización no encontrada.");
	    }

	    // Obtener cliente
	    $clientesModel = new ClientesModel();
	    $cliente = $clientesModel->find($cotizacion['cliente']);

	    if (!$cliente) {
	        throw new \CodeIgniter\Exceptions\PageNotFoundException("Cliente no encontrado.");
	    }

	    // Obtener los artículos de la cotización
	    $detalles = $db->table('sellopro_detalles')
	        ->where('id_cotizacion', $id)
	        ->join('sellopro_articulos', 'sellopro_articulos.id_articulo = sellopro_detalles.id_articulo')
	        ->get()
	        ->getResultArray();

	    // Cálculos de totales - VERSIÓN CORREGIDA
	    // 1. Calcular subtotal sumando todos los productos (sin IVA)
	    $subtotal_productos = array_reduce($detalles, function($carry, $item) {
	        return $carry + ($item['p_unitario'] * $item['cantidad']);
	    }, 0);

	    // 2. Aplicar descuento (si existe)
	    $descuento = (float)$cotizacion['descuento'];
	    $subtotal_con_descuento = $subtotal_productos - $descuento;

	    // 3. Calcular IVA (16% del subtotal con descuento)
	    $iva = $subtotal_con_descuento * 0.16;

	    // 4. Calcular total (subtotal con descuento + IVA)
	    $total = $subtotal_con_descuento + $iva;

	    // 5. Calcular saldo (total - anticipo)
	    $anticipo = (float)$cotizacion['anticipo'];
	    $saldo = $total - $anticipo;

	    // Preparar datos para la vista PDF
	    $data = [
	        'cliente' => $cliente,
	        'cot' => $cotizacion,
	        'id_cotizacion' => $cotizacion['id_cotizacion'],
	        'detalles' => $detalles,
	        'sub_total' => number_format($subtotal_productos, 2),        // Subtotal sin descuento
	        'sub_total_con_descuento' => number_format($subtotal_con_descuento, 2), // Subtotal con descuento
	        'descuento' => number_format($descuento, 2),
	        'iva' => number_format($iva, 2),
	        'total' => number_format($total, 2),
	        'pagado' => $cotizacion['pago'] == 1,
	        'anticipo' => number_format($anticipo, 2),
	        'saldo' => number_format($saldo, 2),
	    ];

	    // Generar PDF
	    $dompdf = new \Dompdf\Dompdf();
	    
	    // Opciones de Dompdf
	    $options = $dompdf->getOptions();
	    $options->set('isRemoteEnabled', true);
	    $dompdf->setOptions($options);
	    
	    $html = view('Panel/PDF', $data);
	    $dompdf->loadHtml($html);
	    $dompdf->setPaper('A4', 'portrait');
	    $dompdf->render();
	    
	    // Forzar descarga del PDF
	    $dompdf->stream("QT-{$id}.pdf", [
	        "Attachment" => true
	    ]);
	}
	public function enviar_pdf($id)
	{
		$db = \Config\Database::connect();
		$cliente_query = new CotizacionesModel();
		$email = \Config\Services::email();

		//datos del cliente
		$cliente_query->where('id_cotizacion',$id);
		$resultado_cotizacion = $cliente_query->findAll();

		$cliente = new ClientesModel();
		$cliente->where('id_cliente',$resultado_cotizacion[0]['cliente']);
		$resultado = $cliente->findAll();

		//mostrar los articulos
		$builder = $db->table('sellopro_detalles');
		$builder->where('id_cotizacion',$id);
		$builder->join('sellopro_articulos','sellopro_articulos.id_articulo = sellopro_detalles.id_articulo');
		$resultado_lineas = $builder->get()->getResultArray();

		//mostrar independientes
		//Mostrar articulos independientes
		$query = new DetalleModel();
		$query->where('id_cotizacion',$id);
		$query->where('id_articulo',0);
		$independiente = $query->findAll();

		//sacamos los totales 
		$total = (float)$resultado_cotizacion[0]['total'];
		$descuento = (float)$resultado_cotizacion[0]['descuento'];
		$anticipo = (float)$resultado_cotizacion[0]['anticipo'];

		$totalConDescuento = $total - $descuento;
		$sub_total = $totalConDescuento / 1.16;
		$iva = $totalConDescuento - $sub_total;
		$saldo = $totalConDescuento - $anticipo;

		$data = [
		    'cliente' => $resultado,
		    'id_cotizacion' => $resultado_cotizacion,
		    'detalles' => $resultado_lineas,
		    'sub_total' => number_format($sub_total, 2),
		    'descuento' => number_format($descuento, 2),
		    'iva' => number_format($iva, 2),
		    'total' => number_format($totalConDescuento, 2),
		];

		//return view('Panel/PDF',$data);
		$doc = new Dompdf();
		$html = view('Panel/PDF',$data);
		//return $html;
		$doc->loadHTML($html);
		$doc->setPaper('A4','portrait');
		$doc->render();
		$nombre = "QT-".$id.".pdf";
		$rutaTemporal = WRITEPATH.'uploads/temp/'.$nombre;
		// Crear directorio si no existe
	    if (!is_dir(dirname($rutaTemporal))) {
	        mkdir(dirname($rutaTemporal), 0777, true);
	    }
	    file_put_contents($rutaTemporal, $doc->output());

		$email = \Config\Services::email();
		$email->setFrom('ventas@sellopronto.com.mx','Sello Pronto');
		$email->setTo($resultado[0]['correo']);
		$email->setSubject('Su cotizacion '.$nombre);

		$imagePath = FCPATH . '/public/img/logo2.png'; // Ruta absoluta a la imagen
    	$email->attach($imagePath, 'inline'); // 'inline' para incrustar
    	$cid = $email->setAttachmentCID($imagePath); // Genera el CID

    	// 2. Pasar el CID a la vista del correo
	    $dataEmail = [
	        'id' => $id,
	        'cid_logo' => $cid // Pasamos el CID a la vista
	    ];

		$email->setMessage(view('Emails/cotizacion', $dataEmail));
		$email->attach($rutaTemporal);
		if ($email->send()) {
			// code...
			unlink($rutaTemporal);
        	return redirect()->to('/cotizaciones');

		}else{
			// Opcional: guardar el error en logs
	        echo 'Error: ' . $email->printDebugger();
		}
	}
	public function pago()
	{
	    $request = \Config\Services::request();
	    $cotizacionModel = new CotizacionesModel();
	    $gastosModel = new GastosModel();
	    $cuentasModel = new CuentasModel();
	    
	    // Validar datos de entrada
	    $id_cotizacion = $request->getVar('id');
	    $monto = (float)$request->getVar('pago');
	    $id_banco = $request->getVar('id_banco'); // Nuevo: ID del banco seleccionado

	    // Verificar monto válido
	    if ($monto <= 0) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'El monto debe ser mayor a cero',
	            'flag' => 0
	        ]);
	    }

	    // Verificar que se haya seleccionado un banco
	    if (empty($id_banco)) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Debe seleccionar un banco',
	            'flag' => 0
	        ]);
	    }

	    try {
	        // Obtener la cotización actual
	        $cotizacion = $cotizacionModel->find($id_cotizacion);
	        
	        if (!$cotizacion) {
	            return $this->response->setJSON([
	                'status' => 'error',
	                'message' => 'Cotización no encontrada',
	                'flag' => 0
	            ]);
	        }

	        // Verificar si ya existe anticipo
	        if ((float)$cotizacion['anticipo'] > 0) {
	            return $this->response->setJSON([
	                'status' => 'error',
	                'message' => 'Ya existe un anticipo registrado',
	                'flag' => 0,
	                'anticipo_actual' => $cotizacion['anticipo']
	            ]);
	        }

	        // Obtener información de la cuenta bancaria
	        $cuenta = $cuentasModel->find($id_banco);
	        if (!$cuenta) {
	            return $this->response->setJSON([
	                'status' => 'error',
	                'message' => 'Cuenta bancaria no encontrada',
	                'flag' => 0
	            ]);
	        }

	        // Iniciar transacción para asegurar la integridad de los datos
	        $db = \Config\Database::connect();
	        $db->transStart();

	        // 1. Actualizar el anticipo en la cotización
	        $updateCotizacion = $cotizacionModel->update($id_cotizacion, [
	            'anticipo' => $monto
	        ]);

	        // 2. Registrar el movimiento en GastosModel (como entrada)
	        $gastosModel->insert([
	            'descripcion' => 'Pago de anticipo de cotización No. '.$id_cotizacion,
	            'entrada' => $monto,
	            'salida' => 0,
	            'cuenta_origen' => $id_banco,
	            'cuenta_destino' => null, // O puedes poner otra cuenta si aplica
	            'fecha_gasto' => date('Y-m-d H:i:s')
	        ]);

	        // 3. Actualizar el saldo en la cuenta bancaria
	        $nuevoSaldo = $cuenta['saldo'] + $monto;
	        $cuentasModel->update($id_banco, [
	            'saldo' => $nuevoSaldo
	        ]);

	        $db->transComplete();

	        if ($db->transStatus() === false) {
	            return $this->response->setJSON([
	                'status' => 'error',
	                'message' => 'Error en la transacción',
	                'flag' => 0
	            ]);
	        }

	        return $this->response->setJSON([
	            'status' => 'success',
	            'message' => 'Anticipo registrado correctamente',
	            'flag' => 1,
	            'data' => [
	                'anticipo' => number_format($monto, 2),
	                'total' => $cotizacion['total'],
	                'nuevo_saldo' => number_format($nuevoSaldo, 2),
	                'banco' => $cuenta['banco']
	            ]
	        ]);

	    } catch (\Exception $e) {
	        // Si hay error, hacer rollback automático (si se usa transacción)
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Error: ' . $e->getMessage(),
	            'flag' => 0
	        ]);
	    }
	}
	public function pago_total()
	{
	    $request = \Config\Services::request();
	    $id = $request->getVar('id');
	    $id_banco = $request->getVar('banco'); // Nuevo: ID del banco seleccionado

	    $cotizacionesModel = new CotizacionesModel();
	    $detalleModel = new DetalleModel();
	    $articulosModel = new ArticulosModel();
	    $ventasModel = new VentasModel();
	    $gastosModel = new GastosModel(); // Nuevo: Modelo para registrar movimientos
	    $cuentasModel = new CuentasModel(); // Nuevo: Modelo para cuentas bancarias

	    $cotizacion = $cotizacionesModel->find($id);

	    if (!$cotizacion) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Cotización no encontrada.'
	        ])->setStatusCode(404);
	    }

	    // Validar que se haya seleccionado un banco
	    if (empty($id_banco)) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Debe seleccionar un banco para registrar el pago'
	        ]);
	    }

	    // Obtener información de la cuenta bancaria
	    $cuenta = $cuentasModel->find($id_banco);
	    if (!$cuenta) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Cuenta bancaria no encontrada'
	        ]);
	    }

	    $referencia = "QT-" . $id;

	    // Validar si ya existe una venta con esta referencia
	    $ventaExistente = $ventasModel->where('ref', $referencia)->first();
	    if ($ventaExistente) {
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => "Esta cotización ya ha sido pagada previamente. Referencia: $referencia"
	        ]);
	    }

	    // Obtener valores de la cotización
	    $total = floatval($cotizacion['total']);
	    $anticipo = floatval($cotizacion['anticipo']);
	    $saldo_pagado = $total - $anticipo; // Calculamos el saldo que se está pagando ahora

	    // Calcular inversión total (suma de precios de proveedor)
	    $detalles = $detalleModel->where('id_cotizacion', $id)->findAll();
	    $inversionTotal = 0;
	    
	    foreach ($detalles as $detalle) {
	        $articulo = $articulosModel->find($detalle['id_articulo']);
	        $precioProv = $articulo ? $articulo['precio_prov'] : 0;
	        $inversionTotal += $detalle['cantidad'] * $precioProv;
	    }

	    // Calcular beneficio (total_neto - inversion)
	    $beneficio = $total - $inversionTotal;

	    // Iniciar transacción para asegurar la integridad de los datos
	    $db = \Config\Database::connect();
	    $db->transStart();

	    try {
	        // 1. Insertar en la tabla ventas
	        $ventasModel->insert([
	            'ref' => $referencia,
	            'total_neto' => $total,
	            'inversion' => $inversionTotal,
	            'beneficio' => $beneficio
	        ]);

	        // 2. Marcar como pagado (1 = pagado)
	        $cotizacionesModel->update($id, ['pago' => 1]);

	        // 3. Registrar el movimiento en GastosModel (como entrada)
	        $gastosModel->insert([
	            'descripcion' => 'Pago del saldo de la cotización QT-' . $id,
	            'entrada' => $saldo_pagado, // Registramos solo el saldo pagado (total - anticipo)
	            'salida' => 0,
	            'cuenta_origen' => $id_banco,
	            'cuenta_destino' => null,
	            'fecha_gasto' => date('Y-m-d H:i:s')
	        ]);

	        // 4. Actualizar el saldo en la cuenta bancaria
	        $nuevoSaldo = $cuenta['saldo'] + $saldo_pagado;
	        $cuentasModel->update($id_banco, [
	            'saldo' => $nuevoSaldo
	        ]);

	        $db->transComplete();

	        if ($db->transStatus() === false) {
	            return $this->response->setJSON([
	                'status' => 'error',
	                'message' => 'Error en la transacción',
	                'flag' => 0
	            ]);
	        }

	        return $this->response->setJSON([
	            'status' => 'success',
	            'message' => 'Pago total registrado y venta creada exitosamente',
	            'flag' => 1,
	            'data' => [
	                'referencia' => $referencia,
	                'total_neto' => $total,
	                'inversion' => $inversionTotal,
	                'beneficio' => $beneficio,
	                'saldo_pagado' => $saldo_pagado,
	                'nuevo_saldo_cuenta' => $nuevoSaldo
	            ]
	        ]);

	    } catch (\Exception $e) {
	        $db->transRollback();
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Error: ' . $e->getMessage(),
	            'flag' => 0
	        ]);
	    }
	}

	public function descontar_inventario()
	{
	    $idCotizacion = $this->request->getVar('id');

	    $detalleModel = new DetalleModel();
	    $inventarioModel = new InventarioModel();
	    $articulosModel = new ArticulosModel();
	    $cotizacionesModel = new CotizacionesModel(); // Modelo añadido

	    $detalles = $detalleModel->where('id_cotizacion', $idCotizacion)->findAll();

	    // Primero: Validar disponibilidad de todos los artículos
	    foreach ($detalles as $detalle) {
	        $idArticulo = $detalle['id_articulo'];
	        $cantidadNecesaria = $detalle['cantidad'];

	        // Obtener stock actual
	        $stockActual = $inventarioModel
	            ->where('id_articulo', $idArticulo)
	            ->selectSum('cantidad')
	            ->first()['cantidad'] ?? 0;

	        if ($stockActual < $cantidadNecesaria) {
	            $articulo = $articulosModel->find($idArticulo);
	            $nombreArticulo = $articulo ? $articulo['nombre'] : 'Artículo #' . $idArticulo;

	            return $this->response->setJSON([
	                'status' => 'error',
	                'message' => "No hay suficiente inventario para: $nombreArticulo. Stock disponible: $stockActual, requerido: $cantidadNecesaria"
	            ]);
	        }
	    }

	    // Segundo: Descontar del inventario dentro de una transacción
	    $db = \Config\Database::connect();
	    $db->transStart();

	    try {
	        // Actualizar cantidades del inventario
			foreach ($detalles as $detalle) {
			    $idArticulo = $detalle['id_articulo'];
			    $cantidadRestar = $detalle['cantidad'];

			    $inventario = $inventarioModel->where('id_articulo', $idArticulo)->first();

			    if ($inventario) {
			        $nuevaCantidad = $inventario['cantidad'] - $cantidadRestar;

			        $inventarioModel->update($inventario['id_entrada'], [
			            'cantidad' => $nuevaCantidad
			        ]);
			    } else {
			        return $this->response->setJSON([
			            'status' => 'error',
			            'message' => 'No se encontró inventario para el artículo ID: ' . $idArticulo
			        ]);
			    }
			}
			$data =['entregada'=> 1];
			$update = $cotizacionesModel->update($idCotizacion,$data);
			if (!$update){
			    return $this->response->setJSON([
			        'status'=>'error',
			        'message'=>'No se actualizo',
			        'flag'=>1
			    ]);
			}
	        $db->transComplete();

	        return $this->response->setJSON(['status' => 'ok']);
	    } catch (\Exception $e) {
	        $db->transRollback();
	        return $this->response->setJSON([
	            'status' => 'error',
	            'message' => 'Error al descontar inventario: ' . $e->getMessage()
	        ]);
	    }
	}

	public function entregado()
	{
		$request = \Config\Services::Request();
		$request->getvar('id');

		//vamos a descontar los valores del inventario
		$query_detalles = new DetalleModel();
		$query_detalles->where('id_cotizacion',$request->getvar('id'));
		$resultado = $query_detalles->findAll();

		return json_encode($resultado);

	}
}