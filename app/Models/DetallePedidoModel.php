<?php

namespace App\Models;

use CodeIgniter\Model;

class DetallePedidoModel extends Model
{
    protected $table            = 'detalle_pedido';
    protected $primaryKey       = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields = [
        'pedido_id',
        'id_articulo', // Asegúrate que esté este campo
        'descripcion',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    // Dates (opcional para detalles)
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

}