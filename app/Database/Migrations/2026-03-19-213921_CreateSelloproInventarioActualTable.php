<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSelloproInventarioActualTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_articulo' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('id_articulo');

        $this->forge->createTable('sellopro_inventario_actual');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_inventario_actual');
    }
}