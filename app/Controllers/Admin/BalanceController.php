<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;
use App\Models\ArticulosModel;
use App\Models\InventarioModel;
use App\Models\BalanceModel;
use App\Models\GastosModel;
use App\Models\VentasModel;
use CodeIgniter\API\ResponseTrait; // Para respuestas JSON si usas AJAX
use CodeIgniter\Database\Exceptions\DataException;

class BalanceController extends BaseController
{
	use ResponseTrait;
    protected $pedidoModel;
    protected $detallePedidoModel;
    protected $articulosModel;
    protected $inventarioModel;
    protected $balanceModel;
    public function __construct()
    {
        // Puedes cargar helpers, librerías o modelos aquí
        helper(['form', 'url']);
        $this->pedidoModel = new PedidoModel();
        $this->detallePedidoModel = new DetallePedidoModel();
        $this->articulosModel = new ArticulosModel();
        $this->inventarioModel = new InventarioModel();
        $this->balanceModel = new BalanceModel();
    }
	public function index()
	{
		$gastosModel = new GastosModel();
    
	    // Obtener gastos del mes con total
	    $resultados = $gastosModel->getGastosMesActual();
	    
	    // Separar los datos individuales del total
	    $data = [
	        'gastos' => $resultados,
	        'total_mes' => array_reduce($resultados, function($carry, $item) {
	            return $carry + $item['monto'];
	        }, 0),
	        'mes_actual' => date('F Y') // Ejemplo: "July 2023"
	    ];
	    

		//reporte de gastos
		return view('Panel/reporte_financiero');
	}
	public function hoy()
	{
		//Total de ventas brutas

		$hoy = date('Y-m-d');
		$db = \Config\Database::connect();

		$builder = $db->table('pedidos'); // Asegúrate que sea el nombre correcto de la tabla
		$builder->selectSum('total');
		$builder->where('DATE(created_at)', $hoy);
		$query = $builder->get()->getRowArray();

		$builder_cap = $db->table('detalle_pedido');
		$builder_cap->selectSum('sellopro_articulos.precio_prov');
		$builder_cap->join('sellopro_articulos', 'sellopro_articulos.id_articulo = detalle_pedido.id_articulo');
		$builder_cap->join('pedidos', 'pedidos.id = detalle_pedido.pedido_id');
		$builder_cap->where('DATE(pedidos.created_at)', $hoy);
		$resultado_cap = $builder_cap->get()->getRowArray();

		//sacamos los gastos

		$data = [
			'total_bruto'=> $query['total'],
			'capital'=> $resultado_cap['precio_prov'],
			'beneficio'=> $query['total'] - $resultado_cap['precio_prov'],
		];

		return json_encode($data);

	}
	public function mes_actual()
	{
	    $mes_actual = date('Y-m');
	    
	    // Obtener ventas del mes actual
	    $ventasModel = new VentasModel();
	    $ventas = $ventasModel->where("DATE_FORMAT(created_at, '%Y-%m') =", $mes_actual)
	                         ->findAll();
	    
	    // Calcular totales de ventas
	    $total_ventas = 0;
	    $total_inversion = 0;
	    
	    foreach ($ventas as $venta) {
	        $total_ventas += $venta['total_neto'];
	        $total_inversion += $venta['inversion'];
	    }
	    
	    // Obtener gastos del mes actual
	    $gastosModel = new GastosModel();
	    $gastos = $gastosModel->where("DATE_FORMAT(fecha_gasto, '%Y-%m') =", $mes_actual)
	                          ->findAll();
	    
	    // Calcular total de gastos
	    $total_gastos = 0;
	    foreach ($gastos as $gasto) {
	        $total_gastos += $gasto['monto'];
	    }
	    
	    // Calcular beneficios
	    $beneficio_bruto = $total_ventas - $total_inversion;
	    $beneficio_neto = $beneficio_bruto - $total_gastos;
	    
	    // Preparar datos para la respuesta
	    $data = [
	        'total_ventas' => number_format($total_ventas, 2),
	        'total_inversion' => number_format($total_inversion, 2),
	        'total_gastos' => number_format($total_gastos, 2),
	        'beneficio_bruto' => number_format($beneficio_bruto, 2),
	        'beneficio_neto' => number_format($beneficio_neto, 2),
	        'mes' => $mes_actual
	    ];
	    
	    return json_encode($data);
	}
	public function rango()
	{
		$fecha_inicio = $this->request->getPost('fecha_inicio'); // formato: YYYY-MM-DD
		$fecha_fin = $this->request->getPost('fecha_fin');       // formato: YYYY-MM-DD

		$db = \Config\Database::connect();

		// Total ventas en rango
		$builder = $db->table('pedidos');
		$builder->selectSum('total');
		$builder->where('DATE(created_at) >=', $fecha_inicio);
		$builder->where('DATE(created_at) <=', $fecha_fin);
		$query = $builder->get()->getRowArray();

		// Capital invertido en rango
		$builder_cap = $db->table('detalle_pedido');
		$builder_cap->selectSum('sellopro_articulos.precio_prov');
		$builder_cap->join('sellopro_articulos', 'sellopro_articulos.id_articulo = detalle_pedido.id_articulo');
		$builder_cap->join('pedidos', 'pedidos.id = detalle_pedido.pedido_id');
		$builder_cap->where('DATE(pedidos.created_at) >=', $fecha_inicio);
		$builder_cap->where('DATE(pedidos.created_at) <=', $fecha_fin);
		$resultado_cap = $builder_cap->get()->getRowArray();

		$data = [
		    'total_bruto' => $query['total'],
		    'capital' => $resultado_cap['precio_prov'],
		    'beneficio' => $query['total'] - $resultado_cap['precio_prov'],
		];

		return json_encode($data);

	}
}