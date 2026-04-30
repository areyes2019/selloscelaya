<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImportFieldsToClientes extends Migration
{
    public function up(): void
    {
        $existing = $this->db->getFieldNames('sellopro_clientes');

        $fields = [
            'nombre_normalizado' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
                'default'    => null,
                'after'      => 'nombre',
            ],
            'uso_cfdi' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => null,
                'after'      => 'municipio',
            ],
            'num_reg_id_trib' => [
                'type'       => 'VARCHAR',
                'constraint' => 40,
                'null'       => true,
                'default'    => null,
                'after'      => 'uso_cfdi',
            ],
            'tax_residence' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => null,
                'after'      => 'num_reg_id_trib',
            ],
            'pais' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'after'      => 'tax_residence',
            ],
        ];

        foreach ($fields as $col => $def) {
            if (!in_array($col, $existing)) {
                $this->forge->addColumn('sellopro_clientes', [$col => $def]);
            }
        }
    }

    public function down(): void
    {
        foreach (['nombre_normalizado', 'uso_cfdi', 'num_reg_id_trib', 'tax_residence', 'pais'] as $col) {
            $this->forge->dropColumn('sellopro_clientes', $col);
        }
    }
}
