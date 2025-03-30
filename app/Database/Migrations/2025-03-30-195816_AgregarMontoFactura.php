<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarMontoFactura extends Migration
{
    public function up()
    {
        $fields = [
            'monto' => [ //esto es el rfc del cliente
                'type'       => 'DECIMAL',
                'constraint' => 50,
                'null'      => true,
                'after'     => 'estado' // Opcional: especificar después de qué campo
            ]
        ];
        
        $this->forge->addColumn('sellopro_facturas', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_clientes', ['monto']);
    }
}
