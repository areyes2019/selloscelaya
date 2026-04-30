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
        'descuento',
        'tax_id',
        'regimen_fiscal',
        'codigo_postal',
        'calle',
        'no_ext',
        'no_int',
        'colonia',
        'municipio',
    ];
}