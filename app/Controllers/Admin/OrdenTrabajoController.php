<?php

namespace App\Controllers\Admin; // Ajusta si es necesario

use App\Controllers\BaseController;
use App\Models\OrdenTrabajoModel;
use App\Models\PedidoModel; // Para obtener datos del cliente
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

   
    /**
     * Muestra el formulario para crear una nueva orden de trabajo,
     * pre-llenando datos desde un pedido existente.
     */
    public function etiquetas_pdf()
    {
        // 1. Obtener los datos (MISMA CONSULTA JOIN QUE ANTES)
        $pedidoModel = new PedidoModel();
        $query = $pedidoModel
            ->select([
                'pedidos.id as pedido_id', // Ya no necesitamos el alias 'pedido_id_col'
                'pedidos.cliente_nombre',
                'pedidos.cliente_telefono',
                'pedidos.total',
                'pedidos.anticipo',
                'pedidos.estado',
                // Puedes incluir otros campos si los necesitas mostrar en algún sitio,
                // pero no se piden explícitamente para la etiqueta
                // 'ot.imagen_path',
                // 'ot.color_tinta',
                // 'ot.observaciones'
            ])
            ->join('sellopro_ordenes_trabajo ot', 'ot.pedido_id = pedidos.id', 'left')
            ->where('pedidos.estado', 'pendiente')
            // ->where('pedidos.deleted_at IS NULL') // Añadir si no usas soft deletes en el modelo
            ->orderBy('pedidos.id', 'ASC'); // Opcional: ordenar por ID

        $resultadosCombinados = $query->get()->getResultObject();

        if (empty($resultadosCombinados)) {
            return redirect()->back()->with('message', 'No se encontraron pedidos pendientes para generar etiquetas.');
        }

        // 2. Preparar el HTML para las Etiquetas
        // --- Definir dimensiones en pt ---
        // Ancho etiqueta: 6.7cm * (72pt / 2.54cm) = 189.9pt -> Usaremos 190pt
        $labelWidthPt = 190;
        // Alto etiqueta: 2.5cm * (72pt / 2.54cm) = 70.9pt -> Usaremos 71pt
        $labelHeightPt = 71;
        // Márgenes pequeños entre etiquetas (ej. 5pt horizontal, 10pt vertical)
        $marginHorizontalPt = 5;
        $marginVerticalPt = 10;

        $html = '<!DOCTYPE html>
                 <html lang="es">
                 <head>
                     <meta charset="UTF-8">
                     <title>Etiquetas Pedidos Pendientes</title>
                     <style>
                         @page {
                             margin: 20pt 20pt 20pt 20pt; /* Márgenes de la página Letter */
                         }
                         body {
                             font-family: DejaVu Sans, sans-serif; /* O Arial, Helvetica */
                         }
                         .label-container {
                             /* Contenedor para usar Flexbox o similar si fuera necesario, */
                             /* pero con inline-block puede no ser estrictamente necesario */
                             width: 100%;
                             text-align: left; /* O center si quieres centrar las filas */
                         }
                         .label {
                             width: ' . $labelWidthPt . 'pt;
                             height: ' . $labelHeightPt . 'pt;
                             border: 1px dotted #ccc; /* Borde punteado como guía (quitar si se imprime en etiquetas pre-cortadas) */
                             display: inline-block; /* Para que fluyan una al lado de otra */
                             margin-left: ' . $marginHorizontalPt . 'pt;
                             margin-right: ' . $marginHorizontalPt . 'pt;
                             margin-bottom: ' . $marginVerticalPt . 'pt;
                             padding: 4pt; /* Espacio interno */
                             box-sizing: border-box; /* Padding incluido en el tamaño */
                             overflow: hidden; /* Evitar que el contenido se desborde */
                             vertical-align: top; /* Alinear por la parte superior */
                             page-break-inside: avoid !important; /* Intentar no cortar una etiqueta entre páginas */
                         }
                         .label .pedido-id {
                             font-size: 12pt; /* Más grande */
                             font-weight: bold; /* Negrita */
                             margin: 0 0 2pt 0;
                             padding: 0;
                             line-height: 1.1;
                         }
                          .label .cliente-nombre,
                          .label .cliente-telefono,
                          .label .clave,
                          .label .saldo-info {
                             font-size: 8pt; /* Tamaño normal/pequeño */
                             margin: 0 0 1pt 0;
                             padding: 0;
                             line-height: 1.1; /* Ajustar interlineado */
                             white-space: nowrap; /* Evitar saltos de línea no deseados */
                             overflow: hidden; /* Ocultar si es demasiado largo */
                             text-overflow: ellipsis; /* Añadir puntos suspensivos (...) */
                         }
                         .label .saldo-info {
                            text-align: right; /* Alinear saldo a la derecha */
                            margin-top: 2pt;
                         }
                         .label .saldo-pagado {
                            font-weight: bold;
                            color: green;
                            text-align: center; /* Centrar "Pagado" */
                         }
                         /* Quita el borde si no lo necesitas */
                         /* .label { border: none; } */
                     </style>
                 </head>
                 <body>
                     <div class="label-container">';

        foreach ($resultadosCombinados as $item) {
            // Calcular Saldo o Estado "Pagado"
            $total = floatval($item->total ?? 0);
            $anticipo = floatval($item->anticipo ?? 0);
            $saldoDisplay = '';
            $saldoClass = 'saldo-info'; // Clase base para el saldo

            if (abs($total - $anticipo) < 0.001 && $total != 0) {
                $saldoDisplay = 'Pagado';
                $saldoClass .= ' saldo-pagado'; // Añade clase para "Pagado"
            } else {
                $saldoCalculado = $total - $anticipo;
                $saldoDisplay = 'Saldo: ' . number_format($saldoCalculado, 2, ',', '.') . ' €'; // Ajusta símbolo
            }

            // Calcular Clave (últimos 2 dígitos del teléfono)
            $telefono = $item->cliente_telefono ?? '';
            $clave = !empty($telefono) && strlen($telefono) >= 2 ? substr($telefono, -4) : 'N/A';

            // Construir el HTML de UNA etiqueta
            $html .= '<div class="label">';
            $html .= '<div class="pedido-id">#' . esc($item->pedido_id ?? 'N/A') . '</div>';
            $html .= '<div class="cliente-nombre">' . esc($item->cliente_nombre ?? 'N/A') . '</div>';
            $html .= '<div class="cliente-telefono">Tel: ' . esc($telefono ?: 'N/A') . '</div>';
            $html .= '<div class="clave">Clave: ' . esc($clave) . '</div>';
            $html .= '<div class="' . $saldoClass . '">' . $saldoDisplay . '</div>';
            $html .= '</div>'; // Cierre de .label
        }

        $html .= '   </div> <!-- Cierre de .label-container -->
                 </body>
                 </html>';

        // 3. Configurar y Generar Dompdf
        $pdfOptions = new \Dompdf\Options();
        $pdfOptions->set('isRemoteEnabled', true); // Por si acaso
        $pdfOptions->set('defaultFont', 'DejaVu Sans'); // Soporte UTF-8
        $pdfOptions->set('isHtml5ParserEnabled', true);
        // IMPORTANTE: Establecer DPI puede ayudar a la consistencia de las unidades (96 es común web)
        // $pdfOptions->setDpi(96);

        $dompdf = new \Dompdf\Dompdf($pdfOptions);
        $dompdf->loadHtml($html);

        // Establecer tamaño CARTA (Letter) y orientación PORTRAIT
        $dompdf->setPaper('letter', 'portrait');

        $dompdf->render();

        // 4. Enviar el PDF
        $nombreArchivo = 'etiquetas_pedidos_pendientes_' . date('Ymd_His') . '.pdf';
        if (ob_get_level()) {
            ob_end_clean();
        }
        $dompdf->stream($nombreArchivo, ['Attachment' => 0]); // Attachment 0 para ver en navegador (más fácil ajustar), 1 para descargar
        exit();
    }
    public function descargar_ordenes() // O considera un nombre como visualizar_reporte_combinado()
    {
        // 1. Instanciar el Modelo Principal
        $pedidoModel = new PedidoModel();

        // 2. Construir la Consulta con JOIN
        $query = $pedidoModel
            ->select([
                'pedidos.id as pedido_id_col', // Alias para evitar conflicto con id_ot si lo necesitaras
                'pedidos.cliente_nombre',
                'pedidos.cliente_telefono',
                'pedidos.total',
                'pedidos.anticipo',
                'pedidos.estado',
                // Campos de la tabla ordenes_trabajo (usar alias si hay nombres iguales)
                'ot.imagen_path', // Asumiendo que la tabla ordenes_trabajo tiene alias 'ot'
                'ot.color_tinta',
                'ot.observaciones'
                // Añade más campos de 'ot' si los necesitas
            ])
            // LEFT JOIN desde 'pedidos' a 'sellopro_ordenes_trabajo' (alias 'ot')
            // Incluirá todos los pedidos que cumplan el WHERE,
            // y los datos de 'ot' si hay coincidencia en pedido_id
            ->join('sellopro_ordenes_trabajo ot', 'ot.pedido_id = pedidos.id', 'left')
            // Filtrar por pedidos pendientes
            ->where('pedidos.estado', 'pendiente');

            // Si usas soft deletes en PedidoModel, CI4 añade ->where('pedidos.deleted_at IS NULL') automáticamente.
            // Si no, y tienes una columna 'deleted_at', añádelo manualmente:
            // ->where('pedidos.deleted_at IS NULL')

        // 3. Ejecutar la consulta y obtener resultados como objetos
        $resultadosCombinados = $query->get()->getResultObject(); // Obtiene un array de objetos stdClass

        // Verificar si hay resultados
        if (empty($resultadosCombinados)) {
            // Puedes redirigir o mostrar un mensaje de error amigable
            log_message('info', 'Intento de generar reporte PDF combinado sin pedidos pendientes.');
            return redirect()->back()->with('message', 'No se encontraron pedidos pendientes para generar el reporte.');
        }

        // 4. Preparar el HTML para el PDF
        $html = '<!DOCTYPE html>
                 <html lang="es">
                 <head>
                     <meta charset="UTF-8">
                     <title>Reporte Combinado de Pedidos Pendientes</title>
                     <style>
                         /* Estilos CSS para el PDF */
                         body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
                         table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
                         th, td { border: 1px solid #ccc; padding: 6px; text-align: left; vertical-align: top; word-wrap: break-word; }
                         th { background-color: #e9e9e9; font-weight: bold; }
                         h1 { text-align: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;}
                         img { max-width: 60px; max-height: 60px; display: block; margin-top: 4px; }
                         .saldo { text-align: right; }
                         .pagado { text-align: center; font-weight: bold; color: green; }
                         .observaciones-cell { min-width: 150px; } /* Dar más espacio a observaciones */
                         .na { color: #888; font-style: italic; } /* Estilo para datos no disponibles */
                     </style>
                 </head>
                 <body>
                     <h1>Reporte Combinado de Pedidos Pendientes</h1>
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

        // Iterar sobre los resultados y construir las filas de la tabla
        foreach ($resultadosCombinados as $item) {
            // Calcular Saldo o Estado "Pagado" (de la tabla pedidos)
            $total = floatval($item->total ?? 0);
            $anticipo = floatval($item->anticipo ?? 0);
            $saldoDisplay = '';
            $saldoClass = 'saldo';

            if (abs($total - $anticipo) < 0.001 && $total != 0) { // Comparación segura de flotantes
                $saldoDisplay = 'Pagado';
                $saldoClass = 'pagado';
            } else {
                $saldoCalculado = $total - $anticipo;
                $saldoDisplay = number_format($saldoCalculado, 2, ',', '.') . ' €'; // Ajusta símbolo moneda si es necesario
            }

            // Agregar fila a la tabla HTML
            $html .= '<tr>';
            $html .= '<td>' . esc($item->cliente_nombre ?? 'N/A') . '</td>';
            $html .= '<td>' . esc($item->cliente_telefono ?? 'N/A') . '</td>';

            // Manejo de Imagen (de la tabla ordenes_trabajo) con Base64
            $html .= '<td>';
            $rutaImagen = WRITEPATH . 'uploads/ordenes/' . ($item->imagen_path ?? '');
            // Verifica si imagen_path existe y no es null (debido al LEFT JOIN) y el archivo es válido
            if (!empty($item->imagen_path) && file_exists($rutaImagen) && is_file($rutaImagen) && filesize($rutaImagen) > 0) {
                try {
                    if (!function_exists('mime_content_type')) {
                        log_message('error', '[PDF Generation] La extensión PHP \'fileinfo\' es necesaria y no está habilitada.');
                        $html .= '<span class="na">(Error: Ext. Fileinfo)</span>';
                    } else {
                        $tipoMime = mime_content_type($rutaImagen);
                        if (strpos($tipoMime, 'image/') === 0) { // Verificar si es una imagen
                            $imagenData = file_get_contents($rutaImagen);
                            $imagenBase64 = base64_encode($imagenData);
                            $html .= '<img src="data:' . $tipoMime . ';base64,' . $imagenBase64 . '" alt="Imagen Orden">';
                        } else {
                            $html .= '<span class="na">(Archivo no es imagen)</span>';
                            log_message('warning', '[PDF Generation] Archivo no es tipo imagen: ' . $rutaImagen . ' MIME: ' . $tipoMime);
                        }
                    }
                } catch (\Exception $e) {
                     log_message('error', '[PDF Generation] Error procesando imagen: ' . $e->getMessage() . ' Archivo: ' . $rutaImagen);
                     $html .= '<span class="na">(Error al cargar imagen)</span>';
                }
            } else {
                // Si imagen_path está vacío/null o el archivo no existe/está vacío
                 $html .= '<span class="na">(Sin imagen)</span>';
                 // Opcional: Log si se esperaba imagen pero falló la carga/existencia
                 if (!empty($item->imagen_path)) {
                    log_message('notice', '[PDF Generation] Archivo de imagen no encontrado o vacío: ' . $rutaImagen);
                 }
            }
            $html .= '</td>';

            // Saldo calculado
            $html .= '<td class="' . $saldoClass . '">' . $saldoDisplay . '</td>';

            // Datos de la orden de trabajo (pueden ser null por el LEFT JOIN)
            $html .= '<td>' . esc($item->color_tinta ?? '<span class="na">N/D</span>') . '</td>'; // Mostrar N/D si es null
            $html .= '<td class="observaciones-cell">' . nl2br(esc($item->observaciones ?? '<span class="na">N/D</span>')) . '</td>'; // Mostrar N/D y convertir saltos de línea

            $html .= '</tr>';
        } // Fin del bucle foreach

        // Cerrar tabla y HTML
        $html .= '   </tbody>
                     </table>
                 </body>
                 </html>';

        // 5. Configurar y Generar Dompdf
        $pdfOptions = new \Dompdf\Options(); // Usar el namespace global para evitar conflictos
        $pdfOptions->set('isRemoteEnabled', true); // Permitir cargar recursos externos si fuera necesario (CSS, fuentes, etc.)
        $pdfOptions->set('defaultFont', 'DejaVu Sans'); // Fuente con buen soporte para UTF-8 (acentos, ñ, etc.)
        $pdfOptions->set('isHtml5ParserEnabled', true); // Usar el parser HTML5 más moderno
        // $pdfOptions->setChroot(WRITEPATH); // Solo necesario si usas rutas de archivo directas en src="" y están en writable
        // $pdfOptions->setDpi(96); // Opcional: ajustar DPI si las unidades pt/cm no se ven como esperas

        $dompdf = new \Dompdf\Dompdf($pdfOptions); // Usar el namespace global

        // Cargar el HTML generado
        $dompdf->loadHtml($html);

        // Establecer tamaño del papel (A4) y orientación (landscape = horizontal)
        $dompdf->setPaper('A4', 'landscape');

        // Renderizar el HTML a PDF
        $dompdf->render();

        // 6. Enviar el PDF al Navegador para visualización en línea
        $nombreArchivo = 'reporte_pedidos_pendientes_' . date('Ymd_His') . '.pdf'; // Nombre sugerido si el usuario guarda

        // Limpiar cualquier salida anterior del buffer (IMPORTANTE en CodeIgniter)
        if (ob_get_level()) {
            ob_end_clean();
        }

        // ¡AQUÍ ESTÁ EL CAMBIO PRINCIPAL!
        // 'Attachment' => 0 : Indica al navegador que intente mostrar el PDF en línea.
        // 'Attachment' => 1 : Forzaría la descarga del archivo.
        $dompdf->stream($nombreArchivo, ['Attachment' => 0]);

        // Detener la ejecución del script de CodeIgniter después de enviar el PDF
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
            return redirect()->to('/admin')->with('success', 'Orden de Trabajo creada con éxito.'); // Redirigir al dashboard
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

    /**
     * (FUTURO) Elimina una orden
     */
    public function delete($id = null)
    {
         // TODO: Implementar lógica de eliminación (considera soft deletes)
         return redirect()->to('/ordenes')->with('info', 'Funcionalidad Eliminar no implementada aún.');
    }

     /**
      * Sirve las imágenes subidas de forma segura.
      * Necesitarás una ruta como /ordenes/imagen/(:segment)
      */
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

        return redirect()->to('admin')->with('success', 'Estatus actualizado correctamente.');
    }


}