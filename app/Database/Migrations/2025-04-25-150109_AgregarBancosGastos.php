<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarBancosGastos extends Migration
{
    public function up()
    {
        $fields = [
            'cuenta_origen' => [ // ID de la cuenta bancaria de origen
                'type'       => 'INT',
                'null'       => false,
                'default'    => 0,
                'after'      => 'monto'
            ],
            'cuenta_destino' => [ // ID de la cuenta bancaria de destino
                'type'       => 'INT',
                'null'       => true,
                'after'      => 'cuenta_origen'
            ],
        ];
        
        $this->forge->addColumn('sellopro_gastos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_gastos', ['cuenta_origen', 'cuenta_destino']);
    }
}

