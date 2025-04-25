<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;
use App\Models\ArticulosModel;
use App\Models\InventarioModel;
use App\Models\BalanceModel;
use App\Models\GastosModel;
use App\Models\CuentasModel;
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
	    $ventasModel = new VentasModel();
	    $cuentasModel = new CuentasModel();
	    
	    // Obtener el primer y último día del mes actual como valores por defecto
	    $fechaInicioDefault = date('Y-m-01');
	    $fechaFinDefault = date('Y-m-t');
	    
	    // Obtener fechas del request o usar valores por defecto
	    $fechaInicio = $this->request->getGet('fecha_inicio') ?? $fechaInicioDefault;
	    $fechaFin = $this->request->getGet('fecha_fin') ?? $fechaFinDefault;
	    
	    // Consulta para obtener los totales filtrados por fecha
	    $totales = $ventasModel->select('SUM(total_neto) as ventas_brutas, 
	                                    SUM(inversion) as inversion_total, 
	                                    SUM(beneficio) as beneficio_total')
	                          ->where('created_at >=', $fechaInicio)
	                          ->where('created_at <=', $fechaFin)
	                          ->first();
	    
	    // Obtener cuentas bancarias
	    $cuentas_bancarias = $cuentasModel->findAll();
	    $total_saldos = array_sum(array_column($cuentas_bancarias, 'saldo'));
	    
	    // Preparar los datos para la vista
	    $data = [
	        'ventas_brutas' => $totales['ventas_brutas'] ?? 0,
	        'inversion_total' => $totales['inversion_total'] ?? 0,
	        'beneficio_total' => $totales['beneficio_total'] ?? 0,
	        'fecha_inicio' => $fechaInicio,
	        'fecha_fin' => $fechaFin,
	        'cuentas_bancarias' => $cuentas_bancarias,
	        'total_saldos' => $total_saldos
	    ];
	    
	    return view('Panel/reporte_financiero', $data);
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

	    // === MODELOS ===
	    $ventasModel = new \App\Models\VentasModel();
	    $gastosModel = new \App\Models\GastosModel();
	    $cuentasModel = new \App\Models\CuentasModel();

	    // === VENTAS ===
	    $ventas = $ventasModel->where("DATE_FORMAT(created_at, '%Y-%m') =", $mes_actual)->findAll();
	    $total_ventas = array_sum(array_column($ventas, 'total_neto'));
	    $total_inversion = array_sum(array_column($ventas, 'inversion'));

	    // === GASTOS ===
	    $gastos = $gastosModel->where("DATE_FORMAT(fecha_gasto, '%Y-%m') =", $mes_actual)->findAll();
	    $total_gastos = array_sum(array_column($gastos, 'salida'));

	    // === CUENTAS ===
	    $cuentas = $cuentasModel->findAll();
	    $total_saldos = 0;
	    $detalle_cuentas = [];

	    foreach ($cuentas as $cuenta) {
	        $saldo = floatval($cuenta['saldo']);
	        $total_saldos += $saldo;

	        $detalle_cuentas[] = [
	            'banco' => $cuenta['banco'],
	            'cuenta' => $cuenta['cuenta'],
	            'saldo' => number_format($saldo, 2)
	        ];
	    }

	    // === CÁLCULOS FINALES ===
	    $beneficio_bruto = $total_ventas - $total_inversion;
	    $beneficio_neto = $beneficio_bruto - $total_gastos;

	    $data = [
	        'total_ventas' => number_format($total_ventas, 2),
	        'total_inversion' => number_format($total_inversion, 2),
	        'total_gastos' => number_format($total_gastos, 2),
	        'beneficio_bruto' => number_format($beneficio_bruto, 2),
	        'beneficio_neto' => number_format($beneficio_neto, 2),
	        'total_saldos_bancarios' => number_format($total_saldos, 2),
	        'detalle_cuentas' => $detalle_cuentas,
	        'mes' => $mes_actual
	    ];

	    return $this->response->setJSON($data);
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