<?php 

namespace App\Models;
use CodeIgniter\Model;

class ClientesModel extends Model
{
    protected $table = 'sellos_clientes';
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