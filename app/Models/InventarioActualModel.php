<?php

namespace App\Models;

use CodeIgniter\Model;

class InventarioActualModel extends Model
{
    protected $table = 'sellopro_inventario_actual';
    protected $primaryKey = 'id_articulo';

    protected $allowedFields = [
        'id_articulo',
        'stock',
        'updated_at'
    ];

    public function getStock($id_articulo)
    {
        return $this->where('id_articulo', $id_articulo)
                    ->first()['stock'] ?? 0;
    }

    public function aumentarStock($id_articulo, $cantidad)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        $existe = $builder->where('id_articulo', $id_articulo)->get()->getRow();

        if ($existe) {
            $builder->set('stock', 'stock + '.$cantidad, false)
                    ->set('updated_at', date('Y-m-d H:i:s'))
                    ->where('id_articulo', $id_articulo)
                    ->update();
        } else {
            $builder->insert([
                'id_articulo' => $id_articulo,
                'stock' => $cantidad,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function disminuirStock($id_articulo, $cantidad)
    {
        $db = \Config\Database::connect();

        $db->table($this->table)
           ->set('stock', 'stock - '.$cantidad, false)
           ->set('updated_at', date('Y-m-d H:i:s'))
           ->where('id_articulo', $id_articulo)
           ->update();
    }
    public function getInventarioConArticulos()
    {
        return $this->select('
                sellopro_inventario_actual.id_articulo,
                sellopro_inventario_actual.stock,
                sa.nombre,
                sa.modelo,
                sa.precio_pub,
                sa.precio_prov,
                sa.minimo
            ')
            ->join('sellopro_articulos sa', 'sa.id_articulo = sellopro_inventario_actual.id_articulo')
            ->findAll();
    }
}