<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameEstatusToEstadoComercial extends Migration
{
    public function up(): void
    {
        $existing = $this->db->getFieldNames('sellopro_cotizaciones');

        // Agregar estado_comercial si no existe todavía
        if (!in_array('estado_comercial', $existing)) {
            $this->forge->addColumn('sellopro_cotizaciones', [
                'estado_comercial' => [
                    'type'       => 'ENUM',
                    'constraint' => ['borrador', 'enviada', 'anticipo', 'pagado', 'facturada'],
                    'default'    => 'borrador',
                    'null'       => false,
                    'after'      => 'pago',
                ],
            ]);

            // Migrar valores desde estatus TINYINT si existe
            if (in_array('estatus', $existing)) {
                $this->db->query("
                    UPDATE sellopro_cotizaciones
                    SET estado_comercial = CASE
                        WHEN estatus >= 1 THEN 'enviada'
                        ELSE 'borrador'
                    END
                ");
            }
        }

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

        // Eliminar estado_comercial
        if (in_array('estado_comercial', $this->db->getFieldNames('sellopro_cotizaciones'))) {
            $this->forge->dropColumn('sellopro_cotizaciones', 'estado_comercial');
        }
    }
}
