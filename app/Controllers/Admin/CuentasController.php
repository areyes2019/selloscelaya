<?php

namespace App\Controllers;

use App\Models\SelloproCuentaModel;

class CuentasController extends BaseController
{
    protected $cuentaModel;

    public function __construct()
    {
        $this->cuentaModel = new SelloproCuentaModel();
    }

    public function index()
    {
        $cuentas = $this->cuentaModel->findAll();
        $data = [
            'titulo' => 'Listado de Cuentas',
            'cuentas' => $cuentas,
        ];
        return view('cuentas/listado_cuentas_bancarias', $data);
    }

    public function nuevo()
    {
        $data = [
            'titulo' => 'Nueva Cuenta',
        ];
        return view('cuentas/nuevo', $data);
    }

    public function guardar()
    {
        $rules = [
            'Banco' => 'required|max_length[255]',
            'NoCta' => 'required|integer',
            'Saldo' => 'required|decimal',
        ];

        if ($this->validate($rules)) {
            $data = [
                'Banco' => $this->request->getPost('Banco'),
                'NoCta' => $this->request->getPost('NoCta'),
                'Saldo' => $this->request->getPost('Saldo'),
            ];
            $this->cuentaModel->insert($data);
            return redirect()->to(base_url('cuentas'))->with('mensaje', 'Cuenta guardada correctamente.');
        } else {
            $data = [
                'titulo' => 'Nueva Cuenta',
                'validation' => $this->validator,
            ];
            return view('cuentas/nuevo', $data);
        }
    }

    public function editar($id = null)
    {
        $cuenta = $this->cuentaModel->find($id);
        if ($cuenta) {
            $data = [
                'titulo' => 'Editar Cuenta',
                'cuenta' => $cuenta,
            ];
            return view('cuentas/editar', $data);
        } else {
            return redirect()->to(base_url('cuentas'))->with('error', 'Cuenta no encontrada.');
        }
    }

    public function actualizar($id = null)
    {
        $rules = [
            'Banco' => 'required|max_length[255]',
            'NoCta' => 'required|integer',
            'Saldo' => 'required|decimal',
        ];

        if ($this->validate($rules)) {
            $data = [
                'Banco' => $this->request->getPost('Banco'),
                'NoCta' => $this->request->getPost('NoCta'),
                'Saldo' => $this->request->getPost('Saldo'),
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
                return view('cuentas/editar', $data);
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
}