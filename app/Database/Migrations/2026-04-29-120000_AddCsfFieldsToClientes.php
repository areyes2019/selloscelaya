<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCsfFieldsToClientes extends Migration
{
    public function up(): void
    {
        $fields = [
            'tax_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => null,
                'after'      => 'descuento',
            ],
            'regimen_fiscal' => [
                'type'    => 'INT',
                'null'    => true,
                'default' => null,
                'after'   => 'tax_id',
            ],
            'codigo_postal' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => null,
                'after'      => 'regimen_fiscal',
            ],
            'calle' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'default'    => null,
                'after'      => 'codigo_postal',
            ],
            'no_ext' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => null,
                'after'      => 'calle',
            ],
            'no_int' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => null,
                'after'      => 'no_ext',
            ],
            'colonia' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'default'    => null,
                'after'      => 'no_int',
            ],
            'municipio' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'colonia',
            ],
        ];

        // Solo agrega columnas que no existen todavía
        $existing = $this->db->getFieldNames('sellopro_clientes');

        foreach ($fields as $col => $def) {
            if (!in_array($col, $existing)) {
                $this->forge->addColumn('sellopro_clientes', [$col => $def]);
            }
        }

        // Si existe la columna codigo_postal como INT, convertirla a VARCHAR
        // para soportar CPs con cero a la izquierda (ej: 06600)
        if (in_array('codigo_postal', $existing)) {
            $this->forge->modifyColumn('sellopro_clientes', [
                'codigo_postal' => [
                    'name'       => 'codigo_postal',
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'null'       => true,
                    'default'    => null,
                ],
            ]);
        }
    }

    public function down(): void
    {
        $drop = ['calle', 'no_ext', 'no_int', 'colonia', 'municipio'];
        foreach ($drop as $col) {
            $this->forge->dropColumn('sellopro_clientes', $col);
        }
    }
}
