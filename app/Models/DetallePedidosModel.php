<?php 

namespace App\Models;
use CodeIgniter\Model;

class DetallePedidosModel extends Model
{
    protected $table = 'sellopro_detalles_pedido';
    protected $primaryKey = 'pedido_detalle_id';
    protected $allowedFields = [
        'id_articulo',
        'cantidad',
        'p_unitario',
        'total',
        'pedido_id',
    ];
}