<?php 

namespace App\Models;
use CodeIgniter\Model;

class ContabilidadModel extends Model
{
    protected $table = 'sellos_cuentas';
    protected $primaryKey = 'idCliente';
    protected $allowedFields = [
        'nombre',
        'correo',
        'direccion',
        'telefono',
        'ciudad',
        'estado',
    ];
}