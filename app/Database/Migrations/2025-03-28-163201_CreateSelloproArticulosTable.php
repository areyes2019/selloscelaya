<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSelloproArticulosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_articulo' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'modelo' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'precio_prov' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ],
            'precio_pub' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ],
            'minimo' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ],
            'stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('id_articulo');
        $this->forge->createTable('sellopro_articulos');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_articulos');
    }
}