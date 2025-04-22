<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaVentas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_venta' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'ref' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'total_neto' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'inversion' => [ // si es inversión lo corregimos también en modelo
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'beneficio' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
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
        $this->forge->addKey('id_venta', true);
        $this->forge->createTable('sellopro_ventas');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_ventas');
    }
}

