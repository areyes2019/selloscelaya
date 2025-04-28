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

    // Validation Rules (puedes definirlas aquí o en el controlador)
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;


    // --- Métodos Helper ---

    /**
     * Obtiene las órdenes agrupadas por status para el dashboard.
     */
    public function getOrdenesPorStatus(array $statuses = ['Diseño', 'Elaboracion', 'Entrega'])
    {
        $result = [];
        // Inicializa el resultado para asegurar que todas las claves existan, incluso si no hay órdenes
        foreach ($statuses as $status) {
            $result[$status] = [];
        }

        if (empty($statuses)) {
            return $result; // No buscar nada si no se pasan estados
        }

        $ordenes = $this->whereIn('status', $statuses)
                        ->orderBy('created_at', 'ASC') // O como prefieras ordenar
                        ->findAll();

        // Agrupar los resultados por status
        foreach ($ordenes as $orden) {
            if (isset($result[$orden->status])) { // Verifica si el status es uno de los solicitados
                 $result[$orden->status][] = $orden;
            }
        }

        return $result;
    }

    /**
     * (Opcional) Función para obtener específicamente las órdenes terminadas,
     * útil si necesitas una vista de historial de terminadas.
     */
    public function getOrdenesTerminadas($limit = 20, $offset = 0)
    {
         return $this->where('status', 'Terminado')
                     ->orderBy('updated_at', 'DESC') // Ordenar por fecha de finalización
                     ->findAll($limit, $offset);
                     // Considera usar paginate() aquí si son muchas:
                     // ->paginate($limit);
    }
}