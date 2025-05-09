<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoVentaToCotizaciones extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sellopro_cotizaciones', [
            'tipo_venta' => [
            'type' => 'TINYINT',
            'constraint' => 1,
            'default' => 1, // 1 = contado, 2 = crédito
            'null' => false,
            'after' => 'cliente'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_cotizaciones', 'tipo_venta');
    }
}
