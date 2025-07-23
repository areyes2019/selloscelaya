<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTareasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_tarea' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'titulo' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'      => false,
            ],
            'descripcion' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'fecha' => [
                'type'       => 'DATE',
                'null'       => false,
            ],
            'completada' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'prioridad' => [
                'type'       => 'ENUM',
                'constraint' => ['alta', 'media', 'baja'],
                'default'    => 'media',
            ],
            'id_usuario' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id_tarea');
        $this->forge->addKey('id_usuario');
        $this->forge->addKey('fecha');
        $this->forge->addKey('completada');
        
        // Si tienes una tabla de usuarios, puedes agregar esta relación
        // $this->forge->addForeignKey('id_usuario', 'usuarios', 'id_usuario', 'CASCADE', 'SET NULL');
        
        $this->forge->createTable('tareas');
    }

    public function down()
    {
        $this->forge->dropTable('tareas');
    }
}
