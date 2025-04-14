<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSelloproBalanceTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_balance' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'monto' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'      => false,
            ],
            'capital' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'      => false,
            ],
            'beneficio' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'      => false,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
        ]);

        $this->forge->addPrimaryKey('id_balance');
        $this->forge->createTable('sellopro_balance');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_balance');
    }
}
