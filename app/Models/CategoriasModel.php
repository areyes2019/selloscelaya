<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriasModel extends Model
{
    protected $table = 'sellopro_categorias';
    protected $primaryKey = 'id_categoria';
    
    protected $allowedFields = ['nombre']; 
    protected $returnType = 'array';
    protected $useTimestamps = false;
    
    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[100]|is_unique[sellopro_categorias.nombre]',
    ];
    
    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre de la categoría es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede exceder los 100 caracteres',
            'is_unique' => 'Ya existe una categoría con este nombre',
        ],
    ];
}