<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\CuentasModel;

class CuentasController extends BaseController
{
    protected $cuentaModel;

    public function __construct()
    {
        $this->cuentaModel = new CuentasModel();
    }

    public function index()
    {
        $cuentas = $this->cuentaModel->findAll();
        $data = [
            'titulo' => 'Listado de Cuentas',
            'cuentas' => $cuentas,
        ];
        return view('Panel/listado_cuentas_bancarias', $data);
    }

    public function nuevo()
    {
        $data = [
            'titulo' => 'Nueva Cuenta',
        ];
        return view('Panel/nueva_cuenta_bancaria', $data);
    }

    public function guardar()
    {
        $rules = [
            'banco' => 'required|max_length[255]',
            'cuenta' => 'required|max_length[50]',
            'saldo' => 'required|decimal',
        ];
        $messages = [
            'cuenta' => [
                'required' => 'El número de cuenta es obligatorio',
                'max_length' => 'El número de cuenta no puede exceder los 50 caracteres'
            ],
            // Puedes agregar mensajes para los otros campos si lo deseas
        ];

        if ($this->validate($rules)) {
            $data = [
                'banco' => $this->request->getPost('banco'),
                'cuenta' => $this->request->getPost('cuenta'),
                'saldo' => $this->request->getPost('saldo'),
            ];
            $this->cuentaModel->insert($data);
            return redirect()->to(base_url('cuentas'))->with('mensaje', 'Cuenta guardada correctamente.');
        } else {
            $data = [
                'titulo' => 'Nueva Cuenta',
                'validation' => $this->validator,
            ];
            return view('Panel/nueva_cuenta_bancaria', $data);
        }
    }

    public function editar($id)
    {
        $cuenta = $this->cuentaModel->find($id);
        if ($cuenta) {
            $data = [
                'titulo' => 'Editar Cuenta',
                'cuenta' => $cuenta,
            ];
            return view('Panel/editar_cuenta_bancaria', $data);
        } else {
            return redirect()->to(base_url('cuentas'))->with('error', 'Cuenta no encontrada.');
        }
    }

    public function actualizar($id)
    {
        $rules = [
            'banco' => 'required|max_length[255]',
            'cuenta' => 'required|max_length[50]',
            'saldo' => 'required|decimal',
        ];

        if ($this->validate($rules)) {
            $data = [
                'banco' => $this->request->getPost('banco'),
                'cuenta' => $this->request->getPost('cuenta'),
                'saldo' => $this->request->getPost('saldo'),
            ];
            $this->cuentaModel->update($id, $data);
            return redirect()->to(base_url('cuentas'))->with('mensaje', 'Cuenta actualizada correctamente.');
        } else {
            $cuenta = $this->cuentaModel->find($id);
            if ($cuenta) {
                $data = [
                    'titulo' => 'Editar Cuenta',
                    'cuenta' => $cuenta,
                    'validation' => $this->validator,
                ];
                return view('Panel/editar_cuenta_bancaria', $data);
            } else {
                return redirect()->to(base_url('cuentas'))->with('error', 'Cuenta no encontrada.');
            }
        }
    }

    public function borrar($id = null)
    {
        $cuenta = $this->cuentaModel->find($id);
        if ($cuenta) {
            $this->cuentaModel->delete($id);
            return redirect()->to(base_url('cuentas'))->with('mensaje', 'Cuenta eliminada correctamente.');
        } else {
            return redirect()->to(base_url('cuentas'))->with('error', 'Cuenta no encontrada.');
        }
    }

    public function listar() //enlista las cuentas bancarias
    {
        $cuentasModel = new CuentasModel();
        return $this->response->setJSON($cuentasModel->findAll());
    }
}