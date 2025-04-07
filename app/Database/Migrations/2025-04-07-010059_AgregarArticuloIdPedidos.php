<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarArticuloIdPedidos extends Migration
{
    public function up()
    {
        $fields = [
            'id_articulo' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false, // NOT NULL como requeriste
                'after'=>'descripcion'
            ],
        ];
        
        $this->forge->addColumn('detalle_pedido', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('detalle_pedido', ['id_articulo']);
    }
}
