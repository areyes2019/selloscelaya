<?php 

namespace App\Models;
use CodeIgniter\Model;

class ArticulosModel extends Model
{
    protected $table = 'sellopro_articulos';
    protected $primaryKey = 'id_articulo';
    protected $allowedFields = [
        'nombre',
        'modelo',
        'precio_prov',
        'precio_pub',
        'minimo',
        'stock',
        'clave_producto'
    ];
}