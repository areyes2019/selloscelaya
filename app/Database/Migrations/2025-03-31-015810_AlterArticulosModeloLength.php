<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterArticulosModeloLength extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('sellopro_articulos', [
            'modelo' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ]
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('sellopro_articulos', [
            'modelo' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ]
        ]);
    }
}
