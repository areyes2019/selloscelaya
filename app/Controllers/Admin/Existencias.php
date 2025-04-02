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
	    
	    // Obtener la lista de artículos con sus cantidades en inventario
	    $builder = $db->table('sellopro_inventario');
	    $builder->select('sellopro_inventario.id_articulo, sellopro_inventario.cantidad, sellopro_articulos.*');
	    $builder->join('sellopro_articulos', 'sellopro_articulos.id_articulo = sellopro_inventario.id_articulo');
	    $resultado = $builder->get()->getResultArray();

	    // Calcular el valor total del inventario (precio público * cantidad)
	    $query = $db->table('sellopro_inventario');
	    $query->join('sellopro_articulos', 'sellopro_articulos.id_articulo = sellopro_inventario.id_articulo');
	    $query->selectSum('(precio_pub * cantidad)', 'total_sum');
	    $resful = $query->get()->getResultArray();

	    $data = [
	        'lista' => $resultado,
	        'super_total' => $resful[0]['total_sum'] ?? 0 // Usamos el operador null coalescente por si no hay resultados
	    ];

	    return view('Panel/existencias', $data);
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