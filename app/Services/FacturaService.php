<?php

namespace App\Services;

use CodeIgniter\HTTP\CURLRequest;
use Config\Services;
use App\Models\CotizacionesModel;

class FacturaService
{
    protected $client;
    protected $apiKey;
    protected $apiSecret;
    protected $mode;

    public function __construct()
    {
        $this->apiKey = env('FACTURA_API_KEY');
        $this->apiSecret = env('FACTURA_API_SECRET');
        $this->mode = env('FACTURA_MODE', 'sandbox');
        
        $this->client = Services::curlrequest([
            'base_uri' => $this->getBaseUri(),
            'headers' => [
                'Content-Type' => 'application/json',
                'F-API-KEY' => $this->apiKey,
                'F-SECRET-KEY' => $this->apiSecret
            ],
            'timeout' => 30,
            'http_errors' => false
        ]);
    }

    protected function getBaseUri(): string
    {
        return $this->mode === 'production' 
            ? 'https://api.factura.com/v4/' 
            : 'https://api.factura.com/v4/cfdi40/create';
    }

    public function crearFactura(array $data)
    {
        try {
            $response = $this->client->post('cfdi40/create', [
                'json' => $data,
                'debug' => true
            ]);
            
            $body = $response->getBody();
            $data = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Respuesta JSON inválida: ' . $body);
            }
            
            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException($data['message'] ?? 'Error al crear factura');
            }
            
            return $data;
            
        } catch (\Exception $e) {
            log_message('error', 'Error al crear factura: ' . $e->getMessage());
            log_message('debug', 'Datos enviados: ' . print_r($data, true));
            return null;
        }
    }

    public function convertirCotizacionAFactura(int $cotizacionId, array $datosAdicionales = [])
    {
        $cotizacionModel = new CotizacionesModel();
        $cotizacion = $cotizacionModel->find($cotizacionId);
        
        if (!$cotizacion) {
            throw new \RuntimeException("Cotización no encontrada");
        }
        
        // Verificar que la cotización tenga items
        if (empty($cotizacion['items'])) {
            throw new \RuntimeException("La cotización no tiene items");
        }
        
        // Mapear los datos al formato de Factura.com v4
        $facturaData = [
            'Receptor' => [
                'UID' => $cotizacion['cliente_uid'] ?? '673b68c8b2632', // Ejemplo fallback
                'ResidenciaFiscal' => $cotizacion['residencia_fiscal'] ?? null
            ],
            'TipoDocumento' => 'I', // Ingreso
            'Conceptos' => [],
            'UsoCFDI' => $cotizacion['uso_cfdi'] ?? 'G03',
            'Serie' => $cotizacion['serie'] ?? 'FAC',
            'FormaPago' => $cotizacion['forma_pago'] ?? '01',
            'MetodoPago' => $cotizacion['metodo_pago'] ?? 'PUE',
            'Moneda' => 'MXN',
            'EnviarCorreo' => true
        ];
        
        // Procesar items
        foreach ($cotizacion['items'] as $item) {
            $concepto = [
                'ClaveProdServ' => $item['clave_producto'] ?? '78102203',
                'Cantidad' => $item['cantidad'] ?? 1,
                'ClaveUnidad' => $item['clave_unidad'] ?? 'H87',
                'Descripcion' => $item['descripcion'] ?? 'Servicio no especificado',
                'ValorUnitario' => $item['precio_unitario'] ?? 0,
                'Impuestos' => [
                    'Traslados' => [
                        [
                            'Base' => $item['subtotal'] ?? $item['precio_unitario'] * $item['cantidad'],
                            'Impuesto' => '002', // IVA
                            'TipoFactor' => 'Tasa',
                            'TasaOCuota' => 0.16,
                            'Importe' => $item['iva'] ?? ($item['subtotal'] * 0.16)
                        ]
                    ]
                ]
            ];
            
            $facturaData['Conceptos'][] = $concepto;
        }
        
        // Calcular totales si no vienen en la cotización
        if (!isset($cotizacion['total'])) {
            $subtotal = array_reduce($facturaData['Conceptos'], function($carry, $item) {
                return $carry + $item['Impuestos']['Traslados'][0]['Base'];
            }, 0);
            
            $iva = array_reduce($facturaData['Conceptos'], function($carry, $item) {
                return $carry + $item['Impuestos']['Traslados'][0]['Importe'];
            }, 0);
            
            $facturaData['SubTotal'] = $subtotal;
            $facturaData['Total'] = $subtotal + $iva;
        }
        
        // Combinar con datos adicionales
        return $this->crearFactura(array_merge($facturaData, $datosAdicionales));
    }
}