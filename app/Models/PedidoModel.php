<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoModel extends Model
{
    protected $table            = 'pedidos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = true; // Habilitar soft deletes si la columna deleted_at existe
    protected $protectFields    = true;
    protected $allowedFields    = ['cliente_nombre', 'cliente_telefono', 'total', 'estado','anticipo'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; // Necesario para useSoftDeletes

    // Relaciones (opcional pero muy útil)
    public function detalles()
    {
        // Un pedido tiene muchos detalles
        return $this->hasMany(DetallePedidoModel::class, 'pedido_id', 'id');
    }
}