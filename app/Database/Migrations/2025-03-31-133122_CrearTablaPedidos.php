<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaPedidos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pedido' => [
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
            'caduca' => [
                'type' => 'DATETIME',
                'null'=>true
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ],
            'entregada' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ]
        ]);

        $this->forge->addPrimaryKey('id_pedido');
        $this->forge->createTable('sellopro_pedidos');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_pedidos');
    }
}
