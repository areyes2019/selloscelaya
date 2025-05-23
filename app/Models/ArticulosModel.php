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
        'precio_dist',
        'minimo',
        'stock',
        'img',
        'venta',
        'clave_producto',
        'visible',
        'categoria',
        'proveedor',
        'created_at',
        'updated_at'
    ];

    // Configuración de timestamps
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';

}