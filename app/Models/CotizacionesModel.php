<?php 

namespace App\Models;
use CodeIgniter\Model;

class CotizacionesModel extends Model
{
    protected $table = 'sellopro_cotizaciones';
    protected $primaryKey = 'idQt';
    protected $allowedFields = [
        'slug',
        'cliente',
        'fecha',
        'caduca',
        'total',
        'anticipo',
        'descuento',
        'pago',
        'entregada',
    ];
}