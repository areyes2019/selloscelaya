<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiscountFieldsToNombreTable extends Migration
{
    public function up()
    {
        $fields = [
            'total_sin_descuento' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'after' => 'total'
            ],
            'descuento' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'after' => 'total_sin_descuento'
            ],
            'monto_descuento' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'after' => 'descuento'
            ]
        ];

        $this->forge->addColumn('pedidos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pedidos', ['total_sin_descuento', 'descuento', 'monto_descuento']);
    }
}

