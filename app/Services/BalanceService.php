<?php

namespace App\Services;

use App\Models\BalanceModel;
use App\Models\ArticulosModel;
use App\Models\DetallePedidoModel;
use Config\Database;
use Exception;

class BalanceService
{
    protected $balanceModel;

    public function __construct()
    {
        $this->balanceModel = new BalanceModel();
    }

    public function procesarPago(int $pedidoId, float $total): bool
    {
        $hoy = date('Y-m-d');
        $db = \Config\Database::connect();

        try {
            // 1. Consulta con JOIN a la tabla correcta "sellopro_articulos"
            $query = $db->table('detalle_pedido');
            $query->join('sellopro_articulos', 'sellopro_articulos.id_articulo = detalle_pedido.id_articulo', 'left')
                ->where('pedido_id', $pedidoId);

            $result = $query->get()->getResultArray();

            if ($result === false) {
                throw new Exception("Error en la consulta SQL. Verifica las tablas y relaciones.");
            }

            $detallesPedido = $result->getResultArray();

            if (empty($detallesPedido)) {
                throw new Exception("No hay artículos en el pedido #{$pedidoId}");
            }

            // 2. Calcular capital (precio_prov * cantidad)
            $capital = 0;
            foreach ($detallesPedido as $detalle) {
                if (!isset($detalle['precio_prov']) || $detalle['precio_prov'] === null) {
                    throw new Exception("Artículo sin precio de proveedor definido");
                }
                $capital += (float)$detalle['precio_prov'] * (int)$detalle['cantidad'];
            }

            // 3. Calcular beneficio
            $beneficio = $total - $capital;

            // 4. Buscar o crear registro en el balance (sellopro_balance)
            $balanceHoy = $this->balanceModel->where('fecha', $hoy)->first();

            if (!$balanceHoy) {
                // Crear registro nuevo
                $this->balanceModel->insert([
                    'fecha'     => $hoy,
                    'monto'     => (float)$total,
                    'capital'   => (float)$capital,
                    'beneficio' => (float)$beneficio
                ]);
            } else {
                // Actualizar registro existente
                $this->balanceModel->update($balanceHoy['id'], [
                    'monto'     => (float)$balanceHoy['monto'] + (float)$total,
                    'capital'   => (float)$balanceHoy['capital'] + (float)$capital,
                    'beneficio' => (float)$balanceHoy['beneficio'] + (float)$beneficio
                ]);
            }

            return true;

        } catch (Exception $e) {
            log_message('error', "Error en BalanceService: " . $e->getMessage());
            throw $e; // Relanzar para manejar el error en el controlador
        }
    }
}