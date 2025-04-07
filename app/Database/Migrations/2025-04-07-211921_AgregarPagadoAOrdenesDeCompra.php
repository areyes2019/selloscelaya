<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarPagadoAOrdenesDeCompra extends Migration
{
    public function up()
    {
        $fields = [
            'pagado' => [ //esto es el rfc del cliente
                'type'       => 'INT',
                'constraint' => 10,
                'null'      => true,
                'after'     => 'entregada' // Opcional: especificar después de qué campo
            ],
        ];
        
        $this->forge->addColumn('sellopro_pedidos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_pedidos', ['pagado']);
    }
}
