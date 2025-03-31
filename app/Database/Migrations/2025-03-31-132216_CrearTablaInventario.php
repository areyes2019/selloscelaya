<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaInventario extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_entrada' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'id_articulo' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'cantidad' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1
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

        $this->forge->addPrimaryKey('id_entrada');
        $this->forge->createTable('sellopro_inventario');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_inventario');
    }
}
