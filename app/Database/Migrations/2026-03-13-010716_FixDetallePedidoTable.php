<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixDetallePedidoTable extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $forge = \Config\Database::forge();

        // PK pedido_detalle_id → id_detalle_pedido
        if ($db->fieldExists('pedido_detalle_id', 'sellopro_detalles_pedido')) {
            $forge->modifyColumn('sellopro_detalles_pedido', [
                'pedido_detalle_id' => [
                    'name' => 'id_detalle_pedido',
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true
                ]
            ]);
        }

        // pedido_id → id_pedido
        if ($db->fieldExists('pedido_id', 'sellopro_detalles_pedido')) {
            $forge->modifyColumn('sellopro_detalles_pedido', [
                'pedido_id' => [
                    'name' => 'id_pedido',
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true
                ]
            ]);
        }

        // agregar descripcion si no existe
        if (!$db->fieldExists('descripcion', 'sellopro_detalles_pedido')) {
            $forge->addColumn('sellopro_detalles_pedido', [
                'descripcion' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'id_articulo'
                ]
            ]);
        }

        // agregar updated_at si no existe
        if (!$db->fieldExists('updated_at', 'sellopro_detalles_pedido')) {
            $forge->addColumn('sellopro_detalles_pedido', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true
                ]
            ]);
        }
    }

    public function down()
    {
        //
    }
}