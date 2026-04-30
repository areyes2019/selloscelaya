<?php 

namespace App\Models;
use CodeIgniter\Model;

class CotizacionesModel extends Model
{
    protected $table = 'sellopro_cotizaciones';
    protected $primaryKey = 'id_cotizacion';
    protected $allowedFields = [
        'slug',
        'cliente',
        'tipo_venta', //nuevo campo
        'fecha',
        'caduca',
        'subtotal',
        'iva',
        'total',
        'anticipo',
        'descuento',
        'pago',
        'entregada',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}