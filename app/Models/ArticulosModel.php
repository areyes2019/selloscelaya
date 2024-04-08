<?php 

namespace App\Models;
use CodeIgniter\Model;

class ArticulosModel extends Model
{
    protected $table = 'sellos_articulos';
    protected $primaryKey = 'idArticulo';
    protected $allowedFields = [
        'nombre',
        'modelo',
        'precio_prov',
        'precio_pub',
        'minimo',
        'stock',
    ];
}