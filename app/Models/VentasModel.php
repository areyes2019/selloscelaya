<?php

namespace App\Models;
use CodeIgniter\Model;

class VentasModel extends Model
{
    protected $table = 'sellopro_ventas';
    protected $primaryKey = 'id_venta';

    protected $allowedFields = [
        'ref',
        'total_neto',
        'inversion', // corregido según tu campo original
        'beneficio'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
