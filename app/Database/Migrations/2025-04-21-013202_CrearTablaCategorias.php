<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaCategorias extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_categoria' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
        ]);
        
        $this->forge->addPrimaryKey('id_categoria');
        $this->forge->createTable('sellopro_categorias');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_categorias');
    }
}
