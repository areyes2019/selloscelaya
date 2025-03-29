<?php 

namespace App\Models;
use CodeIgniter\Model;

class DetalleModel extends Model
{
    protected $table = 'sellopro_detalles';
    protected $primaryKey = 'id_detalle';
    protected $allowedFields = [
        'cantidad',
        'id_articulo',
        'p_unitario',
        'total',
        'id_cotizacion',
        'inversion',
        'descripcion'
    ];
}