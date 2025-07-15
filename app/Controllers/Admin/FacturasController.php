<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;
use Fiscalapi\Http\FiscalApiSettings;
use Fiscalapi\Services\FiscalApiClient;

class FacturasController extends Controller
{
    private $fiscalApiClient;

    public function __construct()
    {
        $this->fiscalApiClient = new FiscalApiClient(
            new \Fiscalapi\Http\FiscalApiSettings(
                getenv('FISCALAPI_URL'),
                getenv('FISCALAPI_KEY'),
                getenv('FISCALAPI_TENANT'),
                filter_var(getenv('FISCALAPI_DEBUG'), FILTER_VALIDATE_BOOLEAN),
                filter_var(getenv('FISCALAPI_VERIFY_SSL'), FILTER_VALIDATE_BOOLEAN),
                getenv('FISCALAPI_API_VERSION'),
                getenv('FISCALAPI_TIMEZONE')
            )
        );

    }
    public function crearFactura()
    {
        $cerPath = WRITEPATH . 'credenciales/cacx7605101p8.cer';
        $keyPath = WRITEPATH . 'credenciales/Claveprivada_FIEL_CACX7605101P8_20230509_114423.key';

        $cerContent = base64_encode(file_get_contents($cerPath));
        $keyContent = base64_encode(file_get_contents($keyPath));

        // === INICIO DEL ARRAY CORREGIDO ===
        // Por favor, verifica que tu array se ve exactamente así.
        $invoice = [
            // CORRECCIÓN 1: El campo "command" es añadido.
            "command" => "Create",

            "versionCode" => "4.0",
            "series" => "SDK-F",
            "date" => date('Y-m-d\TH:i:s'),
            "paymentFormCode" => "01",
            "currencyCode" => "MXN",
            "typeCode" => "I",
            "expeditionZipCode" => "42501",
            "issuer" => [
                "tin" => "CACX7605101P8", //RFC
                "legalName" => "XOCHILT CASAS CHAVEZ",
                "taxRegimeCode" => "612",
                "taxCredentials" => [
                    [
                        "base64File" => $cerContent,
                        // CORRECCIÓN 2: "fileType" es ahora el número 1.
                        "fileType" => 0,
                        "password" => "12345678a"
                    ],
                    [
                        "base64File" => $keyContent,
                        // CORRECCIÓN 2: "fileType" es ahora el número 2.
                        "fileType" => 1,
                        "password" => "12345678a"
                    ]
                ]
            ],
            "recipient" => [
                "tin" => "EKU9003173C9",
                "legalName" => "ESCUELA KEMPER URGATE",
                "zipCode" => "42501",
                "taxRegimeCode" => "601",
                "cfdiUseCode" => "G03",
                "email" => "cliente@example.com"
            ],
            "items" => [
                [
                    "itemCode" => "01010101",
                    "quantity" => 1,
                    "unitOfMeasurementCode" => "E48",
                    "description" => "Servicio de prueba",
                    "unitPrice" => 100,
                    "taxObjectCode" => "02",
                    'itemSku' => "7506022301697",
                    "discount" => 0,
                    "itemTaxes" => [
                        [
                            "taxCode" => "002",
                            "taxTypeCode" => "Tasa",
                            "taxRate" => 0.16,
                            "taxFlagCode" => "T"
                        ]
                    ]
                ]
            ],
            "paymentMethodCode" => "PUE"
        ];
        // === FIN DEL ARRAY CORREGIDO ===

        try {
            $invoiceService = $this->fiscalApiClient->getInvoiceService();
            $apiResponse = $invoiceService->create($invoice);

            // --- CÓDIGO INTELIGENTE PARA MANEJAR ÉXITO O ERROR ---

            // Usamos Reflexión para acceder a la respuesta interna de Guzzle
            $reflection = new \ReflectionObject($apiResponse);
            $property = $reflection->getProperty('response');
            $property->setAccessible(true);
            $guzzleResponse = $property->getValue($apiResponse);
            $statusCode = $guzzleResponse->getStatusCode();

            // Si el código NO es 200 (éxito), mostramos el error detallado
            if ($statusCode !== 200) {
                $bodyStream = $guzzleResponse->getBody();
                $bodyStream->rewind(); 
                $bodyContent = $bodyStream->getContents();
                $errorDetails = json_decode($bodyContent);

                // Devolvemos el error en un JSON limpio
                return $this->response->setStatusCode($statusCode)->setJSON([
                    'error' => true,
                    'message' => 'La API de FiscalAPI devolvió un error.',
                    'details' => $errorDetails
                ]);
            }

            // Si el código SÍ es 200, devolvemos la respuesta exitosa
            return $this->response->setJSON($apiResponse);

        } catch (\Exception $e) {
            // Este bloque se ejecutará si hay un error de conexión o un problema grave
            return $this->response->setStatusCode(500)->setJSON([
                'error' => true,
                'message' => 'Error crítico en la ejecución: ' . $e->getMessage()
            ]);
        }
    }

}
