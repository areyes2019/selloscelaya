<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoToClientes extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sellopro_clientes', [
            'tipo' => [
                'type' => 'INT',
                'constraint' => 10,
                'null' => true,
                'after' => 'id_cliente' // Opcional: especifica después de qué campo irá
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_clientes', 'tipo');
    }
}
