<?php 

namespace App\Models;
use CodeIgniter\Model;

class ClientesModel extends Model
{
    protected $table = 'sellopro_clientes';
    protected $primaryKey = 'idCliente';
    protected $allowedFields = [
        'nombre',
        'correo',
        'direccion',
        'telefono',
        'ciudad',
        'estado',
        'descuento'
    ];
}