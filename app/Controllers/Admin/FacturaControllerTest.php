<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use Facturapi\Facturapi;
use App\Models\CotizacionesModel;
use App\Models\ArticulosModel;
use App\Models\ClientesModel;
use App\Models\DetalleModel;
use App\Models\FacturaModel;
use Dompdf\Dompdf;
class FacturaController extends BaseController
{
    public function convertir()
    {
        // Creamos una instancias de los recursos
        $request = \Config\Services::Request();
        $db = \Config\Database::connect();
        $factura = new FacturaModel();
        $cotizacion = new CotizacionesModel();
        $cliente = new ClientesModel();
        $articulos = new DetalleModel();
        $apiSecret = env('FACTURA_API_SECRET'); // la api key
        $cotizacionId = $request->getVar('id_cotizacion');
        
        $resultado_cotizacion = $cotizacion->where('id_cotizacion',$cotizacionId)->findAll();
        $numeroCliente = $resultado_cotizacion[0]['cliente'];
        
        //nos aseguramos que la cotizacion no se haya facturado
        $hayFactura = $factura->where('cotizacion_id',$cotizacionId)->first();
        if ($hayFactura){
            return $this->response->setJSON([
                'status'=>'error',
                'message'=>'Esta cotizacion ya se facturo',
                'factura'=> 1
            ]);
        }

        // Articulos de la cotizacion
        $builder = $db->table('sellopro_detalles');
        $builder->where('id_cotizacion',$resultado_cotizacion[0]['id_cotizacion']);
        $builder->join('sellopro_articulos','sellopro_articulos.id_articulo = sellopro_detalles.id_articulo');
        $listaArticulos = $builder->get()->getResultArray();

        //return json_encode($listaArticulos);

        $items_factura = [];
        foreach ($listaArticulos as $item) {
            $items_factura[] = [
                "quantity" => $item['cantidad'],
                "product" => [
                    "description" => $item['descripcion'],
                    "product_key" => $item['clave_producto'], // Asegúrate de tener este campo
                    "price" => $item['p_unitario'], // Usar precio unitario, no el total
                    "taxes" => [
                        [
                            "type" => "IVA",
                            "rate" => 0.16, // Puedes tener tasas variables
                        ]
                    ]
                ]
            ];
        }
        // Datos del cliente
        $datosCliente = $cliente->where('id_cliente',$numeroCliente)->findAll();
        $rfc = $datosCliente[0]['tax_id'];
        //return json_encode($listaArticulos);

        // Creamos al factura
        $facturapi = new Facturapi($apiSecret); //aqui abrimos el portal

        $invoice = $facturapi->Invoices->create([
          "customer" => [
            "legal_name" => $datosCliente[0]['nombre'],
            "email" => $datosCliente[0]['correo'],
            "tax_id" => $rfc,
            "tax_system" => $datosCliente[0]['regimen_fiscal'],
            "address" => [
              "zip" => $datosCliente[0]['codigo_postal']
            ]
          ],
          "items" => $items_factura,
          "payment_form" => "28" // "Tarjeta de débito"
        ]);

        $invoiceId = $invoice->id;
        $invoice = $facturapi->Invoices->retrieve($invoiceId);

        //guardamos la factura
        $datosFactura = [
            'cotizacion_id'=> $cotizacionId, 
            'factura_uuid'=>$invoice->id,
            'monto'=> $invoice->total,
        ];

        $insertar = $factura->insert($datosFactura);    
        if (!$insertar){
            return $this->response->setJSON([
                'status'=>'error',
                'message'=>'La factura no se pudo generar',
                'flag'=> 0
            ]);
        }

        //marcamos la cotizacion como facturada
        $facturada['entregada'] = 1;
        $update_facturada = $cotizacion->update($cotizacionId,$facturada);

        //obtener los pdf y xml


        //enviarlos por correo

        //que nos de la opcion de enviar

        //volver a la lista

        return json_encode($invoice);
    }
    public function enviarFacturaPorCorreo($id)
    {
        // Configuración de Dompdf (igual que en tu código original)
        $doc = new Dompdf();
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans'); 
        $doc->setOptions($options);
        
        // Obtener datos de la factura (igual que en tu código original)
        $factura = new FacturasModel();
        $factura->where('id_folio', $id);
        $resultado = $factura->findAll();
        $entidad = $resultado[0]['entidad'];
        $folio_externo = $resultado[0]['serie_emisor'];

        $api = new EntidadesModel();
        $api->where('id_entidad', $entidad);
        $resultado_api = $api->findAll();
        $key = $resultado_api[0]['secret_key'];
        
        $facturapi = new Facturapi($key);
        $invoice_in = $facturapi->Invoices->retrieve($folio_externo);
        $invoice = json_decode(json_encode($invoice_in), true);

        // Consulta regimen fiscal (igual que en tu código original)
        $regimen = new RegimenFiscalModel();
        $regimen->where('codigo', $invoice['customer']['tax_system']);
        $resultado_regimen = $regimen->findAll();
        $reg = $resultado_regimen[0]['nombre'];

        // Consulta de uso de factura (igual que en tu código original)
        $uso = new UsosFacturaModel();
        $uso->where('clave', $invoice['use']);
        $resultado_uso = $uso->findAll();
        $usoCFDI = $resultado_uso[0]['descripcion'];

        $subtotal = 0;
        $iva = 0;

        $qr = $invoice_in->verification_url;
        // Obtener el codigo qr (igual que en tu código original)
        $qrCode = new QrCode($qr);
        $qrCode->setSize(150);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $qrDataUri = $result->getDataUri();

        // Calcular subtotal e IVA (igual que en tu código original)
        foreach ($invoice['items'] as $item) {
            $quantity = $item['quantity'];
            $price = $item['product']['price'];
            $taxRate = $item['product']['taxes'][0]['rate'] ?? 0;
            $subtotal += $price * $quantity;
            $iva += ($price * $quantity) * $taxRate;
        }

        // Generar el HTML (igual que en tu código original)
        $html = view('pdf/factura', [
            'invoice' => $invoice,
            'id' => $id, 
            'regimen' => $reg,
            'uso' => $usoCFDI,
            'sub_total' => $subtotal,
            'iva' => $iva,
            'qrImage' => $qrDataUri,
        ]);

        // Generar el PDF
        $doc->loadHTML($html);
        $doc->setPaper('letter', 'portrait');
        $doc->render();
        
        // Obtener el contenido del PDF
        $pdfContent = $doc->output();
        
        // Configurar el correo electrónico
        $email = \Config\Services::email();
        
        // Obtener el correo del cliente desde la factura
        $clienteEmail = $invoice['customer']['email'];
        
        // Configurar el correo
        $email->setFrom('tucorreo@tudominio.com', 'Nombre de tu empresa');
        $email->setTo($clienteEmail);
        $email->setSubject('Factura FCT-' . $id);
        $email->setMessage('Adjunto encontrará su factura electrónica. Gracias por su preferencia.');
        
        // Adjuntar el PDF
        $email->attach($pdfContent, 'attachment', 'FCT-' . $id . '.pdf', 'application/pdf');
        
        // Enviar el correo
        if ($email->send()) {
            return "Factura enviada correctamente a " . $clienteEmail;
        } else {
            // Mostrar errores si hay alguno
            return "Error al enviar el correo: " . $email->printDebugger();
        }
    }
}