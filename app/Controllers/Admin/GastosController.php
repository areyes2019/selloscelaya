<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\GastosModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class GastosController extends BaseController
{
    protected $gastoModel;

    public function __construct()
    {
        $this->gastoModel = new GastosModel();
    }

    // Listar todos los gastos
    public function index()
    {
        $data = [
            'title' => 'Gestión de Gastos',
            'gastos' => $this->gastoModel->orderBy('fecha_gasto', 'DESC')->findAll()
        ];

        return view('Panel/gastos', $data);
    }

    // Mostrar formulario de creación
    public function nuevo()
    {
        $data = [
            'title' => 'Registrar Nuevo Gasto'
        ];

        return view('Panel/nuevo_gasto', $data);
    }

    // Guardar nuevo gasto
    public function guardar()
    {
        // Validar datos
        if (!$this->validate($this->gastoModel->validationRules, $this->gastoModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Obtener datos del formulario
        $data = [
            'descripcion' => $this->request->getPost('descripcion'),
            'monto'       => $this->request->getPost('monto'),
            'fecha_gasto' => $this->request->getPost('fecha_gasto')
        ];

        // Insertar en la base de datos
        $this->gastoModel->insert($data);

        return redirect()->to('/gastos/inicio')->with('message', 'Gasto registrado exitosamente');
    }

    // Mostrar detalles de un gasto
   // Mostrar detalles de un gasto
    public function mostrar($id)
    {

        $gasto = $this->gastoModel->find($id);

        if (empty($gasto)) {
            throw new PageNotFoundException('No se encontró el gasto con ID: ' . $id);
        }

        $data = [
            'title' => 'Detalles del Gasto',
            'gasto' => $gasto
        ];

        // Si necesitas devolver JSON en algún caso específico
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($gasto);
        }

        return view('Panel/ver_gasto', $data);
    }

    // Mostrar formulario de edición
    public function editar($id)
    {
        $gasto = $this->gastoModel->find($id);

        if (empty($gasto)) {
            throw new PageNotFoundException('No se encontró el gasto con ID: ' . $id);
        }

        $data = [
            'title' => 'Editar Gasto',
            'gasto' => $gasto
        ];

        return view('Panel/editar_gasto', $data);
    }

    // Actualizar un gasto
    public function actualizar($id)
    {
        // Validar datos
        if (!$this->validate($this->gastoModel->validationRules, $this->gastoModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Obtener datos del formulario
        $data = [
            'descripcion' => $this->request->getPost('descripcion'),
            'monto'       => $this->request->getPost('monto'),
            'fecha_gasto' => $this->request->getPost('fecha_gasto')
        ];

        // Actualizar en la base de datos
        $this->gastoModel->update($id, $data);

        return redirect()->to('/gastos/inicio')->with('message', 'Gasto actualizado exitosamente');
    }

    // Eliminar un gasto
    public function eliminar($id)
    {

        $gasto = $this->gastoModel->find($id);

        if (empty($gasto)) {
            throw new PageNotFoundException('No se encontró el gasto con ID: ' . $id);
        }

        $this->gastoModel->delete($id);

        return redirect()->to('/gastos/inicio')->with('message', 'Gasto eliminado exitosamente');
    }
}