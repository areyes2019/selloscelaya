<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoOrigenToOrdenesTrabajo extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sellopro_ordenes_trabajo', [
            'tipo_origen' => [
                'type' => 'ENUM',
                'constraint' => ['pedido', 'cotizacion'],
                'null' => false,
                'default' => 'pedido'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_ordenes_trabajo', 'tipo_origen');
    }
}
