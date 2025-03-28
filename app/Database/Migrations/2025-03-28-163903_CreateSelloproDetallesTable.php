<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSelloproDetallesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detalle' => [
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
            'id_cotizacion' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'inversion' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('id_detalle');
        $this->forge->createTable('sellopro_detalles');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_detalles');
    }
}