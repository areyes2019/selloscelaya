<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaGastos extends Migration
{
   public function up()
    {
        $this->forge->addField([
            'id_registro' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'descripcion' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'monto' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'fecha_gasto' => [
                'type'       => 'DATE',
                'null'       => false,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id_registro');
        $this->forge->createTable('sellopro_gastos');
    }

    public function down()
    {
        $this->forge->dropTable('sello_gastos');
    }
}
