<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEstadosFinancieroFiscalToCotizaciones extends Migration
{
    public function up(): void
    {
        $existing = $this->db->getFieldNames('sellopro_cotizaciones');

        $fields = [
            'estado_financiero' => [
                'type'       => 'ENUM',
                'constraint' => ['pendiente', 'anticipo', 'parcial', 'pagado'],
                'default'    => 'pendiente',
                'null'       => false,
                'after'      => 'estatus',
            ],
            'estado_fiscal' => [
                'type'       => 'ENUM',
                'constraint' => ['sin_facturar', 'facturada', 'cancelada'],
                'default'    => 'sin_facturar',
                'null'       => false,
                'after'      => 'estado_financiero',
            ],
        ];

        foreach ($fields as $col => $def) {
            if (!in_array($col, $existing)) {
                $this->forge->addColumn('sellopro_cotizaciones', [$col => $def]);
            }
        }
    }

    public function down(): void
    {
        $this->forge->dropColumn('sellopro_cotizaciones', ['estado_financiero', 'estado_fiscal']);
    }
}
