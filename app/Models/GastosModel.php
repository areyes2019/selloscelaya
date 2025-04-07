<?php

namespace App\Models;

use CodeIgniter\Model;

class GastosModel extends Model
{
    protected $table      = 'sellopro_gastos';
    protected $primaryKey = 'id_registro';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['descripcion', 'monto', 'fecha_gasto'];


    protected $validationRules    = [
        'descripcion' => 'required|min_length[3]|max_length[255]',
        'monto'       => 'required|decimal',
        'fecha_gasto' => 'required|valid_date'
    ];
    
    protected $validationMessages = [
        'descripcion' => [
            'required'   => 'La descripción del gasto es obligatoria',
            'min_length' => 'La descripción debe tener al menos 3 caracteres',
            'max_length' => 'La descripción no puede exceder los 255 caracteres'
        ],
        'monto' => [
            'required' => 'El monto del gasto es obligatorio',
            'decimal'  => 'El monto debe ser un valor decimal válido'
        ],
        'fecha_gasto' => [
            'required'    => 'La fecha del gasto es obligatoria',
            'valid_date' => 'Debe proporcionar una fecha válida'
        ]
    ];
    
    protected $skipValidation = false;
}