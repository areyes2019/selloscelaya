<?php

namespace App\Cells;

use CodeIgniter\View\Cells\Cell;
use App\Models\CategoriasModel;

class CategoryMenuCell extends Cell
{
    // Puedes tener propiedades si la celda necesita parámetros
    // public $parametro;

    public function render(): string
    {
        $categoriasModel = new CategoriasModel();
        $data['categorias'] = $categoriasModel->orderBy('nombre', 'ASC')->findAll();
        return view('cells/category_menu', $data); // Vista específica para la celda
    }
}