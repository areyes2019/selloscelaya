<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CotizacionesModel;
use App\Models\ClientesModel;
use App\Models\DetalleModel;
use App\Models\ArticulosModel;
use App\Models\FacturaModel;
use App\Services\FacturapiService;
use CodeIgniter\API\ResponseTrait;
use Dompdf\Dompdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

/**
 * FacturasController
 *
 * Convierte una cotización en un CFDI 4.0 timbrado usando FacturAPI v2.
 * Documentación: https://docs.facturapi.io
 *
 * Variables de entorno requeridas (.env):
 *   FACTURAPI_KEY   = sk_test_xxxx   (sk_live_xxxx en producción)
 *   FACTURAPI_SERIE = QT             (configurable también en el dashboard)
 *   FACTURAPI_CP    = 38000          (lugar de expedición, se configura en el dashboard)
 *
 * Datos requeridos en el cliente: tax_id (RFC), regimen_fiscal, codigo_postal
 * Datos requeridos en artículos:  clave_producto (clave SAT)
 */
class FacturasController extends BaseController
{
    use ResponseTrait;

    protected CotizacionesModel $cotizacionesModel;
    protected ClientesModel     $clientesModel;
    protected DetalleModel      $detalleModel;
    protected ArticulosModel    $articulosModel;
    protected FacturaModel      $facturaModel;
    protected FacturapiService  $facturapi;

    public function __construct()
    {
        helper(['form', 'url']);

        $this->cotizacionesModel = new CotizacionesModel();
        $this->clientesModel     = new ClientesModel();
        $this->detalleModel      = new DetalleModel();
        $this->articulosModel    = new ArticulosModel();
        $this->facturaModel      = new FacturaModel();
        $this->facturapi         = new FacturapiService();
    }

    // ==================================================================
    // LISTADO
    // ==================================================================

    public function index()
    {
        $db      = \Config\Database::connect();
        $facturas = $db->table('sellopro_facturas f')
            ->select('f.*, c.nombre as nombre_cliente, cot.total as total_cotizacion')
            ->join('sellopro_cotizaciones cot', 'cot.id_cotizacion = f.cotizacion_id')
            ->join('sellopro_clientes c',       'c.id_cliente = cot.cliente')
            ->orderBy('f.id', 'ASC')
            ->get()
            ->getResultArray();

        return view('Panel/facturas', ['facturas' => $facturas]);
    }

    // ==================================================================
    // TIMBRAR  (cotización → CFDI 4.0)
    // ==================================================================

    /**
     * Timbra una cotización pagada como CFDI 4.0 en FacturAPI.
     *
     * POST /facturas/timbrar
     * Body JSON: {
     *   "id_cotizacion": 42,
     *   "forma_pago": "03",
     *   "metodo_pago": "PUE",
     *   "uso_cfdi": "G03"
     * }
     *
     * Formas de pago comunes: 01=Efectivo, 03=Transferencia, 04=Tarjeta crédito, 28=Tarjeta débito
     * Métodos de pago: PUE=Pago único, PPD=Pago en parcialidades
     * Uso CFDI: G01=Adquisición, G03=Gastos generales, D10=Pagos por servicios profesionales
     */
    public function timbrar()
    {
        // ── 1. Validar entrada ─────────────────────────────────────────
        $input = $this->request->getJSON(true);

        $idCotizacion = (int) ($input['id_cotizacion'] ?? 0);
        $formaPago    = $input['forma_pago']  ?? '03';
        $metodoPago   = $input['metodo_pago'] ?? 'PUE';
        $usoCfdi      = $input['uso_cfdi']    ?? 'G03';

        if (!$idCotizacion) {
            return $this->respond(['status' => 'error', 'message' => 'ID de cotización requerido'], 400);
        }

        // ── 2. Verificar que no esté ya facturada ──────────────────────
        $facturaExistente = $this->facturaModel
            ->where('cotizacion_id', $idCotizacion)
            ->where('estado !=', 'cancelada')
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

        foreach (['tax_id', 'regimen_fiscal', 'codigo_postal'] as $campo) {
            if (empty($cliente[$campo])) {
                return $this->respond([
                    'status'  => 'error',
                    'message' => "El cliente no tiene el campo fiscal '{$campo}'. Actualízalo en el módulo de clientes.",
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

        // ── 6. Construir los items en formato FacturAPI ────────────────
        $items = $this->buildItems($detalles);
        if (isset($items['error'])) {
            return $this->respond(['status' => 'error', 'message' => $items['error']], 422);
        }

        // ── 7. Armar el payload para FacturAPI ─────────────────────────
        $payload = [
            'type'           => 'I',
            'customer'       => [
                'legal_name' => strtoupper(trim($cliente['nombre'])),
                'tax_id'     => strtoupper(trim($cliente['tax_id'])),
                'tax_system' => (string) $cliente['regimen_fiscal'],
                'address'    => [
                    'zip' => (string) $cliente['codigo_postal'],
                ],
            ],
            'use'            => $usoCfdi,
            'payment_form'   => $formaPago,
            'payment_method' => $metodoPago,
            'currency'       => 'MXN',
            'items'          => $items,
        ];

        // Agregar correo del cliente si está disponible (FacturAPI lo usa para envío automático)
        if (!empty($cliente['correo'])) {
            $payload['customer']['email'] = $cliente['correo'];
        }

        // Agregar serie si está configurada
        $serie = env('FACTURAPI_SERIE', '');
        if ($serie) {
            $payload['series'] = $serie;
        }

        // ── 8. Llamar a FacturAPI ──────────────────────────────────────
        $db = \Config\Database::connect();
        $db->transStart();

        $apiResponse = $this->facturapi->crearFactura($payload);

        if (!$apiResponse['success']) {
            $db->transRollback();
            log_message('error', '[FacturAPI] Error al timbrar cotización ' . $idCotizacion . ': ' . json_encode($apiResponse));
            return $this->respond([
                'status'  => 'error',
                'message' => 'Error al timbrar en FacturAPI: ' . ($apiResponse['message'] ?? 'Error desconocido'),
                'detalle' => $apiResponse['body'] ?? null,
            ], 500);
        }

        $cfdi = $apiResponse['body'];

        // ── 9. Guardar factura en BD (dentro de la transacción) ────────
        $this->facturaModel->insert([
            'cotizacion_id'      => $idCotizacion,
            'factura_uuid'       => $cfdi['id'],                  // ID de FacturAPI (para descargas/cancelación)
            'folio'              => $cfdi['folio_number'] ?? null,
            'serie'              => $cfdi['series']       ?? null,
            'estado'             => $cfdi['status']       ?? 'valid',
            'fecha_timbrado'     => date('Y-m-d H:i:s'),
            'monto'              => $cfdi['total']         ?? null,
            'respuesta_completa' => json_encode($cfdi),
        ]);

        $this->cotizacionesModel->update($idCotizacion, ['estado_comercial' => 'facturada']);

        $db->transComplete();

        if (!$db->transStatus()) {
            log_message('error', '[FacturAPI] Factura timbrada pero falló la escritura en BD. CFDI ID: ' . $cfdi['id']);
            return $this->respond([
                'status'  => 'error',
                'message' => 'El CFDI fue timbrado pero ocurrió un error al guardarlo. Contacta al administrador con este ID: ' . $cfdi['id'],
            ], 500);
        }

        return $this->respond([
            'status'      => 'success',
            'message'     => 'CFDI timbrado correctamente',
            'cfdi_id'     => $cfdi['id'],
            'serie_folio' => ($cfdi['series'] ?? '') . '-' . ($cfdi['folio_number'] ?? ''),
            'total'       => $cfdi['total'] ?? null,
            'uuid'        => $cfdi['uuid']  ?? null,
            'livemode'    => $cfdi['livemode'] ?? false,
        ]);
    }

    // ==================================================================
    // PDF LOCAL  (mismo estilo que cotizaciones)
    // ==================================================================

    /**
     * GET /facturas/pdf/{id}
     * Genera el PDF con DomPDF usando la plantilla PDF_factura.php.
     */
    public function pdfLocal(int $id)
    {
        $db = \Config\Database::connect();
        $row = $db->table('sellopro_facturas f')
            ->select('f.*, cl.nombre as nombre_cliente, cl.tax_id as cliente_rfc,
                      cl.correo as cliente_email, cl.codigo_postal as cliente_cp,
                      cl.calle as cliente_calle, cl.no_ext as cliente_no_ext,
                      cl.colonia as cliente_colonia, cl.municipio as cliente_municipio,
                      cl.ciudad as cliente_ciudad, cl.estado as cliente_estado_dir,
                      cl.regimen_fiscal as cliente_regimen')
            ->join('sellopro_cotizaciones cot', 'cot.id_cotizacion = f.cotizacion_id')
            ->join('sellopro_clientes cl',      'cl.id_cliente     = cot.cliente')
            ->where('f.id', $id)
            ->get()->getRowArray();

        if (!$row) {
            return redirect()->back()->with('error', 'Factura no encontrada');
        }

        $resp  = json_decode($row['respuesta_completa'] ?? '{}', true) ?? [];
        $stamp = $resp['stamp'] ?? [];
        $items = $resp['items'] ?? [];

        // ── Montos ────────────────────────────────────────────────────────
        // Preferir resp['total'] porque monto en BD puede estar sin decimales
        $total = (float)($resp['total'] ?? $row['monto'] ?? 0);

        // FacturAPI v2 no devuelve 'subtotal' en el nivel raíz; se calcula desde los items
        $subtotal = 0;
        foreach ($items as $it) {
            $price = (float)($it['product']['price'] ?? $it['unit_price'] ?? 0);
            $qty   = (float)($it['quantity'] ?? 1);
            $subtotal += $price * $qty;
        }
        if ($subtotal === 0.0) {
            $subtotal = (float)($resp['subtotal'] ?? 0);
        }

        $iva = $total - $subtotal;

        // ── Campos del Timbre Fiscal Digital ─────────────────────────────
        // uuid está en el nivel raíz de la respuesta FacturAPI v2
        $uuid        = $resp['uuid']             ?? $row['factura_uuid'] ?? '';
        $rfcEmisor   = 'RERA7701272R1';
        $rfcReceptor = $row['cliente_rfc']       ?? '';
        $satCert     = $stamp['sat_cert_number'] ?? '';
        $rfcProvCert = $stamp['rfc_provider_cert'] ?? '';
        $fechaStamp  = $stamp['date']            ?? $row['fecha_timbrado'] ?? '';

        // sat_signature = Sello del SAT; signature = Sello del CFDI (emisor)
        $selloSat  = $stamp['sat_signature'] ?? '';
        $selloCfdi = $stamp['signature']     ?? '';

        // FacturAPI ya construye la cadena original; si falta, la armamos
        $cadenaOriginal = $stamp['complement_string']
            ?? "||1.1|{$uuid}|{$fechaStamp}|{$rfcProvCert}|{$selloCfdi}|{$satCert}||";

        // FacturAPI también provee la URL de verificación SAT
        $urlVerif = $resp['verification_url'] ?? '';
        if (!$urlVerif) {
            $fe       = substr($selloCfdi, -8);
            $totalFmt = number_format($total, 6, '.', '');
            $urlVerif = 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx'
                      . "?id={$uuid}&re={$rfcEmisor}&rr={$rfcReceptor}&tt={$totalFmt}&fe={$fe}";
        }

        // ── Código QR ────────────────────────────────────────────────────
        $qrBase64 = '';
        try {
            $qr     = QrCode::create($urlVerif)->setSize(200)->setMargin(4);
            $writer = new PngWriter();
            $result = $writer->write($qr);
            $qrBase64 = 'data:image/png;base64,' . base64_encode($result->getString());
        } catch (\Throwable $e) {
            log_message('error', '[PDF Factura QR] ' . $e->getMessage());
        }

        $usoCfdi = $resp['use'] ?? '';
        $clienteDireccion = trim(implode(' ', array_filter([
            $row['cliente_calle']    ?? '',
            $row['cliente_no_ext']   ?? '',
            $row['cliente_colonia']  ?? '',
            $row['cliente_municipio'] ?? '',
            $row['cliente_ciudad']   ?? '',
            $row['cliente_estado_dir'] ?? '',
            'MEXICO'
        ])));

        $data = [
            'serie'          => $row['serie']         ?? '',
            'folio'          => $row['folio']          ?? '',
            'estado'         => $row['estado']         ?? '',
            'fecha_timbrado' => $fechaStamp,
            'cliente_nombre'    => $row['nombre_cliente'] ?? '',
            'cliente_rfc'       => $rfcReceptor,
            'cliente_email'     => $row['cliente_email']  ?? '',
            'cliente_cp'        => $row['cliente_cp']     ?? '',
            'cliente_direccion' => $clienteDireccion,
            'cliente_regimen'   => $row['cliente_regimen'] ?? '',
            'uso_cfdi'          => $usoCfdi,
            'items'          => $items,
            'subtotal'       => $subtotal,
            'iva'            => $iva,
            'total'          => $total,
            'uuid'           => $uuid,
            'sat_cert'       => $satCert,
            'rfc_prov_cert'  => $rfcProvCert,
            'sello_sat'      => $selloSat,
            'sello_cfdi'     => $selloCfdi,
            'cadena_original'=> $cadenaOriginal,
            'qr_base64'      => $qrBase64,
            'url_verif'      => $urlVerif,
        ];

        $dompdf  = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled',    true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont',        'DejaVu Sans');
        $options->set('charset',            'UTF-8');
        $dompdf->setOptions($options);

        $dompdf->loadHtml(view('Panel/PDF_factura', $data), 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("FAC-{$row['serie']}-{$row['folio']}.pdf", ['Attachment' => true]);
    }

    // ==================================================================
    // DESCARGAR PDF / XML / ZIP
    // ==================================================================

    /**
     * GET /facturas/descargar/{id}/{formato}
     * $formato: "pdf" | "xml" | "zip"
     *
     * FacturAPI devuelve el archivo binario directamente.
     */
    public function descargar(int $id, string $formato = 'pdf')
    {
        $factura = $this->facturaModel->find($id);
        if (!$factura) {
            return redirect()->back()->with('error', 'Factura no encontrada');
        }

        $formato = strtolower($formato);
        if (!in_array($formato, ['pdf', 'xml', 'zip'])) {
            return redirect()->back()->with('error', 'Formato no válido. Use pdf, xml o zip.');
        }

        $apiResponse = $this->facturapi->descargar($factura['factura_uuid'], $formato);

        if (!$apiResponse['success']) {
            return redirect()->back()->with('error', 'No se pudo obtener el archivo de FacturAPI.');
        }

        $contentTypes = [
            'pdf' => 'application/pdf',
            'xml' => 'text/xml; charset=utf-8',
            'zip' => 'application/zip',
        ];

        $serie  = $factura['serie']  ?? 'F';
        $folio  = $factura['folio']  ?? $id;
        $nombre = "{$serie}-{$folio}.{$formato}";

        return $this->response
            ->setHeader('Content-Type', $contentTypes[$formato])
            ->setHeader('Content-Disposition', "attachment; filename=\"{$nombre}\"")
            ->setBody($apiResponse['body']);
    }

    // ==================================================================
    // ENVIAR FACTURA POR CORREO  (GET /facturas/enviar/{id})
    // ==================================================================

    public function enviarFactura(int $id)
    {
        $db  = \Config\Database::connect();
        $row = $db->table('sellopro_facturas f')
            ->select('f.*, cl.nombre as nombre_cliente, cl.tax_id as cliente_rfc,
                      cl.correo as cliente_email, cl.codigo_postal as cliente_cp,
                      cl.calle as cliente_calle, cl.no_ext as cliente_no_ext,
                      cl.colonia as cliente_colonia, cl.municipio as cliente_municipio,
                      cl.ciudad as cliente_ciudad, cl.estado as cliente_estado_dir,
                      cl.regimen_fiscal as cliente_regimen')
            ->join('sellopro_cotizaciones cot', 'cot.id_cotizacion = f.cotizacion_id')
            ->join('sellopro_clientes cl',      'cl.id_cliente     = cot.cliente')
            ->where('f.id', $id)
            ->get()->getRowArray();

        if (!$row) {
            return redirect()->to(base_url('facturas'))->with('error', 'Factura no encontrada.');
        }

        $correo = trim($row['cliente_email'] ?? '');
        if (!$correo) {
            return redirect()->to(base_url('facturas'))->with('error', 'El cliente no tiene correo registrado.');
        }

        $resp  = json_decode($row['respuesta_completa'] ?? '{}', true) ?? [];
        $stamp = $resp['stamp'] ?? [];
        $items = $resp['items'] ?? [];

        $total    = (float)($resp['total']    ?? $row['monto'] ?? 0);
        $subtotal = 0;
        foreach ($items as $it) {
            $subtotal += (float)($it['product']['price'] ?? $it['unit_price'] ?? 0)
                       * (float)($it['quantity'] ?? 1);
        }
        $iva = $total - $subtotal;

        $uuid        = $resp['uuid']               ?? $row['factura_uuid'] ?? '';
        $rfcEmisor   = 'RERA7701272R1';
        $rfcReceptor = $row['cliente_rfc']         ?? '';
        $satCert     = $stamp['sat_cert_number']   ?? '';
        $rfcProvCert = $stamp['rfc_provider_cert'] ?? '';
        $fechaStamp  = $stamp['date']              ?? $row['fecha_timbrado'] ?? '';
        $selloSat    = $stamp['sat_signature']     ?? '';
        $selloCfdi   = $stamp['signature']         ?? '';

        $cadenaOriginal = $stamp['complement_string']
            ?? "||1.1|{$uuid}|{$fechaStamp}|{$rfcProvCert}|{$selloCfdi}|{$satCert}||";

        $urlVerif = $resp['verification_url'] ?? '';
        if (!$urlVerif) {
            $fe       = substr($selloCfdi, -8);
            $totalFmt = number_format($total, 6, '.', '');
            $urlVerif = 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx'
                      . "?id={$uuid}&re={$rfcEmisor}&rr={$rfcReceptor}&tt={$totalFmt}&fe={$fe}";
        }

        $qrBase64 = '';
        try {
            $qr       = QrCode::create($urlVerif)->setSize(200)->setMargin(4);
            $qrBase64 = 'data:image/png;base64,' . base64_encode((new PngWriter())->write($qr)->getString());
        } catch (\Throwable $e) {
            log_message('error', '[Factura Email QR] ' . $e->getMessage());
        }

        $usoCfdi = $resp['use'] ?? '';
        $clienteDireccion = trim(implode(' ', array_filter([
            $row['cliente_calle']     ?? '',
            $row['cliente_no_ext']    ?? '',
            $row['cliente_colonia']   ?? '',
            $row['cliente_municipio'] ?? '',
            $row['cliente_ciudad']    ?? '',
            $row['cliente_estado_dir'] ?? '',
            'MEXICO'
        ])));

        $viewData = [
            'serie'          => $row['serie']         ?? '',
            'folio'          => $row['folio']          ?? '',
            'estado'         => $row['estado']         ?? '',
            'fecha_timbrado' => $fechaStamp,
            'cliente_nombre'    => $row['nombre_cliente'] ?? '',
            'cliente_rfc'       => $rfcReceptor,
            'cliente_email'     => $correo,
            'cliente_cp'        => $row['cliente_cp']     ?? '',
            'cliente_direccion' => $clienteDireccion,
            'cliente_regimen'   => $row['cliente_regimen'] ?? '',
            'uso_cfdi'          => $usoCfdi,
            'items'          => $items,
            'subtotal'       => $subtotal,
            'iva'            => $iva,
            'total'          => $total,
            'uuid'           => $uuid,
            'sat_cert'       => $satCert,
            'rfc_prov_cert'  => $rfcProvCert,
            'sello_sat'      => $selloSat,
            'sello_cfdi'     => $selloCfdi,
            'cadena_original'=> $cadenaOriginal,
            'qr_base64'      => $qrBase64,
            'url_verif'      => $urlVerif,
        ];

        $dompdf  = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isRemoteEnabled',      true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont',          'DejaVu Sans');
        $options->set('charset',              'UTF-8');
        $dompdf->setOptions($options);
        $dompdf->loadHtml(view('Panel/PDF_factura', $viewData), 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfContent = $dompdf->output();

        $xmlContent  = '';
        $xmlResponse = $this->facturapi->descargar($row['factura_uuid'], 'xml');
        if ($xmlResponse['success']) {
            $xmlContent = $xmlResponse['body'];
        }

        $logoPath = ROOTPATH . 'public/img/logo2.png';
        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
        $logoMime = file_exists($logoPath) ? mime_content_type($logoPath) : 'image/png';

        $serie = $row['serie'] ?? 'F';
        $folio = $row['folio'] ?? $id;

        $email = \Config\Services::email();
        $email->setFrom('ventas@sellopronto.com.mx', 'Sello Pronto');
        $email->setTo($correo);
        $email->setSubject("Factura CFDI {$serie}-{$folio}");
        $email->setMailType('html');
        $email->setMessage(view('Emails/factura', [
            'cliente_nombre' => $row['nombre_cliente'] ?? '',
            'serie'          => $serie,
            'folio'          => $folio,
            'total'          => $total,
            'uuid'           => $uuid,
            'logo_data'      => $logoData,
            'logo_mime'      => $logoMime,
        ]));
        $email->attach($pdfContent, 'attachment', "factura-{$serie}-{$folio}.pdf", 'application/pdf');
        if ($xmlContent) {
            $email->attach($xmlContent, 'attachment', "factura-{$uuid}.xml", 'text/xml');
        }

        if ($email->send()) {
            return redirect()->to(base_url('facturas'))
                ->with('success', "Factura {$serie}-{$folio} enviada correctamente a {$correo}");
        }

        log_message('error', '[Factura Email] ' . $email->printDebugger(['headers']));
        return redirect()->to(base_url('facturas'))
            ->with('error', 'No se pudo enviar el correo. Revisa la configuración de email.');
    }

    // ==================================================================
    // ENVIAR POR CORREO  (POST JSON — delegado a FacturAPI)
    // ==================================================================

    /**
     * POST /facturas/enviar_correo
     * Body JSON: { "id_factura": 5, "correo": "cliente@email.com" }
     */
    public function enviarCorreo()
    {
        $input     = $this->request->getJSON(true);
        $idFactura = (int) ($input['id_factura'] ?? 0);
        $correo    = trim($input['correo'] ?? '');

        if (!$idFactura || !$correo) {
            return $this->respond(['status' => 'error', 'message' => 'ID de factura y correo son requeridos'], 400);
        }

        $factura = $this->facturaModel->find($idFactura);
        if (!$factura) {
            return $this->respond(['status' => 'error', 'message' => 'Factura no encontrada'], 404);
        }

        $apiResponse = $this->facturapi->enviarEmail($factura['factura_uuid'], $correo);

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
     * POST /facturas/cancelar
     * Body JSON: { "id_factura": 5, "motivo": "02", "uuid_sustituto": null }
     *
     * Motivos SAT: 01=Errores con relación, 02=Errores sin relación,
     *              03=No se realizó la operación, 04=Operación nominativa
     */
    public function cancelar()
    {
        $input        = $this->request->getJSON(true);
        $idFactura    = (int) ($input['id_factura'] ?? 0);
        $motivo       = $input['motivo']         ?? '02';
        $uuidSustituto = $input['uuid_sustituto'] ?? null;

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

        $apiResponse = $this->facturapi->cancelar($factura['factura_uuid'], $motivo, $uuidSustituto);

        if (!$apiResponse['success']) {
            return $this->respond([
                'status'  => 'error',
                'message' => 'No se pudo cancelar: ' . ($apiResponse['message'] ?? ''),
                'detalle' => $apiResponse['body'] ?? null,
            ], 500);
        }

        $this->facturaModel->update($idFactura, ['estado' => 'cancelada']);
        $this->cotizacionesModel->update($factura['cotizacion_id'], ['estado_comercial' => 'pagado']);

        return $this->respond([
            'status'  => 'success',
            'message' => 'CFDI cancelado correctamente',
        ]);
    }

    // ==================================================================
    // HELPERS PRIVADOS
    // ==================================================================

    /**
     * Convierte los detalles de la cotización al formato de items de FacturAPI.
     *
     * Estructura FacturAPI:
     * {
     *   "quantity": 2,
     *   "product": {
     *     "description": "...",
     *     "product_key": "01010101",  ← clave SAT
     *     "unit_key":    "H87",       ← H87 = Pieza
     *     "unit_name":   "Pieza",
     *     "price":       100.00,      ← precio SIN IVA
     *     "tax_included": false,
     *     "taxes": [{"type": "IVA", "rate": 0.16}]
     *   }
     * }
     */
    private function buildItems(array $detalles): array
    {
        $items = [];

        foreach ($detalles as $detalle) {
            $claveSat = '01010101'; // Clave genérica SAT

            if (!empty($detalle['id_articulo'])) {
                $articulo = $this->articulosModel->find($detalle['id_articulo']);
                if ($articulo && !empty($articulo['clave_producto'])) {
                    $claveSat = $articulo['clave_producto'];
                }
            }

            $items[] = [
                'quantity' => (float) $detalle['cantidad'],
                'product'  => [
                    'description' => $detalle['descripcion'] ?? 'Producto / Servicio',
                    'product_key' => $claveSat,
                    'unit_key'    => 'H87',      // H87 = Pieza (catálogo SAT)
                    'unit_name'   => 'Pieza',
                    'price'       => (float) $detalle['p_unitario'],
                    'tax_included'=> false,
                    'taxes'       => [
                        ['type' => 'IVA', 'rate' => 0.16],
                    ],
                ],
            ];
        }

        return $items;
    }
}
