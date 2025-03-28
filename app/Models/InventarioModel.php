<?php 

namespace App\Models;
use CodeIgniter\Model;

class InventarioModel extends Model
{
    protected $table = 'sellopro_inventario';
    protected $primaryKey = 'id_entrada';
    protected $allowedFields = [
        'id_articulo',
        'cantidad',
        'total',
    ];
    public function obtenerProducto($id_articulo)
    {
        return $this->where('id_articulo', $id_articulo)->first();
    }

    public function incrementarCantidad($id, $cantidad, $precio)
    {

        $producto = $this->find($id); //este id es el id de movimiento
        $nuevaCantidad = $producto['cantidad'] + $cantidad;
        $nuevoPrecioTotal = $producto['total'] + ($precio * $cantidad);

        $this->update($id, [
            'cantidad' => $nuevaCantidad,
            'total' => $nuevoPrecioTotal
        ]);

        /*$this->update($id, [
            'cantidad' => $this->find($id)['cantidad'] + $cantidad
        ]);*/
    }
   
}