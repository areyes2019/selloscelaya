<?php 

namespace App\Models;
use CodeIgniter\Model;

class ClientesModel extends Model
{
    protected $table = 'sellopro_clientes';
    protected $primaryKey = 'id_cliente';
    protected $allowedFields = [
        'nombre',
        'tipo',
        'correo',
        'direccion',
        'telefono',
        'ciudad',
        'estado',
        'descuento'
    ];
}