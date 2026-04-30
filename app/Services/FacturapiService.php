<?php

namespace App\Services;

/**
 * FacturapiService
 *
 * Cliente HTTP para la API de FacturAPI v2 (https://www.facturapi.io/v2).
 * Autenticación: Bearer token (sk_test_* para sandbox, sk_live_* para producción).
 *
 * Variables de entorno requeridas:
 *   FACTURAPI_KEY   = sk_test_xxxxxxxxxxxx
 *   FACTURAPI_SERIE = QT   (opcional, se configura también en el dashboard)
 */
class FacturapiService
{
    private const BASE_URL = 'https://www.facturapi.io/v2';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('FACTURAPI_KEY', '');
    }

    // ------------------------------------------------------------------
    // Operaciones principales
    // ------------------------------------------------------------------

    /**
     * Crea un CFDI de Ingreso.
     *
     * @param  array $payload Payload con type, customer, items, payment_form, etc.
     * @return array ['success', 'body', 'message']
     */
    public function crearFactura(array $payload): array
    {
        return $this->request('POST', '/invoices', $payload);
    }

    /**
     * Descarga el PDF, XML o ZIP de una factura.
     * Retorna el contenido binario del archivo en ['body'].
     *
     * @param  string $invoiceId ID de FacturAPI (campo "id" de la factura)
     * @param  string $formato   "pdf" | "xml" | "zip"
     * @return array ['success', 'body' (binario), 'message']
     */
    public function descargar(string $invoiceId, string $formato): array
    {
        return $this->request('GET', "/invoices/{$invoiceId}/{$formato}", [], true);
    }

    /**
     * Cancela un CFDI ante el SAT.
     *
     * Motivos SAT:
     *   01 = Errores con relación      (requiere related_uuid)
     *   02 = Errores sin relación
     *   03 = No se llevó a cabo la operación
     *   04 = Operación nominativa en factura global
     *
     * @param  string      $invoiceId   ID de FacturAPI
     * @param  string      $motivo      Clave de motivo SAT (default "02")
     * @param  string|null $relatedUuid UUID del CFDI sustituto (solo para motivo "01")
     * @return array ['success', 'body', 'message']
     */
    public function cancelar(string $invoiceId, string $motivo = '02', ?string $relatedUuid = null): array
    {
        $body = ['motive' => $motivo];
        if ($relatedUuid) {
            $body['related_uuid'] = $relatedUuid;
        }
        return $this->request('DELETE', "/invoices/{$invoiceId}", $body);
    }

    /**
     * Envía la factura por correo electrónico al cliente.
     *
     * @param  string $invoiceId ID de FacturAPI
     * @param  string $email     Dirección de correo destino
     * @return array ['success', 'body', 'message']
     */
    public function enviarEmail(string $invoiceId, string $email): array
    {
        return $this->request('POST', "/invoices/{$invoiceId}/email", ['email' => $email]);
    }

    // ------------------------------------------------------------------
    // HTTP interno
    // ------------------------------------------------------------------

    /**
     * Realiza una petición HTTP autenticada a la API de FacturAPI.
     *
     * @param  string $method   GET | POST | DELETE
     * @param  string $endpoint Ruta sin base URL, ej: "/invoices"
     * @param  array  $body     Cuerpo JSON (POST/DELETE con body)
     * @param  bool   $binary   true para respuestas binarias (PDF/XML)
     * @return array  ['success' => bool, 'body' => mixed, 'message' => string]
     */
    private function request(string $method, string $endpoint, array $body = [], bool $binary = false): array
    {
        $url    = self::BASE_URL . $endpoint;
        $method = strtoupper($method);
        $curl   = curl_init();

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
        ];

        if (!$binary) {
            $headers[] = 'Accept: application/json';
        }

        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
        ];

        switch ($method) {
            case 'POST':
                $options[CURLOPT_POST]       = true;
                $options[CURLOPT_POSTFIELDS] = json_encode($body);
                break;

            case 'DELETE':
                $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                if (!empty($body)) {
                    $options[CURLOPT_POSTFIELDS] = json_encode($body);
                }
                break;
        }

        curl_setopt_array($curl, $options);

        $rawResponse = curl_exec($curl);
        $httpCode    = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError   = curl_error($curl);
        curl_close($curl);

        if ($curlError) {
            log_message('error', "[FacturAPI] cURL error en {$method} {$endpoint}: {$curlError}");
            return [
                'success' => false,
                'message' => "Error de conexión: {$curlError}",
                'body'    => null,
            ];
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'body'    => $binary ? $rawResponse : json_decode($rawResponse, true),
                'message' => 'OK',
            ];
        }

        $decoded  = json_decode($rawResponse, true);
        $errorMsg = $decoded['message'] ?? "HTTP {$httpCode}";

        log_message('error', "[FacturAPI] HTTP {$httpCode} en {$method} {$endpoint}: " . json_encode($decoded));

        return [
            'success' => false,
            'message' => $errorMsg,
            'body'    => $decoded,
        ];
    }
}
