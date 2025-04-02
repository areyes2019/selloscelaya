<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\OrdenTrabajoModel;
use App\Models\PedidoModel; // Para obtener datos del cliente

class Admin extends BaseController
{
	protected $ordenTrabajoModel;
    protected $pedidoModel;

    public function __construct()
    {
        $this->ordenTrabajoModel = new OrdenTrabajoModel();
        $this->pedidoModel = new PedidoModel();
        helper(['form', 'url', 'filesystem','text']); // Necesitamos filesystem para manejar archivos
    }
	public function index()
	{
		$data['title'] = 'Dashboard Órdenes de Trabajo';
        $data['ordenesPorStatus'] = $this->ordenTrabajoModel->getOrdenesPorStatus();

        // Pasamos los nombres de los status para las pestañas
        $data['statuses'] = ['Diseño', 'Elaboracion', 'Entrega'];

		return view('Panel/panel',$data);
	}
}