<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisibleToSelloproArticulos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sellopro_articulos', [
            'visible' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_articulos', 'visible');
    }
}
