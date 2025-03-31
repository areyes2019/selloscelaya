<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePedidosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'cliente_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null' => false,
            ],
            'cliente_telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => '25',
                'null' => true, // Opcional
            ],
            'total' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2', // 10 dígitos en total, 2 decimales
                'default'    => 0.00,
            ],
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'pendiente', // Ej: pendiente, completado, cancelado
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [ // Para soft deletes (opcional pero recomendado)
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pedidos');
    }

    public function down()
    {
        $this->forge->dropTable('pedidos');
    }
}