<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarCamposGastosEntradaSalida extends Migration
{
    public function up()
    {
        // Cambiar el nombre de 'monto' a 'entrada'
        $this->forge->modifyColumn('sellopro_gastos', [
            'monto' => [
                'name' => 'entrada',
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
                'null' => true,
                'after' => 'descripcion'
            ]
        ]);

        // Agregar el nuevo campo 'salida' después de 'entrada'
        $this->forge->addColumn('sellopro_gastos', [
            'salida' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => 0,
                'after' => 'entrada'
            ]
        ]);
    }

    public function down()
    {
        // Revertir los cambios en orden inverso
        $this->forge->modifyColumn('sellopro_gastos', [
            'entrada' => [
                'name' => 'monto',
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'after' => 'descripcion'
            ]
        ]);

        $this->forge->dropColumn('sellopro_gastos', 'salida');
    }
}

