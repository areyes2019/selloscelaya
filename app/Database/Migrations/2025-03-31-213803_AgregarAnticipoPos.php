<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarAnticipoPos extends Migration
{
    public function up()
    {
        $fields = [
            'anticipo' => [ //esto es el rfc del cliente
                'type'       => 'DECIMAL',
                'constraint' => '10.2',
                'null'      => true,
                'after'     => 'cliente_telefono' // Opcional: especificar después de qué campo
            ],
        ];
        
        $this->forge->addColumn('pedidos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pedidos', ['anticipo']);
    }
}
