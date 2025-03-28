<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSelloproInventarioTable extends Migration
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
                'default' => 0
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ],
            'fecha' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'tipo_movimiento' => [
                'type' => 'ENUM',
                'constraint' => ['entrada', 'salida'],
                'default' => 'entrada'
            ],
            'referencia' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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