<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;

class Ventas extends BaseController
{
	public function index()
	{
		return view('Panel/clientes');
	}
	public function nueva()
	{
		return view('Panel/nueva_venta');
	}
}