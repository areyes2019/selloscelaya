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
    protected $allowedFields    = ['cliente_nombre', 'cliente_telefono', 'total', 'estado', 'anticipo'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; // Necesario para useSoftDeletes
    protected $pagerGroup = 'default';
    // Callbacks
    protected $beforeUpdate = ['actualizarEstado'];

    /**
     * Callback para actualizar automáticamente el estado cuando el anticipo iguala al total
     */
    protected function actualizarEstado(array $data)
    {
        // Verificamos si se está actualizando el anticipo
        if (isset($data['data']['anticipo']) && isset($data['data']['total'])) {
            // Si el anticipo es igual al total, marcamos como pagado
            if ($data['data']['anticipo'] == $data['data']['total']) {
                $data['data']['estado'] = 'pagado';
            }
            // Opcional: Si el anticipo es mayor que 0 pero menor que el total, marcamos como "parcial"
            elseif ($data['data']['anticipo'] > 0 && $data['data']['anticipo'] < $data['data']['total']) {
                $data['data']['estado'] = 'parcial';
            }
        }
        return $data;
    }

    // Relaciones (opcional pero muy útil)
    public function detalles()
    {
        // Un pedido tiene muchos detalles
        return $this->hasMany(DetallePedidoModel::class, 'pedido_id', 'id');
    }

    /**
     * Método para marcar un pedido como pagado
     */
    public function marcarComoPagado($id)
    {
        $pedido = $this->find($id);
        if (!$pedido) {
            return false;
        }

        return $this->update($id, [
            'anticipo' => $pedido['total'],
            'estado' => 'pagado'
        ]);
    }
}