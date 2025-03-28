<?php 

namespace App\Models;
use CodeIgniter\Model;

class PedidosModel extends Model
{
    protected $table = 'sellopro_pedidos';
    protected $primaryKey = 'pedidos_id';
    protected $allowedFields = [
        'slug',
        'proveedor',
        'fecha',
        'caduca',
        'total',
        'pagado',
        'recibido'
    ];
    
}