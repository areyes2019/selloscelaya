<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrdenTrabajoModel;
use App\Models\PedidoModel;
use App\Models\CuentasModel;
use App\Models\GastosModel;
use CodeIgniter\API\ResponseTrait;

class AdministracionController extends BaseController
{
    use ResponseTrait;

    protected $model;
    protected $cuentasModel;

    public function __construct()
    {
        $this->model = new OrdenTrabajoModel();
        $this->pedidoModel = new PedidoModel();
        $this->cuentasModel = new CuentasModel();
    }

    public function index()
    {
        return view('Panel/administracion');
    }
    // Agrega este nuevo método
    public function obtenerCuentas()
    {
        $cuentas = $this->cuentasModel->findAll();
        return $this->respond($cuentas);
    }

    // Modifica el método pagar para incluir la cuenta bancaria
    public function pagar($id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $data = $this->request->getJSON();
            
            if (!isset($data->cuenta_id)) {
                throw new \Exception('Cuenta bancaria no seleccionada');
            }

            $pedidoModel = new PedidoModel();
            $pedido = $pedidoModel->find($id);
            
            if (!$pedido) {
                throw new \Exception('Pedido no encontrado');
            }

            $cuentasModel = new CuentasModel();
            $cuenta = $cuentasModel->find($data->cuenta_id);
            
            if (!$cuenta) {
                throw new \Exception('Cuenta bancaria no encontrada');
            }

            $monto = $pedido['total'] - $pedido['anticipo']; // Calcula el monto pendiente

            // 1. Actualizar el pedido
            $pedidoModel->update($id, [
                'anticipo' => $pedido['total'],
                'estado' => 'pagado',
                'cuenta_id' => $data->cuenta_id
            ]);

            // 2. Actualizar saldo de la cuenta
            $nuevoSaldo = $cuenta['saldo'] + $monto;
            $cuentasModel->update($data->cuenta_id, ['saldo' => $nuevoSaldo]);

            // 3. Registrar movimiento (asumiendo que tienes un modelo GastosModel)
            $gastosModel = new GastosModel();
            $gastosModel->insert([
                'descripcion' => 'Pago completo del pedido no. ' . $id,
                'entrada' => $monto,
                'salida' => 0,
                'cuenta_origen' => 0, // 0 podría significar "cliente" o "efectivo"
                'cuenta_destino' => $data->cuenta_id,
                'fecha_gasto' => date('Y-m-d H:i:s'),
                'pedido_id' => $id // Asumiendo que tu tabla gastos tiene este campo
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error en la transacción de base de datos');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pedido marcado como pagado correctamente',
                'nuevo_saldo' => $nuevoSaldo,
                'cuenta_id' => $data->cuenta_id
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    public function cargar_ordenes($value='')
    {
        $db = \Config\Database::connect();
        $builder = $db->table('sellopro_ordenes_trabajo ot');
        $builder->select('ot.*, p.total, p.estado as estado_pedido, p.anticipo');
        $builder->join('pedidos p', 'p.id = ot.pedido_id', 'left');
        $ordenes = $builder->get()->getResult();
        
        return json_encode($ordenes);
    }
    public function actualizarEstado($id)
    {
        // Verificar si el ID existe
        $orden = $this->model->find($id);
        if(!$orden) {
            return $this->failNotFound("Orden no encontrada");
        }

        // Validar los datos recibidos
        $data = $this->request->getJSON();
        if(!isset($data->status)) {
            return $this->fail("Estado no proporcionado");
        }

        // Lista de estados permitidos
        $estadosPermitidos = ['Dibujo', 'Elaboracion', 'Entrega', 'Facturacion','Entregado', 'Facturado'];
        if(!in_array($data->status, $estadosPermitidos)) {
            return $this->fail("Estado no válido");
        }

        // Actualizar
        if ($this->model->update($id, ['status' => $data->status])) {
            return $this->respond([
                'success' => true,
                'message' => 'Estado actualizado',
                'data' => $this->model->find($id) // Devuelve los datos actualizados
            ]);
        }
        
        return $this->failServerError('Error en la base de datos');
    }
    public function eliminar($id)
    {
        $orden = $this->model->find($id);
        if (!$orden) {
            return $this->failNotFound("Orden no encontrada");
        }

        // Verificar si el pedido está pagado
        $pedidoModel = new PedidoModel();
        $pedido = $pedidoModel->find($orden->pedido_id);

        if (!$pedido) {
            return $this->failNotFound("Pedido asociado no encontrado");
        }

        if (strtolower($pedido['estado']) !== 'pagado') {
            return $this->fail("No se puede eliminar la orden: el pedido no está pagado");
        }

        // Eliminar la orden
        if ($this->model->delete($id)) {
            return $this->respond([
                'success' => true,
                'message' => 'Orden eliminada correctamente'
            ]);
        }

        return $this->failServerError("Error al eliminar la orden");
    }


}