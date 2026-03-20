<?php

namespace App\Services;

use App\Models\InventarioModel;
use App\Models\InventarioActualModel;

class InventarioService
{
    protected $movimientos;
    protected $actual;

    public function __construct()
    {
        $this->movimientos = new InventarioModel();
        $this->actual = new InventarioActualModel();
    }

    // ✅ ENTRADA (compras, ajustes, etc)
    public function entrada($id_articulo, $cantidad, $total = 0, $referencia = null)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $this->movimientos->insert([
            'id_articulo' => $id_articulo,
            'cantidad' => $cantidad,
            'total' => $total,
            'fecha' => date('Y-m-d H:i:s'),
            'tipo_movimiento' => 'entrada',
            'referencia' => $referencia
        ]);

        $this->actual->aumentarStock($id_articulo, $cantidad);

        $db->transComplete();
    }

    // 🔴 SALIDA (ventas)
    public function salida($id_articulo, $cantidad, $total = 0, $referencia = null)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $stock = $this->actual->getStock($id_articulo);

        if ($stock < $cantidad) {
            throw new \Exception("Stock insuficiente");
        }

        $this->movimientos->insert([
            'id_articulo' => $id_articulo,
            'cantidad' => $cantidad,
            'total' => $total,
            'fecha' => date('Y-m-d H:i:s'),
            'tipo_movimiento' => 'salida',
            'referencia' => $referencia
        ]);

        $this->actual->disminuirStock($id_articulo, $cantidad);

        $db->transComplete();
    }

    // 🔁 REVERSA (cancelaciones)
    public function reversa($id_articulo, $cantidad, $referencia = null)
    {
        $this->entrada($id_articulo, $cantidad, 0, $referencia);
    }
}