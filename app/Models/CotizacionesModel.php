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
        'tipo_venta',
        'fecha',
        'caduca',
        'subtotal',
        'iva',
        'total',
        'anticipo',
        'descuento',
        'pago',
        'estatus',
        'estado_financiero',
        'estado_fiscal',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}