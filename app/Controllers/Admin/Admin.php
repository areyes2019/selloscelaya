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
	// En tu controlador

    public function index()
    {
        $ordenModel = new OrdenTrabajoModel();

        $ordenes = $ordenModel
            ->select('sellopro_ordenes_trabajo.id_ot, sellopro_ordenes_trabajo.cliente_nombre, sellopro_ordenes_trabajo.cliente_telefono, sellopro_ordenes_trabajo.status, pedidos.total, pedidos.anticipo')
            ->join('pedidos', 'pedidos.id = sellopro_ordenes_trabajo.pedido_id')
            ->findAll();

        foreach ($ordenes as $orden) {
            $orden->clave = substr($orden->cliente_telefono, -4);

            if (isset($orden->total) && isset($orden->anticipo)) {
                if ($orden->total == $orden->anticipo) {
                    $orden->saldo = 'Pagado';
                } else {
                    $orden->saldo = number_format($orden->total - $orden->anticipo, 2);
                }
            } else {
                $orden->saldo = 'Desconocido';
            }
        }

        usort($ordenes, function ($a, $b) {
            if ($a->saldo === 'Pagado' && $b->saldo !== 'Pagado') {
                return 1;
            } elseif ($a->saldo !== 'Pagado' && $b->saldo === 'Pagado') {
                return -1;
            } else {
                return 0;
            }
        });

        return view('Panel/panel', ['ordenes' => $ordenes]);
    }





}