<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaPorcentajes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_descuento' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'descuento' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null, // Mejor práctica para valores nulos
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);
        
        // Clave primaria
        $this->forge->addPrimaryKey('id_descuento');
        
        // Crear la tabla
        $this->forge->createTable('sellopro_descuentos');
        
        // Opcional: Agregar índice al campo descuento si se usará en búsquedas
        $this->forge->addKey('descuento');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_descuentos');
    }
}