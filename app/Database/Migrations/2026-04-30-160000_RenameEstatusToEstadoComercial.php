<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameEstatusToEstadoComercial extends Migration
{
    public function up(): void
    {
        $existing = $this->db->getFieldNames('sellopro_cotizaciones');

        // NOTA: La columna 'estado_comercial' se agregó en la migración
        // 2026-04-30-120000_AddEstadosFinancieroFiscalToCotizaciones.
        // Aquí solo nos aseguramos de limpiar columnas antiguas que pudieran
        // haber quedado si esa migración no se ejecutó completamente.

        // Eliminar columnas que ya no se usan
        foreach (['estatus', 'estado_financiero', 'estado_fiscal'] as $col) {
            if (in_array($col, $this->db->getFieldNames('sellopro_cotizaciones'))) {
                $this->forge->dropColumn('sellopro_cotizaciones', $col);
            }
        }
    }

    public function down(): void
    {
        $existing = $this->db->getFieldNames('sellopro_cotizaciones');

        // Restaurar estatus
        if (!in_array('estatus', $existing)) {
            $this->forge->addColumn('sellopro_cotizaciones', [
                'estatus' => [
                    'type'       => 'TINYINT',
                    'constraint' => 2,
                    'default'    => 0,
                    'null'       => false,
                    'after'      => 'pago',
                ],
            ]);

            if (in_array('estado_comercial', $existing)) {
                $this->db->query("
                    UPDATE sellopro_cotizaciones
                    SET estatus = CASE
                        WHEN estado_comercial IN ('enviada', 'anticipo', 'pagado', 'facturada') THEN 1
                        ELSE 0
                    END
                ");
            }
        }
    }
}
