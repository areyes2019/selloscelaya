<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CotizacionesModel;
use App\Models\ClientesModel;
use App\Models\DetalleModel;
use App\Models\ArticulosModel;
use App\Models\FacturaModel;
use CodeIgniter\API\ResponseTrait;

/**
 * FacturasController
 * 
 * Convierte una cotización de Sellopro en un CFDI 4.0 timbrado
 * usando la API de Facturama (https://facturama.mx).
 * 
 * Requiere en .env:
 *   FACTURAMA_USER      = tu_usuario_de_facturama
 *   FACTURAMA_PASSWORD  = tu_password_de_facturama
 *   FACTURAMA_SANDBOX   = true   # false en producción
 *   FACTURAMA_CP        = 38000  # CP de expedición (tu sucursal)
 *   FACTURAMA_SERIE     = A      # Serie del CFDI (opcional)
 * 
 * En clientes se requieren: tax_id (RFC), regimen_fiscal, codigo_postal
 * En artículos se requiere:  clave_producto (clave SAT del producto/servicio)
 */
class FacturasController extends BaseController
{
    use ResponseTrait;

    // ------------------------------------------------------------------
    // URLs de la API
    // ------------------------------------------------------------------
    private const URL_PRODUCCION = 'https://api.facturama.mx';
    private const URL_SANDBOX    = 'https://apisandbox.facturama.mx';

    private string $baseUrl;
    private string $authHeader;

    // ------------------------------------------------------------------
    // Modelos
    // ------------------------------------------------------------------
    protected CotizacionesModel $cotizacionesModel;
    protected ClientesModel     $clientesModel;
    protected DetalleModel      $detalleModel;
    protected ArticulosModel    $articulosModel;
    protected FacturaModel      $facturaModel;

    public function __construct()
    {
        helper(['form', 'url']);

        $this->cotizacionesModel = new CotizacionesModel();
        $this->clientesModel     = new ClientesModel();
        $this->detalleModel      = new DetalleModel();
        $this->articulosModel    = new ArticulosModel();
        $this->facturaModel      = new FacturaModel();

        // Elegir entorno según .env
        $sandbox = filter_var(env('FACTURAMA_SANDBOX', true), FILTER_VALIDATE_BOOLEAN);
        $this->baseUrl = $sandbox ? self::URL_SANDBOX : self::URL_PRODUCCION;

        // Armar cabecera de autenticación Basic
        $user     = env('FACTURAMA_USER', '');
        $password = env('FACTURAMA_PASSWORD', '');
        $this->authHeader = 'Basic ' . base64_encode("{$user}:{$password}");
    }

    // ==================================================================
    // LISTADO
    // ==================================================================

    /**
     * Muestra todas las facturas generadas.
     */
    public function index()
    {
        $db      = \Config\Database::connect();
        $facturas = $db->table('sellopro_facturas f')
            ->select('f.*, c.nombre as nombre_cliente, cot.total as total_cotizacion')
            ->join('sellopro_cotizaciones cot', 'cot.id_cotizacion = f.cotizacion_id')
            ->join('sellopro_clientes c',       'c.id_cliente = cot.cliente')
            ->orderBy('f.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('Panel/facturas', ['facturas' => $facturas]);
    }

    // ==================================================================
    // TIMBRAR  (cotización → CFDI 4.0)
    // ==================================================================

    /**
     * Timbra una cotización pagada como CFDI 4.0 en Facturama.
     *
     * POST /facturas/timbrar
     * Body JSON: { "id_cotizacion": 42, "forma_pago": "03", "metodo_pago": "PUE", "uso_cfdi": "G03" }
     */
    public function timbrar()
    {
        // ── 1. Validar entrada ─────────────────────────────────────────
        $input = $this->request->getJSON(true);

        $idCotizacion = (int) ($input['id_cotizacion'] ?? 0);
        $formaPago    = $input['forma_pago']  ?? '03';  // 03 = Transferencia
        $metodoPago   = $input['metodo_pago'] ?? 'PUE'; // PUE = Pago en una sola exhibición
        $usoCfdi      = $input['uso_cfdi']    ?? 'G03'; // G03 = Gastos en general

        if (!$idCotizacion) {
            return $this->respond(['status' => 'error', 'message' => 'ID de cotización requerido'], 400);
        }

        // ── 2. Verificar que no esté ya facturada ──────────────────────
        $facturaExistente = $this->facturaModel
            ->where('cotizacion_id', $idCotizacion)
            ->first();

        if ($facturaExistente) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'Esta cotización ya fue timbrada.',
                'factura' => $facturaExistente,
            ], 409);
        }

        // ── 3. Obtener cotización ──────────────────────────────────────
        $cotizacion = $this->cotizacionesModel->find($idCotizacion);
        if (!$cotizacion) {
            return $this->respond(['status' => 'error', 'message' => 'Cotización no encontrada'], 404);
        }

        // ── 4. Obtener cliente con datos fiscales ──────────────────────
        $cliente = $this->clientesModel->find($cotizacion['cliente']);
        if (!$cliente) {
            return $this->respond(['status' => 'error', 'message' => 'Cliente no encontrado'], 404);
        }

        // Validar datos fiscales mínimos del cliente
        $camposFiscales = ['tax_id', 'regimen_fiscal', 'codigo_postal'];
        foreach ($camposFiscales as $campo) {
            if (empty($cliente[$campo])) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => "El cliente no tiene el campo fiscal '{$campo}' configurado. Actualízalo en el módulo de clientes.",
                ], 422);
            }
        }

        // ── 5. Obtener detalles de la cotización ───────────────────────
        $detalles = $this->detalleModel
            ->where('id_cotizacion', $idCotizacion)
            ->findAll();

        if (empty($detalles)) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'La cotización no tiene artículos.',
            ], 422);
        }

        // ── 6. Construir los Items del CFDI ────────────────────────────
        $items = $this->buildItems($detalles);
        if (isset($items['error'])) {
            return $this->respond(['status' => 'error', 'message' => $items['error']], 422);
        }

        // ── 7. Armar el payload para Facturama ─────────────────────────
        $payload = [
            'NameId'          => 1,           // 1 = "Factura"
            'CfdiType'        => 'I',          // I = Ingreso
            'PaymentForm'     => $formaPago,
            'PaymentMethod'   => $metodoPago,
            'ExpeditionPlace' => env('FACTURAMA_CP', '38000'),
            'Currency'        => 'MXN',
            'Exportation'     => '01',         // 01 = No aplica
            'Folio'           => (string) $idCotizacion,
            'Serie'           => env('FACTURAMA_SERIE', 'QT'),
            'OrderNumber'     => 'QT-' . $idCotizacion,
            'Observations'    => 'Cotización QT-' . $idCotizacion,
            'Receiver'        => [
                'Rfc'          => strtoupper(trim($cliente['tax_id'])),
                'Name'         => strtoupper(trim($cliente['nombre'])),
                'CfdiUse'      => $usoCfdi,
                'FiscalRegime' => (string) $cliente['regimen_fiscal'],
                'TaxZipCode'   => (string) $cliente['codigo_postal'],
            ],
            'Items' => $items,
        ];

        // ── 8. Llamar a la API de Facturama ────────────────────────────
        $apiResponse = $this->callFacturama('POST', '/3/cfdis', $payload);

        if (!$apiResponse['success']) {
            log_message('error', '[Facturama] Error al timbrar cotización ' . $idCotizacion . ': ' . json_encode($apiResponse));
            return $this->respond([
                'status'  => 'error',
                'message' => 'Error al timbrar en Facturama: ' . ($apiResponse['message'] ?? 'Error desconocido'),
                'detalle' => $apiResponse['body'] ?? null,
            ], 500);
        }

        $cfdi = $apiResponse['body'];

        // ── 9. Guardar factura en la BD ────────────────────────────────
        $this->facturaModel->insert([
            'cotizacion_id'     => $idCotizacion,
            'factura_uuid'      => $cfdi['Id'],
            'folio'             => $cfdi['Folio']  ?? null,
            'serie'             => $cfdi['Serie']  ?? null,
            'estado'            => 'timbrada',
            'fecha_timbrado'    => date('Y-m-d H:i:s'),
            'respuesta_completa'=> json_encode($cfdi),
            'created_at'        => date('Y-m-d H:i:s'),
        ]);

        // Marcar cotización como entregada/facturada
        $this->cotizacionesModel->update($idCotizacion, ['entregada' => 1]);

        return $this->respond([
            'status'       => 'success',
            'message'      => 'CFDI timbrado correctamente',
            'cfdi_id'      => $cfdi['Id'],
            'serie_folio'  => ($cfdi['Serie'] ?? '') . '-' . ($cfdi['Folio'] ?? ''),
            'total'        => $cfdi['Total'] ?? null,
            'uuid'         => $cfdi['Complement']['TaxStamp']['Uuid'] ?? null,
        ]);
    }

    // ==================================================================
    // DESCARGAR PDF / XML
    // ==================================================================

    /**
     * Descarga el PDF o XML de una factura timbrada.
     *
     * GET /facturas/descargar/{id}/{formato}
     * $formato: "pdf" | "xml"
     */
    public function descargar(int $id, string $formato = 'pdf')
    {
        $factura = $this->facturaModel->find($id);
        if (!$factura) {
            return redirect()->back()->with('error', 'Factura no encontrada');
        }

        $formato = strtolower($formato);
        if (!in_array($formato, ['pdf', 'xml'])) {
            return redirect()->back()->with('error', 'Formato no válido. Use pdf o xml.');
        }

        // GET /cfdi/{formato}/issued/{uuid}
        $endpoint = "/cfdi/{$formato}/issued/{$factura['factura_uuid']}";
        $apiResponse = $this->callFacturama('GET', $endpoint);

        if (!$apiResponse['success']) {
            return redirect()->back()->with('error', 'No se pudo obtener el archivo de Facturama.');
        }

        // La respuesta es un objeto con { ContentType, Content (base64), FileName }
        $archivo = $apiResponse['body'];
        $contenido = base64_decode($archivo['Content']);
        $nombreArchivo = $archivo['FileName'] ?? "factura-{$id}.{$formato}";
        $contentType = $formato === 'pdf' ? 'application/pdf' : 'text/xml';

        return $this->response
            ->setHeader('Content-Type', $contentType)
            ->setHeader('Content-Disposition', "attachment; filename=\"{$nombreArchivo}\"")
            ->setBody($contenido);
    }

    // ==================================================================
    // ENVIAR POR CORREO
    // ==================================================================

    /**
     * Envía la factura por correo usando la API de Facturama.
     *
     * POST /facturas/enviar_correo
     * Body JSON: { "id_factura": 5, "correo": "cliente@email.com" }
     */
    public function enviarCorreo()
    {
        $input      = $this->request->getJSON(true);
        $idFactura  = (int) ($input['id_factura'] ?? 0);
        $correo     = trim($input['correo'] ?? '');

        if (!$idFactura || !$correo) {
            return $this->respond(['status' => 'error', 'message' => 'ID de factura y correo son requeridos'], 400);
        }

        $factura = $this->facturaModel->find($idFactura);
        if (!$factura) {
            return $this->respond(['status' => 'error', 'message' => 'Factura no encontrada'], 404);
        }

        // POST /cfdi/issed/{uuid}/email/{correo}
        // Facturama acepta: /cfdi/{cfdiType}/{cfdiId}/{email}/{subject}/{comments}
        $cfdiId  = urlencode($factura['factura_uuid']);
        $asunto  = urlencode('Su factura electrónica - QT-' . $factura['cotizacion_id']);
        $endpoint = "/cfdi/issued/{$cfdiId}/{$correo}/{$asunto}";

        $apiResponse = $this->callFacturama('POST', $endpoint);

        if (!$apiResponse['success']) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'No se pudo enviar el correo: ' . ($apiResponse['message'] ?? ''),
            ], 500);
        }

        return $this->respond([
            'status'  => 'success',
            'message' => "Factura enviada correctamente a {$correo}",
        ]);
    }

    // ==================================================================
    // CANCELAR
    // ==================================================================

    /**
     * Cancela un CFDI ante el SAT (a través de Facturama).
     *
     * POST /facturas/cancelar
     * Body JSON: { "id_factura": 5, "motivo": "02" }
     * 
     * Motivos SAT: 01=Comprobante emitido con errores con relación, 02=Comprobante emitido con errores sin relación,
     *              03=No se llevó a cabo la operación, 04=Operación nominativa relacionada en la factura global
     */
    public function cancelar()
    {
        $input     = $this->request->getJSON(true);
        $idFactura = (int) ($input['id_factura'] ?? 0);
        $motivo    = $input['motivo'] ?? '02';

        if (!$idFactura) {
            return $this->respond(['status' => 'error', 'message' => 'ID de factura requerido'], 400);
        }

        $factura = $this->facturaModel->find($idFactura);
        if (!$factura) {
            return $this->respond(['status' => 'error', 'message' => 'Factura no encontrada'], 404);
        }

        if ($factura['estado'] === 'cancelada') {
            return $this->respond(['status' => 'error', 'message' => 'La factura ya fue cancelada'], 409);
        }

        // DELETE /cfdi/{id}?type=issued&motive={motivo}
        $cfdiId   = urlencode($factura['factura_uuid']);
        $endpoint = "/cfdi/{$cfdiId}?type=issued&motive={$motivo}";

        $apiResponse = $this->callFacturama('DELETE', $endpoint);

        if (!$apiResponse['success']) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'No se pudo cancelar: ' . ($apiResponse['message'] ?? ''),
                'detalle' => $apiResponse['body'] ?? null,
            ], 500);
        }

        // Actualizar estado en BD
        $this->facturaModel->update($idFactura, [
            'estado'     => 'cancelada',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Revertir el estatus de la cotización
        $this->cotizacionesModel->update($factura['cotizacion_id'], ['entregada' => 0]);

        return $this->respond([
            'status'  => 'success',
            'message' => 'CFDI cancelado correctamente',
        ]);
    }

    // ==================================================================
    // HELPERS PRIVADOS
    // ==================================================================

    /**
     * Construye el array de Items para el payload de Facturama
     * a partir de los detalles de la cotización.
     */
    private function buildItems(array $detalles): array
    {
        $items = [];

        foreach ($detalles as $detalle) {
            // Si id_articulo = 0 es una línea independiente (sin artículo del catálogo)
            $claveSat  = '01010101'; // Clave genérica SAT si no hay artículo
            $sku       = null;

            if (!empty($detalle['id_articulo'])) {
                $articulo = $this->articulosModel->find($detalle['id_articulo']);

                if ($articulo && !empty($articulo['clave_producto'])) {
                    $claveSat = $articulo['clave_producto'];
                    $sku      = $articulo['modelo'] ?? null;
                }
            }

            $precioUnitario = (float) $detalle['p_unitario'];
            $cantidad       = (float) $detalle['cantidad'];
            $subtotal       = round($precioUnitario * $cantidad, 6);
            $baseIva        = $subtotal;
            $montoIva       = round($baseIva * 0.16, 6);

            $items[] = [
                'ProductCode'         => $claveSat,
                'IdentificationNumber'=> $sku,
                'Description'         => $detalle['descripcion'] ?? 'Producto / Servicio',
                'Unit'                => 'Pieza',
                'UnitCode'            => 'H87',   // H87 = Pieza en catálogo SAT
                'UnitPrice'           => $precioUnitario,
                'Quantity'            => $cantidad,
                'Subtotal'            => $subtotal,
                'TaxObject'           => '02',    // 02 = Sí objeto de impuesto
                'Taxes'               => [
                    [
                        'Total'       => $montoIva,
                        'Name'        => 'IVA',
                        'Base'        => $baseIva,
                        'Rate'        => 0.16,
                        'IsRetention' => false,
                    ]
                ],
                'Total' => round($subtotal + $montoIva, 6),
            ];
        }

        return $items;
    }

    /**
     * Realiza una llamada HTTP a la API de Facturama.
     *
     * @param  string $method   GET | POST | DELETE
     * @param  string $endpoint Ruta sin base URL, ej: "/3/cfdis"
     * @param  array  $body     Datos a enviar como JSON (solo en POST/PUT)
     * @return array  ['success' => bool, 'body' => mixed, 'message' => string]
     */
    private function callFacturama(string $method, string $endpoint, array $body = []): array
    {
        $url    = $this->baseUrl . $endpoint;
        $method = strtoupper($method);

        $curl = curl_init();

        $headers = [
            'Authorization: ' . $this->authHeader,
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => filter_var(env('FACTURAMA_VERIFY_SSL', true), FILTER_VALIDATE_BOOLEAN),
        ];

        switch ($method) {
            case 'POST':
                $options[CURLOPT_POST]       = true;
                $options[CURLOPT_POSTFIELDS] = json_encode($body);
                break;

            case 'PUT':
                $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
                $options[CURLOPT_POSTFIELDS]    = json_encode($body);
                break;

            case 'DELETE':
                $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                break;

            case 'GET':
            default:
                // No body
                break;
        }

        curl_setopt_array($curl, $options);

        $rawResponse = curl_exec($curl);
        $httpCode    = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError   = curl_error($curl);
        curl_close($curl);

        if ($curlError) {
            log_message('error', "[Facturama] cURL error: {$curlError}");
            return [
                'success' => false,
                'message' => "Error de conexión: {$curlError}",
                'body'    => null,
            ];
        }

        $decoded = json_decode($rawResponse, true);

        // Facturama devuelve 200/201 en éxito
        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'body'    => $decoded,
                'message' => 'OK',
            ];
        }

        // Extraer mensaje de error de la respuesta de Facturama
        $errorMsg = $decoded['ModelState'][0]
            ?? $decoded['Message']
            ?? $decoded['message']
            ?? "HTTP {$httpCode}";

        log_message('error', "[Facturama] HTTP {$httpCode} en {$method} {$endpoint}: " . json_encode($decoded));

        return [
            'success' => false,
            'message' => $errorMsg,
            'body'    => $decoded,
        ];
    }
}