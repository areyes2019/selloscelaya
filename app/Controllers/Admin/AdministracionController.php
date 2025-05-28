<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrdenTrabajoModel;
use App\Models\PedidoModel;
use CodeIgniter\API\ResponseTrait;

class AdministracionController extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new OrdenTrabajoModel();
        $this->pedidoModel = new PedidoModel();
    }

    public function index()
    {
        return view('Panel/administracion');
    }
    public function cargar_ordenes($value='')
    {
        $ordenes = $this->model->findAll();
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
        $estadosPermitidos = ['Dibujo', 'Elaboracion', 'Entrega', 'Entregado'];
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
    // PedidoController.php
    public function pagar($id)
    {
        $pedido = $this->pedidoModel->find($id);

        if (!$pedido) {
            return $this->response->setJSON(['error' => 'Pedido no encontrado'])->setStatusCode(404);
        }

        $this->pedidoModel->update($id, [
            'anticipo' => $pedido['total'],
            'estado' => 'pagado'
        ]);

        return $this->response->setJSON(['message' => 'Pedido marcado como pagado correctamente']);
    }



}