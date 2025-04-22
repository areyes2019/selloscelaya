<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarCamposTotalesDetallesCotizacion extends Migration
{
    public function up()
    {
        $fields = [
            'subtotal' => [ //esto es el rfc del cliente
                'type'       => 'DECIMAL',
                'constraint' => [10,2],
                'null'      => true,
                'after'     => 'caduca' // Opcional: especificar después de qué campo
            ],
            'iva' => [ //esto es el rfc del cliente
                'type'       => 'DECIMAL',
                'constraint' => [10,2],
                'null'      => true,
                'after'     => 'subtotal' // Opcional: especificar después de qué campo
            ],

        ];
        
        $this->forge->addColumn('sellopro_cotizaciones', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_cotizaciones', ['subtotal','iva']);
    }
}
