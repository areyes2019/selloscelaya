<?php

namespace App\Models;

use CodeIgniter\Model;

class GastosModel extends Model
{
    protected $table      = 'sellopro_gastos';
    protected $primaryKey = 'id_registro';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'descripcion', 
        'entrada', 
        'salida', 
        'cuenta_origen',
        'cuenta_destino',
        'fecha_gasto',
    ];

}