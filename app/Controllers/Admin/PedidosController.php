<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;
use CodeIgniter\API\ResponseTrait; // Para respuestas JSON si usas AJAX
use CodeIgniter\Database\Exceptions\DataException;

class PedidosController extends BaseController
{
    use ResponseTrait; // Útil si decides usar AJAX

    protected $pedidoModel;
    protected $detallePedidoModel;
    protected $db;

    public function __construct()
    {
        $this->pedidoModel = new PedidoModel();
        $this->detallePedidoModel = new DetallePedidoModel();
        $this->db = \Config\Database::connect(); // Para transacciones
        helper(['form', 'url', 'number']); // Carga helpers útiles
    }

    /**
     * Muestra la lista de pedidos (historial)
     */
    public function index()
    {

        $data['pedidos'] = $this->pedidoModel->orderBy('created_at', 'DESC')->paginate(15);
        $data['pager'] = $this->pedidoModel->pager;
        $data['title'] = 'Punto de venta';

        return view('Panel/pos', $data);
    }

    /**
     * Muestra el formulario para crear un nuevo pedido (la interfaz POS)
     */
    public function new()
    {
        $data['title'] = 'Nuevo Pedido POS';
        // Puedes pasar datos iniciales si es necesario, ej: lista de productos
        return view('Panel/new', $data);
    }

    /**
     * Procesa la creación de un nuevo pedido
     */
    public function create()
    {
        $validation = \Config\Services::validation();

        // --- Validación ---
        $rules = [
            'cliente_nombre' => 'required|min_length[3]|max_length[150]',
            'cliente_telefono' => 'permit_empty|max_length[25]',
            'detalle' => 'required', // Asegura que al menos haya un item
            'detalle.*.descripcion' => 'required|max_length[255]',
            'detalle.*.cantidad' => 'required|numeric|greater_than[0]',
            'detalle.*.precio_unitario' => 'required|numeric|greater_than_equal_to[0]',
            'anticipo' => 'permit_empty|numeric|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            // Vuelve al formulario con errores y datos antiguos
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // --- Procesamiento ---
        $this->db->transStart(); // Iniciar transacción

        try {
            // 1. Crear el Pedido Principal
            $pedidoData = [
                'cliente_nombre' => $this->request->getPost('cliente_nombre'),
                'cliente_telefono' => $this->request->getPost('cliente_telefono'),
                'estado' => 'completado',
                'total' => 0,
                'anticipo' => $this->request->getPost('anticipo') ?? 0, // Nuevo campo
                'saldo' => 0 // Se calculará después
            ];

            $pedidoId = $this->pedidoModel->insert($pedidoData, true); // true para retornar ID

            if (!$pedidoId) {
                throw new DataException('No se pudo crear el registro principal del pedido.');
            }

            // 2. Procesar los Detalles del Pedido
            $detalles = $this->request->getPost('detalle');
            $granTotal = 0;

            foreach ($detalles as $item) {
                $cantidad = (int) $item['cantidad'];
                $precioUnitario = (float) $item['precio_unitario'];
                $subtotal = $cantidad * $precioUnitario;
                $granTotal += $subtotal;

                $detalleData = [
                    'pedido_id' => $pedidoId,
                    'descripcion' => trim($item['descripcion']),
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotal,
                ];

                if (!$this->detallePedidoModel->insert($detalleData)) {
                     // Si falla un detalle, la transacción hará rollback
                     throw new DataException('Error al guardar un detalle del pedido.');
                }
            }

            // 3. Actualizar el Total del Pedido Principal
            $anticipo = (float)($this->request->getPost('anticipo') ?? 0);
            $this->pedidoModel->update($pedidoId, [
                'total' => $granTotal,
                'anticipo' => $anticipo,
                'saldo' => $granTotal - $anticipo
            ]);

            $this->db->transComplete(); // Finalizar transacción (Commit o Rollback)

            if ($this->db->transStatus() === false) {
                 // La transacción falló (Rollback automático)
                 log_message('error', 'Fallo en la transacción al crear pedido.');
                 return redirect()->back()->withInput()->with('error', 'Ocurrió un error al guardar el pedido. Inténtalo de nuevo.');
            }

            // Éxito
            return redirect()->to('/pedidos/ticket/' . $pedidoId) // Redirige a ver el ticket
                             ->with('success', 'Pedido creado con éxito.');


        } catch (\Exception $e) {
            $this->db->transRollback(); // Asegurar rollback en caso de excepción
            log_message('error', 'Error al crear pedido: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error inesperado: ' . $e->getMessage());
        }
    }

    /**
     * Muestra los detalles de un pedido específico (para ver o imprimir ticket)
     */
    public function show($id = null)
    {
        $pedido = $this->pedidoModel->find($id);

        if (!$pedido) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pedido no encontrado');
        }

        // Cargar los detalles asociados (si usas la relación del modelo)
        // $detalles = $this->detallePedidoModel->where('pedido_id', $id)->findAll();
        // O usando la relación definida en PedidoModel (si usas Entidades y la relación)
        // Necesitas cargar la relación explícitamente si no usas Entidades o eager loading
        $data['pedido'] = $pedido;
        $data['detalles'] = $this->detallePedidoModel->where('pedido_id', $id)->findAll();
        $data['title'] = 'Detalle Pedido #' . $id;

        return view('Panel/show', $data); // Vista que muestra los detalles (puede ser el ticket)
    }

     /**
     * Muestra una vista específica para el ticket (puede ser la misma que show o una simplificada)
     * Esta función es llamada después de crear el pedido.
     */
    public function ticket($id = null)
    {
        $data['pedido'] = $this->pedidoModel->find($id);

        if (!$data['pedido']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pedido no encontrado para generar ticket');
        }

        $data['detalles'] = $this->detallePedidoModel->where('pedido_id', $id)->findAll();
        $data['title'] = 'Ticket Pedido #' . $id;

        // Esta vista debe contener el botón para descargar
        return view('Panel/ticket', $data);
    }


    /**
     * Genera y fuerza la descarga de un ticket (ejemplo simple en HTML/Texto)
     * Para PDF necesitarás una librería como TCPDF o Dompdf
     */
    public function downloadTicket($id = null)
    {
        $pedido = $this->pedidoModel->find($id);

        if (!$pedido) {
             return redirect()->back()->with('error', 'Pedido no encontrado para descargar.');
        }

        $detalles = $this->detallePedidoModel->where('pedido_id', $id)->findAll();

        // --- Generación simple del contenido del Ticket (Texto/HTML básico) ---
        $content = view('Panel/_ticket_content', ['pedido' => $pedido, 'detalles' => $detalles]);

        // --- Configurar Headers para descarga ---
        $filename = "ticket_pedido_" . $id . ".html"; // O .txt si es texto plano

        // Forzar descarga
        return $this->response
            ->setHeader('Content-Type', 'text/html') // Cambiar a 'text/plain' si es texto plano
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content)
            ->send(); // No uses return view() aquí

        // --- Alternativa usando una librería PDF (Ej: Dompdf) ---
        /*
        $dompdf = new \Dompdf\Dompdf();
        $html = view('pedidos/_ticket_pdf_template', ['pedido' => $pedido, 'detalles' => $detalles]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A7', 'portrait'); // O un tamaño de ticket típico
        $dompdf->render();
        $filename = "ticket_pedido_" . $id . ".pdf";
        $dompdf->stream($filename, ['Attachment' => 1]); // 1 para forzar descarga
        exit(); // Importante salir después de stream
        */
    }


    /**
     * Muestra el formulario para editar (Opcional para POS)
     * Editar pedidos completados puede ser complejo o no deseado.
     */
    public function edit($id = null)
    {
       // Implementar si es necesario, similar a show() pero con un formulario.
       // Considera las implicaciones de editar un pedido ya procesado.
        return redirect()->to('/pedidos')->with('info', 'Función Editar no implementada.');
    }

    /**
     * Procesa la actualización de un pedido (Opcional para POS)
     */
    public function update($id = null)
    {
        // Implementar lógica de actualización y validación si se habilita edit()
        return redirect()->to('/pedidos')->with('info', 'Función Actualizar no implementada.');
    }

    /**
     * Elimina un pedido (Soft Delete si está configurado)
     */
    public function delete($id = null)
    {
        if ($this->pedidoModel->delete($id)) {
             return redirect()->to('/pedidos')->with('success', 'Pedido eliminado (o marcado como eliminado).');
        } else {
             return redirect()->to('/pedidos')->with('error', 'No se pudo eliminar el pedido.');
        }
         // Para eliminar permanentemente si usas soft deletes: $this->pedidoModel->delete($id, true);
    }
}