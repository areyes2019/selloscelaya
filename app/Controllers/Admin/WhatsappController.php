<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\WhatsappService;
use CodeIgniter\API\ResponseTrait;

class WhatsappController extends BaseController
{
    use ResponseTrait;

    protected WhatsappService $whatsapp;

    public function __construct()
    {
        $this->whatsapp = new WhatsappService();
    }

    public function send()
    {
        $input = $this->request->getJSON(true);
        $tipo  = $input['tipo'] ?? '';
        $id    = (int) ($input['id'] ?? 0);

        if (!$tipo || !$id) {
            return $this->respond(['success' => false, 'message' => 'Parámetros inválidos'], 400);
        }

        [$telefono, $template, $vars] = match ($tipo) {
            'cotizacion'   => $this->datosCotizacion($id),
            'factura'      => $this->datosFactura($id),
            'orden_compra' => $this->datosOrdenCompra($id),
            default        => [null, null, null],
        };

        if (!$telefono) {
            return $this->respond(['success' => false, 'message' => 'Registro no encontrado o sin teléfono registrado'], 404);
        }

        $result = $this->whatsapp->enviarTemplate($telefono, $template, $vars);

        if (!$result['success']) {
            log_message('error', '[WhatsApp] Error al enviar (' . $tipo . ' #' . $id . '): ' . $result['message']);
            return $this->respond(['success' => false, 'message' => $result['message']], 500);
        }

        return $this->respond(['success' => true, 'message' => 'Mensaje enviado correctamente']);
    }

    public function test()
    {
        $result = $this->whatsapp->enviarHelloWorld('4612901439');

        if (!$result['success']) {
            log_message('error', '[WhatsApp Test] ' . $result['message']);
            return $this->respond(['success' => false, 'message' => $result['message']], 500);
        }

        return $this->respond(['success' => true, 'message' => 'Mensaje de prueba enviado correctamente']);
    }

    private function datosCotizacion(int $id): array
    {
        $db  = \Config\Database::connect();
        $row = $db->table('sellopro_cotizaciones c')
            ->select('c.id_cotizacion, c.total, cl.telefono')
            ->join('sellopro_clientes cl', 'cl.id_cliente = c.cliente')
            ->where('c.id_cotizacion', $id)
            ->get()->getRowArray();

        if (!$row || empty($row['telefono'])) return [null, null, null];

        return [
            $row['telefono'],
            'notificacion_cotizacion',
            [$row['id_cotizacion'], number_format((float) $row['total'], 2)],
        ];
    }

    private function datosFactura(int $id): array
    {
        $db  = \Config\Database::connect();
        $row = $db->table('sellopro_facturas f')
            ->select('f.folio, f.serie, f.monto, cl.telefono')
            ->join('sellopro_cotizaciones cot', 'cot.id_cotizacion = f.cotizacion_id')
            ->join('sellopro_clientes cl', 'cl.id_cliente = cot.cliente')
            ->where('f.id', $id)
            ->get()->getRowArray();

        if (!$row || empty($row['telefono'])) return [null, null, null];

        $folio = ($row['serie'] ?? '') . '-' . ($row['folio'] ?? '');
        return [
            $row['telefono'],
            'notificacion_factura',
            [$folio, number_format((float) $row['monto'], 2)],
        ];
    }

    private function datosOrdenCompra(int $id): array
    {
        $db  = \Config\Database::connect();
        $row = $db->table('sellopro_pedidos p')
            ->select('p.id_pedido, p.total, prov.telefono')
            ->join('sellopro_proveedores prov', 'prov.id_proveedor = p.proveedor')
            ->where('p.id_pedido', $id)
            ->get()->getRowArray();

        if (!$row || empty($row['telefono'])) return [null, null, null];

        return [
            $row['telefono'],
            'notificacion_orden_compra',
            [$row['id_pedido'], number_format((float) $row['total'], 2)],
        ];
    }
}
