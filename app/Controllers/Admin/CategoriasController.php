<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoriasModel;
use CodeIgniter\API\ResponseTrait;

class CategoriasController extends BaseController
{
    use ResponseTrait;
    
    protected $model;
    
    public function __construct()
    {
        $this->model = new CategoriasModel();
    }
    
    // Vista principal (lista de categorías)
    public function index()
    {   
        return view('Panel/categorias');
    }
    public function show()
    {
        $resultado = $this->model->findAll();
        return json_encode($resultado);
    }
    // Guardar nueva categoría (AJAX)
    public function store()
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden('Acceso no permitido');
        }
        
        if ($this->validateRequest()) {
            $this->model->save([
                'nombre' => $this->request->getPost('nombre'),
            ]);
            
            return $this->respondCreated([
                'success' => true,
                'message' => 'Categoría creada correctamente',
            ]);
        }
        
        return $this->respond([
            'success' => false,
            'errors' => $this->model->errors(),
        ], 422);
    }
    
    // Mostrar formulario de edición (para el modal)
    public function edit($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden('Acceso no permitido');
        }
        
        $categoria = $this->model->find($id);
        
        if (!$categoria) {
            return $this->failNotFound('Categoría no encontrada');
        }
        
        return $this->respond([
            'success' => true,
            'data' => $categoria,
        ]);
    }
    
    // Actualizar categoría (AJAX)
    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->failForbidden('Acceso no permitido');
        }
        
        $categoria = $this->model->find($id);
        
        if (!$categoria) {
            return $this->failNotFound('Categoría no encontrada');
        }
        
        // Validación especial para update (ignorar el mismo registro)
        $rules = $this->model->getValidationRules();
        $rules['nombre'] = str_replace('{id}', $id, $rules['nombre']);
        
        if ($this->validate($rules, $this->model->getValidationMessages())) {
            $this->model->update($id, [
                'nombre' => $this->request->getPost('nombre'),
            ]);
            
            return $this->respond([
                'success' => true,
                'message' => 'Categoría actualizada correctamente',
            ]);
        }
        
        return $this->respond([
            'success' => false,
            'errors' => $this->model->errors(),
        ], 422);
    }
    
    // Eliminar categoría (AJAX)
    public function delete($id)
    {
        
        $categoria = $this->model->find($id);
        
        if (!$categoria) {
            return $this->failNotFound('Categoría no encontrada');
        }
        
        $this->model->delete($id);
        
        return $this->respond([
            'success' => true,
            'message' => 'Categoría eliminada correctamente',
        ]);
    }
    
    // Validar los datos del request
    protected function validateRequest()
    {
        $rules = $this->model->getValidationRules();
        $messages = $this->model->getValidationMessages();
        
        return $this->validate($rules, $messages);
    }
}