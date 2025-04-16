<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarProveedorArticulo extends Migration
{
    public function up()
    {
        $fields = [
            'proveedor' => [ //esto es el rfc del cliente
                'type'       => 'INT',
                'constraint' => 10,
                'null'      => true,
                'after'     => 'venta' // Opcional: especificar después de qué campo
            ],
        ];
        
        $this->forge->addColumn('sellopro_articulos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_articulos', ['proveedor']);
    }
}
