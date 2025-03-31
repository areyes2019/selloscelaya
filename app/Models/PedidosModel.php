<?php 

namespace App\Models;
use CodeIgniter\Model;

class PedidosModel extends Model
{
    protected $table = 'sellopro_pedidos';
    protected $primaryKey = 'id_pedido';
    protected $allowedFields = [
        'slug',
        'proveedor',
        'caduca',
        'total',
        'entregada',
    ];

    // Habilitar timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
}