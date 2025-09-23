<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeEntregadaToEstatusInCotizaciones extends Migration
{
    public function up()
    {
        // Cambiar el nombre del campo y su tipo
        $this->forge->modifyColumn('sellopro_cotizaciones', [
            'entregada' => [
                'name' => 'estatus',
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0,
                'comment' => '0: Pendiente, 1: Entregada, 2: Cancelada, etc.'
            ]
        ]);
    }

    public function down()
    {
        // Revertir los cambios en caso de rollback
        $this->forge->modifyColumn('sellopro_cotizaciones', [
            'estatus' => [
                'name' => 'entregada',
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ]
        ]);
    }
}