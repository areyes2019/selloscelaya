<?php

namespace App\Controllers\Admin; // Ajusta si es necesario

use App\Controllers\BaseController;
use App\Models\OrdenTrabajoModel;
use App\Models\PedidoModel; // Para obtener datos del cliente
use App\Models\CotizacionesModel; // Para obtener datos del cliente
use App\Models\ClientesModel; // Para obtener datos del cliente
use CodeIgniter\Exceptions\PageNotFoundException;
use Dompdf\Dompdf;

class OrdenTrabajoController extends BaseController
{
    protected $ordenTrabajoModel;
    protected $pedidoModel;

    public function __construct()
    {
        $this->ordenTrabajoModel = new OrdenTrabajoModel();
        $this->pedidoModel = new PedidoModel();
        helper(['form', 'url', 'filesystem']); // Necesitamos filesystem para manejar archivos
    }

    public function index()
    {
        $resultado = $this->ordenTrabajoModel->findAll();
        $data =[
            'titulo'=>'Ordenes de Trabajo',
            'lista'=> $resultado
        ];
        return view('Panel/ordenes_trabajo',$data);
    }
   
    /**
     * Muestra el formulario para crear una nueva orden de trabajo,
     * pre-llenando datos desde un pedido existente.
     */
    public function etiquetas_pdf()
    {
        $pedidoModel = new PedidoModel();

        $query = $pedidoModel
            ->select([
                'pedidos.id as pedido_id',
                'pedidos.cliente_nombre',
                'pedidos.cliente_telefono',
                'pedidos.total',
                'pedidos.anticipo',
                'pedidos.estado'
            ])
            ->join('sellopro_ordenes_trabajo ot', 'ot.pedido_id = pedidos.id', 'left')
            ->where('pedidos.estado', 'pendiente')
            ->orderBy('pedidos.id', 'ASC');

        $resultados = $query->get()->getResultObject();

        if (empty($resultados)) {
            return redirect()->back()->with('message', 'No se encontraron pedidos pendientes para generar etiquetas.');
        }

        $html = '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <style>
                @page {
                    margin-top: 18pt;
                    margin-right: 34pt;
                    margin-bottom: 26pt;
                    margin-left: 34pt;
                }
                body {
                    font-family: DejaVu Sans, sans-serif;
                    font-size: 10pt;
                    margin: 0;
                    padding: 0;
                }
                .etiqueta {
                    width: 215pt;
                    height: 71pt;
                    display: inline-block;
                    margin-left:5pt;
                    margin-right:5pt;
                    margin-bottom: 2pt;
                    box-sizing: border-box;
                    padding: 5px 10px;
                    overflow: hidden;
                    vertical-align: top;
                    page-break-inside: avoid;
                    position: relative;
                    line-height: 1.2; /* Ajuste de interlineado */
                }
                .pedido-id {
                    font-size: 12pt;
                    font-weight: bold;
                    display: inline-block; /* Para que el saldo pueda flotar junto a él */
                    margin-bottom: 3px;
                }
                .cliente-nombre,
                .cliente-telefono,
                .clave {
                    font-size: 8pt;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    margin: 2px 0;
                    display: block; /* Cada uno en su propia línea */
                }
                .saldo {
                    position: absolute;
                    right: 25px;
                    top: 5px; /* Alineado con el pedido-id */
                    font-size: 10pt;
                    font-weight: bold;
                }
                .pagado {
                    position: absolute;
                    right: 2px;
                    top: 25px;
                    font-size: 10pt;
                    font-weight: bold;
                    color: green;
                }
                .pagina {
                    page-break-after: always;
                }
            </style>
        </head>
        <body><div class="pagina">';

        $contador = 0;
        foreach ($resultados as $item) {
            $total = floatval($item->total ?? 0);
            $anticipo = floatval($item->anticipo ?? 0);

            $saldoDisplay = '';
            $saldoClass = 'saldo';

            if (abs($total - $anticipo) < 0.01 && $total > 0) {
                $saldoDisplay = 'Pagado';
                $saldoClass = 'pagado';
            } else {
                $saldo = $total - $anticipo;
                $saldoDisplay = 'Saldo: ' . number_format($saldo, 2, ',', '.') . ' $';
            }

            $telefono = $item->cliente_telefono ?? '';
            $clave = (strlen($telefono) >= 4) ? substr($telefono, -4) : 'N/A';

            $html .= '<div class="etiqueta">';
            $html .= '<div class="pedido-id">#' . esc($item->pedido_id) . '</div>';
            $html .= '<div class="cliente-nombre">' . esc($item->cliente_nombre) . '</div>';
            $html .= '<div class="cliente-telefono">Tel: ' . esc($telefono ?: 'N/A') . '</div>';
            $html .= '<div class="clave">Clave: ' . esc($clave) . '</div>';
            $html .= '<div class="' . $saldoClass . '">' . esc($saldoDisplay) . '</div>';
            $html .= '</div>';

            $contador++;
            if ($contador % 5 == 0) {
                $html .= '</div><div class="pagina">';
            }
        }

        $html .= '</div></body></html>';

        // Configurar Dompdf
        $pdfOptions = new \Dompdf\Options();
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('defaultFont', 'DejaVu Sans');
        $pdfOptions->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($pdfOptions);
        $dompdf->loadHtml($html);

        // Tamaño 10cm x 15cm en pt
        $dompdf->setPaper([0, 0, 283.46, 425.2], 'portrait');

        $dompdf->render();

        $nombreArchivo = 'etiquetas_pedidos_' . date('Ymd_His') . '.pdf';
        if (ob_get_level()) ob_end_clean();
        $dompdf->stream($nombreArchivo, ['Attachment' => 0]);
        exit();
    }
    public function etiquetas_txt()
    {
        $pedidoModel = new PedidoModel();

        $query = $pedidoModel
            ->select([
                'pedidos.id as pedido_id',
                'pedidos.cliente_nombre',
                'pedidos.cliente_telefono',
                'pedidos.total',
                'pedidos.anticipo',
                'pedidos.estado',
                'ot.status'
            ])
            ->join('sellopro_ordenes_trabajo ot', 'ot.pedido_id = pedidos.id', 'inner')
            ->where('ot.status', 'Elaboracion')
            ->orderBy('pedidos.id', 'ASC');

        $resultados = $query->get()->getResultObject();

        if (empty($resultados)) {
            return redirect()->back()->with('message', 'No se encontraron órdenes en elaboración para generar etiquetas.');
        }

        // Encabezados del archivo CSV
        $csvContent = "pedido_id,cliente_nombre,cliente_telefono,total,anticipo,saldo,clave,estado_pago,status_ot\n";

        foreach ($resultados as $item) {
            $total = floatval($item->total ?? 0);
            $anticipo = floatval($item->anticipo ?? 0);
            $saldo = $total - $anticipo;
            
            $telefono = $item->cliente_telefono ?? '';
            $clave = (strlen($telefono) >= 4) ? substr($telefono, -4) : 'N/A';
            
            $estadoPago = (abs($total - $anticipo) < 0.01 && $total > 0) ? 'Pagado' : 'Pendiente';

            // Formatear cada línea con los datos necesarios
            $csvContent .= sprintf(
                '%d,"%s","%s",%.2f,%.2f,%.2f,"%s","%s","%s"'."\n",
                $item->pedido_id,
                str_replace('"', '""', $item->cliente_nombre), // Escapar comillas dobles
                $telefono,
                $total,
                $anticipo,
                $saldo,
                $clave,
                $estadoPago,
                $item->status
            );
        }

        // Configurar headers para descarga como CSV
        $nombreArchivo = 'etiquetas_ordenes_elaboracion_'.date('Ymd_His').'.csv';
        
        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="'.$nombreArchivo.'"')
            ->setBody($csvContent);
    }
    public function descargar_ordenes()
    {
        // 1. Instanciar el Modelo Principal
        $pedidoModel = new PedidoModel();

        // 2. Construir la Consulta con JOIN
        $query = $pedidoModel
            ->select([
                'pedidos.id as pedido_id_col',
                'pedidos.cliente_nombre',
                'pedidos.cliente_telefono',
                'pedidos.total',
                'pedidos.anticipo',
                'pedidos.estado', // Este campo podría ser diferente al status de OT
                'ot.imagen_path',
                'ot.color_tinta',
                'ot.observaciones',
                'ot.status' // Asegúrate de incluir este campo si lo necesitas mostrar
            ])
            ->join('sellopro_ordenes_trabajo ot', 'ot.pedido_id = pedidos.id', 'left')
            ->where('ot.status', 'Elaboracion'); // Filtramos por el status en la tabla de órdenes de trabajo

        // 3. Ejecutar la consulta y obtener resultados
        $resultadosCombinados = $query->get()->getResultObject();

        if (empty($resultadosCombinados)) {
            log_message('info', 'Intento de generar reporte PDF sin pedidos en estado Elaboracion.');
            return redirect()->back()->with('message', 'No se encontraron pedidos en estado Elaboración para generar el reporte.');
        }

        // 4. Preparar el HTML para el PDF
        $html = '<!DOCTYPE html>
                 <html lang="es">
                 <head>
                     <meta charset="UTF-8">
                     <title>Reporte de Pedidos en Elaboración</title>
                     <style>
                         body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
                         table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
                         th, td { border: 1px solid #ccc; padding: 6px; text-align: left; vertical-align: top; word-wrap: break-word; }
                         th { background-color: #e9e9e9; font-weight: bold; }
                         h1 { text-align: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;}
                         img { max-width: 60px; max-height: 60px; display: block; margin-top: 4px; }
                         .saldo { text-align: right; }
                         .pagado { text-align: center; font-weight: bold; color: green; }
                         .observaciones-cell { min-width: 150px; }
                         .na { color: #888; font-style: italic; }
                     </style>
                 </head>
                 <body>
                     <h1>Reporte de Pedidos en Elaboración</h1>
                     <table>
                         <thead>
                             <tr>
                                 <th>Cliente</th>
                                 <th>Teléfono</th>
                                 <th>Imagen</th>
                                 <th>Saldo</th>
                                 <th>Color Tinta</th>
                                 <th>Observaciones OT</th>
                             </tr>
                         </thead>
                         <tbody>';

        foreach ($resultadosCombinados as $item) {
            $total = floatval($item->total ?? 0);
            $anticipo = floatval($item->anticipo ?? 0);
            $saldoDisplay = '';
            $saldoClass = 'saldo';

            if (abs($total - $anticipo) < 0.001 && $total != 0) {
                $saldoDisplay = 'Pagado';
                $saldoClass = 'pagado';
            } else {
                $saldoCalculado = $total - $anticipo;
                $saldoDisplay = number_format($saldoCalculado, 2, ',', '.') . ' €';
            }

            $html .= '<tr>';
            $html .= '<td>' . esc($item->cliente_nombre ?? 'N/A') . '</td>';
            $html .= '<td>' . esc($item->cliente_telefono ?? 'N/A') . '</td>';

            $html .= '<td>';
            $rutaImagen = WRITEPATH . 'uploads/ordenes/' . ($item->imagen_path ?? '');
            if (!empty($item->imagen_path) && file_exists($rutaImagen)) {
                try {
                    $tipoMime = mime_content_type($rutaImagen);
                    if (strpos($tipoMime, 'image/') === 0) {
                        $imagenData = file_get_contents($rutaImagen);
                        $imagenBase64 = base64_encode($imagenData);
                        $html .= '<img src="data:' . $tipoMime . ';base64,' . $imagenBase64 . '" alt="Imagen Orden">';
                    } else {
                        $html .= '<span class="na">(Archivo no es imagen)</span>';
                    }
                } catch (\Exception $e) {
                    $html .= '<span class="na">(Error al cargar imagen)</span>';
                }
            } else {
                $html .= '<span class="na">(Sin imagen)</span>';
            }
            $html .= '</td>';

            $html .= '<td class="' . $saldoClass . '">' . $saldoDisplay . '</td>';
            $html .= '<td>' . esc($item->color_tinta ?? '<span class="na">N/D</span>') . '</td>';
            $html .= '<td class="observaciones-cell">' . nl2br(esc($item->observaciones ?? '<span class="na">N/D</span>')) . '</td>';

            $html .= '</tr>';
        }

        $html .= '</tbody>
                 </table>
                 </body>
                 </html>';

        // 5. Configurar y Generar Dompdf
        $pdfOptions = new \Dompdf\Options();
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('defaultFont', 'DejaVu Sans');
        $pdfOptions->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // 6. Enviar el PDF al navegador
        $nombreArchivo = 'reporte_pedidos_elaboracion_' . date('Ymd_His') . '.pdf';

        if (ob_get_level()) {
            ob_end_clean();
        }

        $dompdf->stream($nombreArchivo, ['Attachment' => 0]);
        exit();
    }
    public function new($pedido_id = null)
    {
        if ($pedido_id === null) {
             return redirect()->to('/pedidos/pos')->with('error', 'Se requiere un ID de pedido para crear la orden.');
        }

        $pedido = $this->pedidoModel->find($pedido_id);

        if (!$pedido) {
            throw PageNotFoundException::forPageNotFound('Pedido original no encontrado.');
        }

        $data['title'] = 'Crear Nueva Orden de Trabajo (Pedido #' . esc($pedido['id']) . ')';
        $data['pedido'] = $pedido; // Pasamos los datos del pedido a la vista

        // Opciones para el select de color (puedes obtenerlas de otro lugar si es dinámico)
        $data['colores_tinta'] = ['Negro', 'Cyan', 'Magenta', 'Amarillo', 'Blanco', 'Otro'];

        return view('Panel/orden_trabajo_new', $data); // Creamos esta vista ahora
    }
    /*
        Procesamos la orden de trabajo para una cotizacion
    **/
    public function crear_orden_trabajo($idCotizacion)
    {
        $cotizacionesModel = new CotizacionesModel();
        $clientesModel = new ClientesModel();
        $ordenTrabajoModel = new OrdenTrabajoModel();

        // Obtener datos de la cotización
        $cotizacion = $cotizacionesModel->find($idCotizacion);
        if (!$cotizacion) {
            session()->setFlashdata('error', 'Cotización no encontrada');
            return redirect()->back();
        }

        // Obtener datos del cliente
        $cliente = $clientesModel->find($cotizacion['cliente']);
        if (!$cliente) {
            session()->setFlashdata('error', 'Cliente no encontrado');
            return redirect()->back();
        }

        // Verificar si ya existe una orden
        $ordenExistente = $ordenTrabajoModel->where('pedido_id', $idCotizacion)->first();

        if ($ordenExistente) {
            return redirect()->back()->with('alert', [
                'type' => 'warning',
                'message' => 'Ya existe una orden de trabajo (OT#'.$ordenExistente->id_ot.') para esta cotización'
            ]);
        }

        // Preparar datos para la vista
        $data = [
            'title' => 'Nueva Orden de Trabajo',
            'pedido' => [
                'id' => $idCotizacion,
                'created_at' => date('Y-m-d H:i:s'), // O usar fecha de la cotización si existe
                'cliente_nombre' => $cliente['nombre'],
                'cliente_telefono' => $cliente['telefono']
            ],
            'colores_tinta' => [
                'Negro', 'Blanco', 'Rojo', 'Azul', 'Verde', 'Amarillo', 'Plateado', 'Dorado', 'Personalizado'
            ],
            // Puedes agregar más datos necesarios para la vista aquí
        ];

        return view('Panel/orden_trabajo_new', $data);
    }

    /**
     * Procesa la creación de una nueva orden de trabajo.
     */
    public function create()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'pedido_id' => 'required|is_natural_no_zero',
            'observaciones' => 'permit_empty|max_length[6000]', // TEXT puede ser grande
            'color_tinta' => 'permit_empty|max_length[100]',
            // Regla para la imagen (ajusta según tus necesidades)
            'imagen_orden' => [
                'label' => 'Imagen Adjunta',
                'rules' => 'permit_empty|uploaded[imagen_orden]|max_size[imagen_orden,2048]|ext_in[imagen_orden,png,jpg,jpeg,gif,webp]',
                 'errors' => [
                    'max_size' => 'La imagen es muy grande (máx 2MB).',
                    'ext_in' => 'Solo se permiten imágenes PNG, JPG, JPEG, GIF, WEBP.'
                 ]
            ],
            'status_inicial' => 'required|in_list[Dibujo,Elaboracion,Entrega]' // Validar status inicial
        ];

        if (!$this->validate($rules)) {
            // Volver al formulario con errores y datos antiguos
            // Necesitamos el pedido_id para redirigir correctamente
            $pedidoId = $this->request->getPost('pedido_id');
            return redirect()->to('/ordenes/new/' . $pedidoId)->withInput()->with('errors', $validation->getErrors());
        }

        // --- Procesar Imagen (si se subió) ---
        $imgPath = null;
        $imgFile = $this->request->getFile('imagen_orden');

        if ($imgFile && $imgFile->isValid() && !$imgFile->hasMoved()) {
            // Generar un nombre aleatorio para evitar colisiones
            $newName = $imgFile->getRandomName();
            // Mover el archivo a un directorio escribible (ej: writable/uploads/ordenes)
            // Asegúrate de que este directorio exista y tenga permisos de escritura
             $uploadPath = WRITEPATH . 'uploads/ordenes';
             if (!is_dir($uploadPath)) {
                 mkdir($uploadPath, 0777, true); // Crear directorio si no existe
             }

            if ($imgFile->move($uploadPath, $newName)) {
                // Guardar solo el nombre del archivo (o la ruta relativa si prefieres)
                 $imgPath = $newName; // O 'uploads/ordenes/' . $newName
                 log_message('info', 'Imagen de orden subida: ' . $imgPath);
            } else {
                log_message('error', 'Error al mover imagen de orden: ' . $imgFile->getErrorString() . '(' . $imgFile->getError() . ')');
                 $pedidoId = $this->request->getPost('pedido_id');
                return redirect()->to('/ordenes/new/' . $pedidoId)->withInput()->with('error', 'Error al guardar la imagen: '.$imgFile->getErrorString());
            }
        } elseif ($imgFile && $imgFile->hasMoved()) {
             // Esto no debería pasar si isValid() es true, pero por si acaso
            log_message('warning', 'Se intentó procesar una imagen ya movida.');
        } elseif($imgFile && $imgFile->getError() !== UPLOAD_ERR_NO_FILE) {
             // Hubo un error diferente a "no se subió archivo"
             log_message('error', 'Error en subida de imagen: ' . $imgFile->getErrorString() . '(' . $imgFile->getError() . ')');
             $pedidoId = $this->request->getPost('pedido_id');
             return redirect()->to('/ordenes/new/' . $pedidoId)->withInput()->with('error', 'Error al procesar la imagen: '.$imgFile->getErrorString());
        }
        // Si no se subió imagen (UPLOAD_ERR_NO_FILE), $imgPath sigue siendo null, lo cual está bien.


        // --- Preparar datos para guardar ---
         // Obtener datos del cliente desde el pedido original (o podrías tener campos en el form)
        $pedido = $this->pedidoModel->find($this->request->getPost('pedido_id'));
        if(!$pedido) {
             return redirect()->back()->withInput()->with('error', 'No se encontró el pedido original asociado.');
        }

        $dataToSave = [
            'pedido_id'      => $pedido['id'],
            'cliente_nombre'   => $pedido['cliente_nombre'], // Tomado del pedido
            'cliente_telefono' => $pedido['cliente_telefono'], // Tomado del pedido
            'observaciones'  => $this->request->getPost('observaciones'),
            'color_tinta'    => $this->request->getPost('color_tinta'),
            'imagen_path'    => $imgPath,
            'status'         => $this->request->getPost('status_inicial'), // Usar status del form
        ];

        // --- Guardar en la BD ---
        if ($this->ordenTrabajoModel->insert($dataToSave)) {
            return redirect()->to('/ordenes')->with('success', 'Orden de Trabajo creada con éxito.'); // Redirigir al dashboard
        } else {
            // Si falla la inserción (raro si la validación pasó, pero posible)
             // Eliminar imagen si se subió pero no se guardó el registro
             if ($imgPath && file_exists($uploadPath . '/' . $imgPath)) {
                 unlink($uploadPath . '/' . $imgPath);
             }
             $pedidoId = $this->request->getPost('pedido_id');
            return redirect()->to('/ordenes/new/' . $pedidoId)->withInput()->with('error', 'No se pudo guardar la orden de trabajo.');
        }
    }

    /**
     * (FUTURO) Muestra el formulario para editar una orden
     */
    public function edit($id = null)
    {
       // TODO: Implementar vista y lógica para editar una orden existente
       // Incluiría cargar datos, mostrar el form (similar a 'new'), y un método 'update'
        return redirect()->to('/ordenes')->with('info', 'Funcionalidad Editar no implementada aún.');
    }

    /**
     * (FUTURO) Procesa la actualización de una orden (incluyendo cambio de status)
     */
    public function update($id = null)
    {
         // TODO: Implementar lógica de actualización
         // Validar datos, manejar posible cambio de imagen, actualizar status
         return redirect()->to('/ordenes')->with('info', 'Funcionalidad Actualizar no implementada aún.');
    }


    /**
     * Cambia el status de una orden (ejemplo usando POST desde un form simple o AJAX)
     * Podrías querer una ruta específica como /ordenes/cambiar_status/[:id]
     */
     public function cambiarStatus($id = null)
     {
         if ($id === null || !$this->request->is('post')) {
             return redirect()->to('/admin')->with('error', 'Solicitud inválida.');
         }

         $orden = $this->ordenTrabajoModel->find($id);
         if (!$orden) {
              return redirect()->to('/admin')->with('error', 'Orden no encontrada.');
         }

         $nuevoStatus = $this->request->getPost('nuevo_status');
         $statusesValidos = ['Diseño', 'Elaboracion', 'Entrega']; // Asegurar que el status sea válido

         if (!in_array($nuevoStatus, $statusesValidos)) {
             return redirect()->to('/admin')->with('error', 'Status inválido proporcionado.');
         }

         // Opcional: Añadir lógica de flujo (ej. no se puede volver de 'Entrega' a 'Diseño')
         // if ($orden->status == 'Entrega' && $nuevoStatus != 'Entrega') { ... error ... }

         if ($this->ordenTrabajoModel->update($id, ['status' => $nuevoStatus])) {
             return redirect()->to('/admin')->with('success', 'Status de la orden #' . $id . ' actualizado a ' . $nuevoStatus);
         } else {
             return redirect()->to('/admin')->with('error', 'No se pudo actualizar el status de la orden.');
         }
     }
   
     public function serveImage($filename = null)
     {
         if ($filename === null) {
             throw PageNotFoundException::forPageNotFound();
         }

         $path = WRITEPATH . 'uploads/ordenes/' . basename($filename); // basename para seguridad

         if (!file_exists($path) || !is_file($path)) {
             throw PageNotFoundException::forPageNotFound('Imagen no encontrada.');
         }

         // Determinar el tipo MIME
         $mime = mime_content_type($path);
         if ($mime === false) {
             $mime = 'application/octet-stream'; // Tipo genérico si falla la detección
         }

         // Servir el archivo
         return $this->response
             ->setHeader('Content-Type', $mime)
             ->setHeader('Content-Length', filesize($path))
             // ->setHeader('Cache-Control', 'max-age=3600') // Opcional: Cache
             ->setBody(file_get_contents($path))
             ->send(); // No uses return aquí
    }
    public function actualizarStatus($id)
    {

        $ordenModel = new OrdenTrabajoModel();

        // Buscamos la orden por su ID
        $orden = $ordenModel->find($id);

        if (!$orden) {
            return redirect()->back()->with('error', 'Orden no encontrada.');
        }

        // Definimos el siguiente estatus
        $nuevoStatus = '';

        switch ($orden->status) {
            case 'Dibujo':
                $nuevoStatus = 'Elaboracion';
                break;
            case 'Elaboracion':
                $nuevoStatus = 'Entrega';
                break;
            case 'Entrega':
                $nuevoStatus = 'Entregado';
                break;
            default:
                // Ya entregado o error
                return redirect()->back()->with('error', 'No se puede actualizar el estatus.');
        }

        // Actualizamos el estatus
        $ordenModel->update($id, ['status' => $nuevoStatus]);

        return redirect()->to('ordenes')->with('success', 'Estatus actualizado correctamente.');
    }
    public function eliminar($id_ot)
    {

        $model = new OrdenTrabajoModel();

        try {
            // Intenta eliminar la orden
            if ($model->delete($id_ot)) {
                return redirect()->back()->with('success', 'Orden eliminada correctamente');
            } else {
                return redirect()->back()->with('error', 'No se pudo eliminar la orden');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar la orden: ' . $e->getMessage());
        }
    }


}