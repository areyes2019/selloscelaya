<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSelloproCotizacionesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_cotizacion' => [
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
            'cliente' => [
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
            'anticipo' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00
            ],
            'descuento' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00
            ],
            'pago' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
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

        $this->forge->addPrimaryKey('id_cotizacion');
        $this->forge->createTable('sellopro_cotizaciones');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_cotizaciones');
    }
}