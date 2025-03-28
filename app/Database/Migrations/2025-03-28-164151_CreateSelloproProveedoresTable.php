<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSelloproProveedoresTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_proveedor' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'empresa' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'contacto' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'direccion' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'telefono' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true
            ],
            'correo' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
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

        $this->forge->addPrimaryKey('id_proveedor');
        $this->forge->createTable('sellopro_proveedores');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_proveedores');
    }
}