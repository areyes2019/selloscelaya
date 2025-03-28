<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSelloproClientesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_cliente' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'correo' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'direccion' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'telefono' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true
            ],
            'ciudad' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'estado' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'descuento' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00
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

        $this->forge->addPrimaryKey('id_cliente');
        $this->forge->createTable('sellopro_clientes');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_clientes');
    }
}