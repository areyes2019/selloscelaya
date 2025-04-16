<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;
use App\Models\ArticulosModel;
use App\Models\InventarioModel;
use App\Models\BalanceModel;
use App\Models\GastosModel;
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
		return view('Panel/reporte_financiero',$data);
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
	    $db = \Config\Database::connect();

	    // 1. Total ventas brutas del mes
	    $builder = $db->table('pedidos');
	    $builder->selectSum('total');
	    $builder->where("DATE_FORMAT(created_at, '%Y-%m') =", $mes_actual);
	    $query = $builder->get()->getRowArray();

	    // 2. Capital invertido en el mes (versión corregida)
		$builder_cap = $db->table('detalle_pedido');
		$builder_cap->select('SUM(sellopro_articulos.precio_prov * detalle_pedido.cantidad) AS total_capital');
		$builder_cap->join('sellopro_articulos', 'sellopro_articulos.id_articulo = detalle_pedido.id_articulo');
		$builder_cap->join('pedidos', 'pedidos.id = detalle_pedido.pedido_id');
		$builder_cap->where("DATE_FORMAT(pedidos.created_at, '%Y-%m') =", $mes_actual);
		$resultado_cap = $builder_cap->get()->getRowArray();


	    // 3. Total de gastos del mes
	    $builder_gastos = $db->table('sellopro_gastos');
	    $builder_gastos->selectSum('monto');
	    $builder_gastos->where("DATE_FORMAT(fecha_gasto, '%Y-%m') =", $mes_actual);
	    $resultado_gastos = $builder_gastos->get()->getRowArray();

	    // Cálculos financieros
	    $total_bruto = $query['total'] ?? 0;
	    $capital = $resultado_cap['total_capital'] ?? 0;
	    $gastos = $resultado_gastos['monto'] ?? 0;
	    
	    $beneficio_bruto = $total_bruto - $capital;
	    $beneficio_neto = $beneficio_bruto - $gastos;

	    $data = [
	        'total_bruto' => number_format($total_bruto ?? 0, 2),
	        'capital' => number_format($capital?? 0,2),
	        'gastos' => number_format($gastos?? 0,2),
	        'beneficio_bruto' => number_format($beneficio_bruto?? 0,2),
	        'beneficio_neto' => number_format($beneficio_neto?? 0,2),
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