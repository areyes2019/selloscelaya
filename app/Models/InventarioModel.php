<?php

namespace App\Models;
use CodeIgniter\Model;

class InventarioModel extends Model
{
    protected $table = 'sellopro_inventario'; // Asegúrate que este sea el nombre correcto de tu tabla
    protected $primaryKey = 'id_entrada';   // Asegúrate que este sea el nombre correcto de tu PK

    protected $allowedFields = [
        'id_articulo',
        'cantidad'
        // Puedes añadir otros campos si los tienes, como 'fecha_movimiento', 'tipo_movimiento', etc.
    ];

    // Si quieres usar timestamps (created_at, updated_at) en esta tabla también
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // --- Relación con Articulos (Opcional pero útil) ---
    // Si quieres acceder fácilmente a los datos del artículo desde un registro de inventario
    protected $useSoftDeletes = false; // O true si usas borrado lógico

    // Define la relación (Necesitarás crear la Entidad Inventario también)
    // protected $with = ['articulo']; // Descomenta si creas la entidad y la relación

    // --- Función para obtener inventario con detalles del artículo ---
    // Alternativa si no usas Entidades/Relaciones complejas
    public function getInventarioConArticulos()
    {
        $this->select('sellopro_inventario.*, sa.nombre, sa.precio_pub, sa.precio_prov'); // Selecciona campos necesarios
        $this->join('sellopro_articulos sa', 'sa.id_articulo = sellopro_inventario.id_articulo');
        return $this->findAll();
    }

    // --- Función para encontrar un registro de inventario por id_articulo ---
    public function findByArticuloId($id_articulo)
    {
        return $this->where('id_articulo', $id_articulo)->first();
    }
}