<?php 

namespace App\Models;
use CodeIgniter\Model;

class ProveedoresModel extends Model
{
    protected $table = 'sellopro_proveedores';
    protected $primaryKey = 'id_proveedor';
    protected $allowedFields = [
        'empresa',
        'contacto',
        'direccion',
        'telefono',
        'correo',
    ];
}