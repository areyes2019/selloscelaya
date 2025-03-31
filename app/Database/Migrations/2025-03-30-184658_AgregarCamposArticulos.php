<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarCamposArticulos extends Migration
{
    public function up()
    {
        $fields = [
            'clave_producto' => [ //esto es el rfc del cliente
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'      => true,
                'after'     => 'stock' // Opcional: especificar después de qué campo
            ],
        ];
        
        $this->forge->addColumn('sellopro_articulos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_articulos', ['clave_producto']);
    }
}
