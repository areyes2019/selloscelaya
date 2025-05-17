<?php

namespace App\Controllers;
use App\Models\CategoriasModel;
use App\Models\ArticulosModel;

class Home extends BaseController
{
    
    public function index()
    {
        return view('home');
    }
    public function articulos($valueFromUrl) // Renombrado para claridad
    {
        // 1. Decodificar el valor de la URL (convierte %20 a espacios, etc.)
        $decodedValue = urldecode($valueFromUrl);
        // 2. Convertir a minúsculas y reemplazar guiones por espacios
        // Esto manejará tanto "nombre-categoria" como "nombre categoria" (después de urldecode)
        $searchTerm = strtolower(str_replace('-', ' ', $decodedValue));

        // 3. Buscar la categoría específica en la base de datos
        $categoriasModel = new CategoriasModel();
        $db = \Config\Database::connect(); // Para escapeLikeString

        // Usamos el searchTerm limpio para la búsqueda
        $categoria = $categoriasModel->like('LOWER(nombre)', $db->escapeLikeString($searchTerm), 'both', true, true)
                                     ->first();
        // O si prefieres búsqueda exacta después de limpiar el slug:
        // $categoria = $categoriasModel->where('LOWER(nombre)', $searchTerm)->first();


        if (!$categoria) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Categoría no encontrada para: ' . esc($searchTerm) . ' (Original URL: ' . esc($valueFromUrl) . ')');
        }

        // 4. Buscar artículos visibles de esta categoría específica
        $articulosModel = new ArticulosModel();
        $articulos = $articulosModel->where('visible', 1)
                                   ->where('categoria', $categoria['id_categoria'])
                                   ->orderBy('nombre', 'ASC')
                                   ->findAll();
        // 5. Preparar datos para la vista 'articulos.php'
        $data = [
            'categoria_actual' => $categoria,
            'articulos'        => $articulos,
            'searchTerm'       => $searchTerm,
            'page_title'       => 'Artículos de ' . esc($categoria['nombre'])
        ];

        return view('articulos', $data);
    }
    public function autoentintables()
    {
        return view('autoentitables');
    }
    public function madera(): string
    {
        return view('madera');
    }
    public function portatiles(): string
    {
        return view('portatiles');
    }
    public function gigantes(): string
    {
        return view('gigantes');
    }
    public function fechadores(): string
    {
        return view('fechadores');
    }
    public function textiles(): string
    {
        return view('textil');
    }
    public function catalogo()
    {
        return view('catalogo');
    }
    public function contacto()
    {
        $correo = $this->request->getPost('correo');
        $nombre = $this->request->getPost('nombre');
        $texto = $this->request->getPost('texto');
        
        $email = \Config\Services::email();
        $email->setFrom($correo, $nombre);
        $email->setTo('ventas@selloscelaya.com');
        $email->setSubject('Quiero más información');
        $email->setMessage($texto);
        $email->send();
        return view('home');
    }
}
