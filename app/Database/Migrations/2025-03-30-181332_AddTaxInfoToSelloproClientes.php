<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTaxInfoToSelloproClientes extends Migration
{
    public function up()
    {
        $fields = [
            'tax_id' => [ //esto es el rfc del cliente
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'      => true,
                'after'     => 'estado' // Opcional: especificar después de qué campo
            ],
            'regimen_fiscal' => [
                'type'       => 'INT',
                'constraint' => 10,
                'null'       => true,
                'after'     => 'tax_id' // Opcional: especificar después de qué campo
            ],
            'codigo_postal' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'after'     => 'regimen_fiscal' // Opcional: especificar después de qué campo
            ]
        ];
        
        $this->forge->addColumn('sellopro_clientes', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_clientes', ['tax_id', 'regimen_fiscal', 'codigo_postal']);
    }
}
