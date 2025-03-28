<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSelloproDetallesPedidoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'pedido_detalle_id' => [
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
            'pedido_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('pedido_detalle_id');
        $this->forge->createTable('sellopro_detalles_pedido');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_detalles_pedido');
    }
}