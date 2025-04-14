<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;
use App\Models\ArticulosModel;
use App\Models\InventarioModel;
use App\Models\BalanceModel;
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
		$db = \Config\Database::connect();

		// Total ventas brutas del mes
		$builder = $db->table('pedidos');
		$builder->selectSum('total');
		$builder->where("DATE_FORMAT(created_at, '%Y-%m') =", $mes_actual);
		$query = $builder->get()->getRowArray();

		// Capital invertido en el mes
		$builder_cap = $db->table('detalle_pedido');
		$builder_cap->selectSum('sellopro_articulos.precio_prov');
		$builder_cap->join('sellopro_articulos', 'sellopro_articulos.id_articulo = detalle_pedido.id_articulo');
		$builder_cap->join('pedidos', 'pedidos.id = detalle_pedido.pedido_id');
		$builder_cap->where("DATE_FORMAT(pedidos.created_at, '%Y-%m') =", $mes_actual);
		$resultado_cap = $builder_cap->get()->getRowArray();

		$data = [
		    'total_bruto' => $query['total'],
		    'capital' => $resultado_cap['precio_prov'],
		    'beneficio' => $query['total'] - $resultado_cap['precio_prov'],
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