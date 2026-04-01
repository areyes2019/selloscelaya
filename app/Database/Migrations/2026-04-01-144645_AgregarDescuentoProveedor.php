<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCamposProveedores extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sellopro_proveedores', [
            'descuento' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'after' => 'correo'
            ],
            'incluye_iva' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'descuento'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('sellopro_proveedores', ['descuento', 'incluye_iva']);
    }
}