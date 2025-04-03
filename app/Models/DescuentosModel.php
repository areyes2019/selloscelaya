<?php 

namespace App\Models;
use CodeIgniter\Model;

class DescuentosModel extends Model
{
    protected $table = 'sellopro_descuentos';
    protected $primaryKey = 'id_descuento';
    protected $allowedFields = [
        'nombre',
        'descuento'
    ];

    // Habilitar timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
}