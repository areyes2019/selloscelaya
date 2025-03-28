<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\InventarioModel;
use App\Models\ArticulosModel;
use Dompdf\Dompdf;

class Existencias extends BaseController
{
	public function index()
	{
		$db = \Config\Database::connect();
		$builder = $db->table('sellopro_inventario');
		$builder->join('sellopro_articulos','sellopro_articulos.idArticulo = sellopro_inventario.id_articulo');
		$resultado = $builder->get()->getResultArray();

		//mostamos el valor total de invecion
		$builder->selectSum('total');
		$base_res = $builder->get()->getResultArray(); 
		$master = $resultado;
		//mostramos el valor total de inventario

		$query = $db->table('sellopro_inventario');
		$query->join('sellopro_articulos','sellopro_articulos.idArticulo = sellopro_inventario.id_articulo');
		$query->selectSum('(precio_pub * cantidad)', 'total_sum');
		$resful = $query->get()->getResultArray();

		$data =[
			'lista'=>$resultado,
			'neto'=>$base_res[0]['total'],
			'super_total'=>$resful[0]['total_sum'] 
		];

		return view('Panel/existencias',$data);
	}
	public function nuevo()
	{
		// code...
	}
	public function editar()
	{
		// code...
	}
	public function actualizar()
	{
		// code...
	}
	public function eliminar()
	{
		// code...
	}
	
}