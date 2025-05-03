<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\OrdenTrabajoModel;
use App\Models\CuentasModel;
use App\Models\PedidosModel;
use App\Models\CotizacionesModel;
use App\Models\ClientesModel;
use App\Models\PedidoModel; // Para obtener datos del cliente

class Admin extends BaseController
{
	protected $ordenTrabajoModel;
    protected $pedidoModel;
    protected $cuentasModel;
    protected $cotizacionesModel;

    public function __construct()
    {
        $this->ordenTrabajoModel = new OrdenTrabajoModel();
        $this->pedidoModel = new PedidoModel();
        $this->cuentasModel = new CuentasModel();
        $this->cotizacionesModel = new CotizacionesModel();
        helper(['form', 'url', 'filesystem','text']); // Necesitamos filesystem para manejar archivos
    }
	// En tu controlador

    public function index()
    {
        // Modelo de cuentas bancarias
        $cuentasModel = new CuentasModel();
        $cuentasBancarias = $cuentasModel->findAll();
        
        // Modelo de cotizaciones
        $cotizacionesModel = new CotizacionesModel();
        $cotizacionesModel->select('sellopro_cotizaciones.*, sellopro_clientes.nombre as nombre_cliente');
        $cotizacionesModel->join('sellopro_clientes', 'sellopro_clientes.id_cliente = sellopro_cotizaciones.cliente');
        $cotizacionesModel->orderBy('created_at', 'DESC');
        $ultimasCotizaciones = $cotizacionesModel->findAll(5);
        
        // Modelo de órdenes de trabajo
        $ordenesModel = new OrdenTrabajoModel();
        $ordenesModel->orderBy('created_at', 'DESC');
        $ultimasOrdenes = $ordenesModel->findAll(5);
        
        // Modelo de pedidos (órdenes de compra)
        $pedidosModel = new PedidosModel();
        $pedidosModel->select('sellopro_pedidos.*, sellopro_proveedores.empresa as nombre_proveedor');
        $pedidosModel->join('sellopro_proveedores', 'sellopro_proveedores.id_proveedor = sellopro_pedidos.proveedor', 'left');
        $pedidosModel->orderBy('sellopro_pedidos.created_at', 'DESC');
        $ultimasOrdenesCompra = $pedidosModel->findAll(5);

        $data = [
            'cuentasBancarias' => $cuentasBancarias,
            'ultimasCotizaciones' => $ultimasCotizaciones,
            'ultimasOrdenes' => $ultimasOrdenes,
            'ultimasOrdenesCompra'=>$ultimasOrdenesCompra
        ];
        
        return view('Panel/panel', $data);
    }

}