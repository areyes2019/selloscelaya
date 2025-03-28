<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSelloproPedidosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pedido_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'proveedor' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ],
            'pagado' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ],
            'recibido' => [
                'type' => 'TINYINT',
                'constraint' => 1,
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

        $this->forge->addPrimaryKey('pedido_id');
        $this->forge->createTable('sellopro_pedidos');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_pedidos');
    }
}