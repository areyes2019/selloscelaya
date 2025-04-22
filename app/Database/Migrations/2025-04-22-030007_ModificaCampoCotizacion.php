<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModificaCampoCotizacion extends Migration
{
    public function up()
    {
        // Verificar si la tabla existe (forma correcta en CI4)
        if ($this->db->tableExists('sellopro_cotizaciones')) {
            // Verificar si la columna existe
            if ($this->db->fieldExists('pago', 'sellopro_cotizaciones')) {
                $this->forge->modifyColumn('sellopro_cotizaciones', [
                    'pago' => [
                        'type' => 'TINYINT',
                        'constraint' => 3,
                        'unsigned' => true,
                        'null' => false,
                        'default' => 0
                    ]
                ]);
            }
        }
    }

    public function down()
    {
        // Revertir el cambio
        if ($this->db->tableExists('sellopro_cotizaciones')) {
            if ($this->db->fieldExists('pago', 'sellopro_cotizaciones')) {
                $this->forge->modifyColumn('sellopro_cotizaciones', [
                    'pago' => [
                        'type' => 'DECIMAL',
                        'constraint' => [10,2],
                        'null' => false,
                        'default' => 0.00
                    ]
                ]);
            }
        }
    }
}