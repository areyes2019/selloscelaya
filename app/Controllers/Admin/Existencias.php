<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InventarioActualModel;
use App\Models\ArticulosModel;
use App\Services\InventarioService;

class Existencias extends BaseController
{
    protected $inventarioActualModel;
    protected $articulosModel;
    protected $inventarioService;
    protected $helpers = ['form', 'number'];

    public function __construct()
    {
        $this->inventarioActualModel = new InventarioActualModel();
        $this->articulosModel = new ArticulosModel();
        $this->inventarioService = new InventarioService();
    }

    // 🔥 LISTADO PRINCIPAL (YA CORRECTO)
    public function index()
    {
        $listaInventario = $this->inventarioActualModel->getInventarioConArticulos();

        $idsEnInventario = array_column($listaInventario, 'id_articulo');

        if (!empty($idsEnInventario)) {
            $articulosStock = $this->articulosModel
                ->whereNotIn('id_articulo', $idsEnInventario)
                ->findAll();
        } else {
            $articulosStock = $this->articulosModel->findAll();
        }

        $valorTotalInventario = 0;
        $valorNetoInventario = 0;

        foreach ($listaInventario as $item) {
            $stock = $item['stock'] ?? 0;
            $precio_pub = $item['precio_pub'] ?? 0;
            $precio_prov = $item['precio_prov'] ?? 0;

            $valorTotalInventario += $precio_pub * $stock;
            $valorNetoInventario += $precio_prov * $stock;
        }

        $valorUtilidades = $valorTotalInventario - $valorNetoInventario;

        $data = [
            'lista' => $listaInventario,
            'articulos_stock' => $articulosStock,
            'valor_total_inventario' => $valorTotalInventario,
            'valor_neto_inventario' => $valorNetoInventario,
            'valor_utilidades' => $valorUtilidades,
            'titulo_pagina' => 'Gestión de Existencias',
            'success' => session()->getFlashdata('success'),
            'error' => session()->getFlashdata('error'),
            'errors' => session()->getFlashdata('errors')
        ];

        return view('Panel/existencias', $data);
    }

    // 🟢 ENTRADA DE INVENTARIO (desde modal)
    public function crear()
    {
        try {
            $id_articulo = $this->request->getPost('id_articulo');
            $cantidad = (int) $this->request->getPost('cantidad_' . $id_articulo);

            if ($cantidad <= 0) {
                return redirect()->back()->with('error', 'Cantidad inválida');
            }

            $this->inventarioService->entrada(
                $id_articulo,
                $cantidad,
                0,
                'ENTRADA MANUAL'
            );

            return redirect()->back()->with('success', 'Inventario agregado correctamente');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // 🔧 AJUSTE DE INVENTARIO (modal editar)
    public function ajustar()
    {
        try {
            $id_articulo = $this->request->getPost('id_articulo');
            $nuevaCantidad = (int) $this->request->getPost('cantidad');

            $stockActual = $this->inventarioActualModel->getStock($id_articulo);

            $diferencia = $nuevaCantidad - $stockActual;

            if ($diferencia > 0) {
                $this->inventarioService->entrada(
                    $id_articulo,
                    $diferencia,
                    0,
                    'AJUSTE +'
                );
            } elseif ($diferencia < 0) {
                $this->inventarioService->salida(
                    $id_articulo,
                    abs($diferencia),
                    0,
                    'AJUSTE -'
                );
            }

            return redirect()->back()->with('success', 'Inventario ajustado correctamente');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // 🚫 ELIMINAR YA NO APLICA
    public function eliminar($id_articulo = null)
    {
        return redirect()->back()->with('error', 'No se puede eliminar inventario. Usa ajuste.');
    }

    // ⚡ EDICIÓN RÁPIDA (para Vue)
    public function edicion_rapida($id_articulo)
    {
        $stock = $this->inventarioActualModel->getStock($id_articulo);

        $articulo = $this->articulosModel
            ->where('id_articulo', $id_articulo)
            ->first();

        if (!$articulo) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Artículo no encontrado',
                'flag' => 0
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Consulta realizada con éxito',
            'flag' => 1,
            'data' => [
                'id_articulo' => $id_articulo,
                'cantidad' => $stock,
                'minimo' => $articulo['minimo'] ?? 0
            ]
        ]);
    }

    // ⚡ GUARDAR RÁPIDO (AJUSTE DESDE VUE)
    public function guardar_rapido()
    {
        $id_articulo = $this->request->getVar('id_articulo');
        $nuevaCantidad = (int) $this->request->getVar('cantidad');

        try {
            $stockActual = $this->inventarioActualModel->getStock($id_articulo);
            $diferencia = $nuevaCantidad - $stockActual;

            if ($diferencia > 0) {
                $this->inventarioService->entrada($id_articulo, $diferencia, 0, 'AJUSTE RÁPIDO +');
            } elseif ($diferencia < 0) {
                $this->inventarioService->salida($id_articulo, abs($diferencia), 0, 'AJUSTE RÁPIDO -');
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Inventario actualizado correctamente',
                'flag' => 1
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
                'flag' => 0
            ]);
        }
    }
}