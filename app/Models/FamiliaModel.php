<?php 

namespace App\Models;
use CodeIgniter\Model;

class FamiliaModel extends Model
{
    protected $table = 'sellopro_familia';
    protected $primaryKey = 'id_familia';
    protected $allowedFields = [
        'nombre',
        'descuento',
        'id_proveedor'
    ];
}