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
        'img',
        'venta',
        'precio_dist',
        'clave_producto'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}