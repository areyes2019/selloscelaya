<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdenesTrabajo extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_ot' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pedido_id' => [ // Referencia al ticket/pedido original
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'cliente_nombre' => [ // Guardamos copia por si el original cambia
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'cliente_telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => '25',
                'null'       => true,
            ],
            'observaciones' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'color_tinta' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'imagen_path' => [ // Guardaremos la ruta de la imagen subida
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // 'Diseño', 'Elaboracion', 'Entrega'
                'default'    => 'Diseño',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [ // Opcional: para soft deletes
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_ot', true);
        // Añade un índice al status para búsquedas rápidas
        $this->forge->addKey('status');
        // Llave foránea (opcional pero recomendado)
        // Asegúrate de que tu tabla 'pedidos' exista y tenga una columna 'id' compatible
        // $this->forge->addForeignKey('pedido_id', 'pedidos', 'id', 'CASCADE', 'SET NULL'); // O 'CASCADE', 'RESTRICT' según tu lógica
        $this->forge->createTable('sellopro_ordenes_trabajo');
    }

    public function down()
    {
        $this->forge->dropTable('sellopro_ordenes_trabajo');
    }
}