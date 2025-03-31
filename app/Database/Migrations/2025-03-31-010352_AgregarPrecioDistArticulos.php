<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarPrecioDistArticulos extends Migration
{
    public function up()
    {
        $fields = [
            'precio_dist' => [ //esto es el rfc del cliente
                'type'       => 'DECIMAL',
                'constraint' => 10,
                'null'      => true,
                'after'     => 'precio_pub' // Opcional: especificar después de qué campo
            ],
        ];
        
        $this->forge->addColumn('sellopro_articulos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_articulos', ['precio_dist']);
    }
}
