<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarImgArticulos extends Migration
{
    public function up()
    {
        $fields = [
            'img' => [ //esto es el rfc del cliente
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'      => true,
                'after'     => 'stock' // Opcional: especificar después de qué campo
            ],
            'venta' => [ //esto es el rfc del cliente
                'type'       => 'INT',
                'constraint' => 10,
                'null'      => true,
                'after'     => 'img' // Opcional: especificar después de qué campo
            ],
        ];
        
        $this->forge->addColumn('sellopro_articulos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_articulos', ['img','venta']);
    }
}
