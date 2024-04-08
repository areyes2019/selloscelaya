<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\CotizacionesModel;
use App\Models\ClientesModel;

class Cotizaciones extends BaseController
{
	public function index()
	{
		//return view('Panel/cotizaciones');
		$model = new CotizacionesModel();
		$cliente = new ClientesModel();
		$data['articulos'] = $model->findAll();
		$data['clientes']  = $cliente->findAll();
		return view('Panel/cotizaciones', $data);
	}
	public function nueva()
	{
		return view('Panel/nueva_cotizacion');
	}
	public function editar()
	{
		return view('Panel/editar_cotizacion');
	}
}