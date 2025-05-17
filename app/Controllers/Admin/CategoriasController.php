<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoriasModel;

class CategoriasController extends BaseController
{
    protected $categoriasModel;

    public function __construct()
    {
        $this->categoriasModel = new CategoriasModel();
    }

    public function index()
    {
        $data = [
            'categorias' => $this->categoriasModel->findAll()
        ];
        return view('Panel/categorias', $data);
    }


    public function store()
    {
        $rules = [
            'nombre' => 'required|min_length[3]|max_length[100]|is_unique[sellopro_categorias.nombre]'
        ];

        $messages = [
            'nombre' => [
                'required' => 'El nombre de la categoría es obligatorio',
                'min_length' => 'El nombre debe tener al menos 3 caracteres',
                'max_length' => 'El nombre no puede exceder los 100 caracteres',
                'is_unique' => 'Ya existe una categoría con este nombre'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->with('error_nombre', $this->validator->getError('nombre'))
                ->withInput();
        }

        $data = [
            'nombre' => $this->request->getPost('nombre')
        ];

        if ($this->categoriasModel->save($data)) {
            return redirect()->to('categorias')
                ->with('success', 'Categoría creada correctamente');
        } else {
            return redirect()->back()
                ->with('errors', ['Error al guardar la categoría'])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $categoria = $this->categoriasModel->find($id);
        
        if (!$categoria) {
            return redirect()->to('categorias')
                ->with('errors', ['Categoría no encontrada']);
        }

        return view('Panel/editar_categorias', [
            'categoria' => $categoria
        ]);
    }

    public function update($id)
    {
        $rules = [
            'nombre' => "required|min_length[3]|max_length[100]|is_unique[sellopro_categorias.nombre,id_categoria,{$id}]"
        ];

        $messages = [
            'nombre' => [
                'required' => 'El nombre de la categoría es obligatorio',
                'min_length' => 'El nombre debe tener al menos 3 caracteres',
                'max_length' => 'El nombre no puede exceder los 100 caracteres',
                'is_unique' => 'Ya existe una categoría con este nombre'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->with('error_nombre', $this->validator->getError('nombre'))
                ->withInput();
        }

        $data = [
            'id_categoria' => $id,
            'nombre' => $this->request->getPost('nombre')
        ];

        if ($this->categoriasModel->save($data)) {
            return redirect()->to('categorias')
                ->with('success', 'Categoría actualizada correctamente');
        } else {
            return redirect()->back()
                ->with('errors', ['Error al actualizar la categoría'])
                ->withInput();
        }
    }

    public function delete($id)
    {
        if ($this->categoriasModel->delete($id)) {
            return redirect()->to('categorias')
                ->with('success', 'Categoría eliminada correctamente');
        } else {
            return redirect()->to('categorias')
                ->with('errors', ['Error al eliminar la categoría']);
        }
    }
}