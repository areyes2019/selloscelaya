<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiscountFieldsToPedidos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pedidos', [
            'total_sin_descuento' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0,
                'after' => 'total'
            ],
            'descuento' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => false,
                'default' => 0,
                'after' => 'total_sin_descuento'
            ],
            'monto_descuento' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'default' => 0,
                'after' => 'descuento'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pedidos', ['total_sin_descuento', 'descuento', 'monto_descuento']);
    }
}
