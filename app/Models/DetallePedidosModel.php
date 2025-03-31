<?php 

namespace App\Models;
use CodeIgniter\Model;

class DetallePedidosModel extends Model
{
    protected $table = 'sellopro_detalles_pedido';
    protected $primaryKey = 'id_detalle_pedido';
    protected $allowedFields = [
        'cantidad',
        'id_articulo',
        'descripcion',
        'p_unitario',
        'total',
        'id_pedido',
    ];
    // Habilitar timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}