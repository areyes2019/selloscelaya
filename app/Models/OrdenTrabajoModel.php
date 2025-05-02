<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdenTrabajoModel extends Model
{
    protected $table            = 'sellopro_ordenes_trabajo';
    protected $primaryKey       = 'id_ot';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object'; // o array
    protected $useSoftDeletes   = false; // Cambia a true si descomentaste deleted_at en la migración
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pedido_id',
        'cliente_nombre',
        'cliente_telefono',
        'observaciones',
        'color_tinta',
        'imagen_path',
        'status',
        // No incluyas created_at, updated_at, deleted_at aquí si usas timestamps/softdeletes
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}