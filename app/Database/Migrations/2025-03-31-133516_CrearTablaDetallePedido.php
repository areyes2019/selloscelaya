<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaDetallePedido extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detalle_pedido' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'cantidad' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1
            ],
            'id_articulo' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'p_unitario' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ],
            'id_pedido' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
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

        $this->forge->addPrimaryKey('id_detalle_pedido');
        $this->forge->createTable('sellopro_detalles_pedido');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_detalles_pedido');
    }
}
