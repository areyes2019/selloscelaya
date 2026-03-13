<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixPedidosTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sellopro_pedidos', [
            'caduca' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->modifyColumn('sellopro_pedidos', [
            'pedido_id' => [
                'name' => 'id_pedido',
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ]
        ]);

        $this->forge->modifyColumn('sellopro_pedidos', [
            'recibido' => [
                'name' => 'entregada',
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ]
        ]);
    }

    public function down()
    {
        //
    }
}
