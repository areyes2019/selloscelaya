<?php

namespace App\Controllers\Admin; // Ajusta si es necesario

use App\Controllers\BaseController;
use App\Models\OrdenTrabajoModel;
use App\Models\PedidoModel; // Para obtener datos del cliente
use CodeIgniter\Exceptions\PageNotFoundException;

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
     * Muestra el dashboard con las órdenes por status.
     */
    public function index()
    {
        $data['title'] = 'Dashboard Órdenes de Trabajo';
        $data['ordenesPorStatus'] = $this->ordenTrabajoModel->getOrdenesPorStatus();

        // Pasamos los nombres de los status para las pestañas
        $data['statuses'] = ['Diseño', 'Elaboracion', 'Entrega'];

        return view('Panel/ordenes_dashboard', $data); // Creamos esta vista más adelante
    }


    /**
     * Muestra el formulario para crear una nueva orden de trabajo,
     * pre-llenando datos desde un pedido existente.
     */
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
            'status_inicial' => 'required|in_list[Diseño,Elaboracion,Entrega]' // Validar status inicial
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
            'status'         => $this->request->getPost('status_inicial') ?? 'Diseño', // Usar status del form
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

}