<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\CsvImportClientesService;
use CodeIgniter\API\ResponseTrait;

class ImportClientesController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        return view('Panel/import_clientes');
    }

    /**
     * POST /clientes/importar_csv
     * Recibe el CSV de Facturama y delega el procesamiento al servicio.
     */
    public function importar()
    {
        $file = $this->request->getFile('archivo_csv');

        if (!$file || !$file->isValid()) {
            return $this->respond(['ok' => false, 'message' => 'No se recibió ningún archivo.'], 400);
        }

        if (strtolower($file->getClientExtension()) !== 'csv') {
            return $this->respond(['ok' => false, 'message' => 'El archivo debe tener extensión .csv'], 422);
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            return $this->respond(['ok' => false, 'message' => 'El archivo supera el límite de 10 MB.'], 422);
        }

        try {
            $service = new CsvImportClientesService();
            $result  = $service->processCsvFile($file->getTempName());

            return $this->respond([
                'ok'    => true,
                'stats' => $result['stats'],
                'log'   => $result['log'],
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[ImportClientes] ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->respond([
                'ok'      => false,
                'message' => 'Error interno al procesar el archivo: ' . $e->getMessage(),
            ], 500);
        }
    }
}
