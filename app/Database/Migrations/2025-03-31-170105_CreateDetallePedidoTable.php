<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetallePedidoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pedido_id' => [ // Foreign Key
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'cantidad' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'precio_unitario' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'subtotal' => [ // Calculado: cantidad * precio_unitario
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
             'created_at' => [ // Opcional para detalles, pero útil
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [ // Opcional para detalles
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        // Añadir Clave Foránea
        $this->forge->addForeignKey('pedido_id', 'pedidos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detalle_pedido');
    }

    public function down()
    {
        $this->forge->dropTable('detalle_pedido');
    }
}
