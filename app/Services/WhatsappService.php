<?php

namespace App\Services;

class WhatsappService
{
    private string $token;
    private string $phoneNumberId;
    private string $apiVersion;

    public function __construct()
    {
        $this->token         = env('WHATSAPP_TOKEN', '');
        $this->phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID', '');
        $this->apiVersion    = env('WHATSAPP_API_VERSION', 'v19.0');
    }

    public function enviarTemplate(string $telefono, string $templateName, array $variables): array
    {
        $params = array_map(fn($v) => ['type' => 'text', 'text' => (string) $v], $variables);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $this->formatearTelefono($telefono),
            'type'              => 'template',
            'template'          => [
                'name'       => $templateName,
                'language'   => ['code' => 'es_MX'],
                'components' => [
                    ['type' => 'body', 'parameters' => $params],
                ],
            ],
        ];

        return $this->post($payload);
    }

    public function formatearTelefono(string $telefono): string
    {
        $n = preg_replace('/\D/', '', $telefono);
        return strlen($n) === 10 ? '52' . $n : $n;
    }

    private function post(array $payload): array
    {
        $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT    => 15,
        ]);

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            return ['success' => false, 'message' => 'Error de conexión: ' . $curlErr];
        }

        $decoded = json_decode($body, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'body' => $decoded];
        }

        $msg = $decoded['error']['message'] ?? 'Error desconocido de la API de Meta';
        return ['success' => false, 'message' => $msg, 'body' => $decoded];
    }
}
