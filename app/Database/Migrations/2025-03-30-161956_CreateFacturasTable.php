<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFacturasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'cotizacion_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ],
            'factura_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'folio' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'serie' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true
            ],
            'estado' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'generada'
            ],
            'fecha_timbrado' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'pdf_url' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'xml_url' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'respuesta_completa' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('cotizacion_id');
        $this->forge->addKey('factura_uuid');
        $this->forge->createTable('sellopro_facturas');
    }

    public function down()
    {
        $this->forge->dropTable('facturas');
    }
}