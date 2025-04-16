<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaBancos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_cuenta' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'Banco' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'NoCta' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'Saldo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true, // Permite valores nulos si no se establece al crear
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true, // Permite valores nulos si no se establece al crear/actualizar
            ],
        ]);
        $this->forge->addKey('id_cuenta', true); // Define id_cuenta como clave primaria
        $this->forge->createTable('sellopro_cuentas');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_cuentas');
    }
}