<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixDescuentoYAgregarPorcentaje extends Migration
{
    public function up()
    {
        // Asegurar que descuento sea DECIMAL(10,2) (en producción puede estar como DECIMAL(5,2))
        $this->forge->modifyColumn('sellopro_cotizaciones', [
            'descuento' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'null'       => false,
            ],
        ]);

        // Agregar columna para guardar el porcentaje de descuento aplicado
        if (!$this->db->fieldExists('porcentaje_descuento', 'sellopro_cotizaciones')) {
            $this->forge->addColumn('sellopro_cotizaciones', [
                'porcentaje_descuento' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'default'    => 0.00,
                    'null'       => false,
                    'after'      => 'descuento',
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_cotizaciones', 'porcentaje_descuento');
    }
}
