<?php 

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;
use App\Models\ArticulosModel;
use App\Models\InventarioModel;
use App\Models\BalanceModel;
use CodeIgniter\API\ResponseTrait; // Para respuestas JSON si usas AJAX
use CodeIgniter\Database\Exceptions\DataException;

class PuntoVentaController extends BaseController
{
    use ResponseTrait;
    protected $pedidoModel;
    protected $detallePedidoModel;
    protected $articulosModel;
    protected $inventarioModel;
    protected $balanceModel;
    public function __construct()
    {
        // Puedes cargar helpers, librerías o modelos aquí
        helper(['form', 'url']);
        $this->pedidoModel = new PedidoModel();
        $this->detallePedidoModel = new DetallePedidoModel();
        $this->articulosModel = new ArticulosModel();
        $this->inventarioModel = new InventarioModel();
        $this->balanceModel = new BalanceModel();
    }
    public function index()
    {
        // Obtener la fecha actual
        $hoy = date('Y-m-d');
        $inicioSemana = date('Y-m-d', strtotime('monday this week'));
        $inicioMes = date('Y-m-01');
        
        // Consultas para los resúmenes (solo ventas pagadas)
        $resumenDia = $this->pedidoModel
            ->select('COUNT(*) as total_ventas, SUM(total) as monto_total')
            ->where('DATE(created_at)', $hoy)
            ->where('estado', 'pagado') // Solo ventas pagadas
            ->first();
        
        $resumenSemana = $this->pedidoModel
            ->select('COUNT(*) as total_ventas, SUM(total) as monto_total')
            ->where('created_at >=', $inicioSemana)
            ->where('estado', 'pagado') // Solo ventas pagadas
            ->first();
        
        $resumenMes = $this->pedidoModel
            ->select('COUNT(*) as total_ventas, SUM(total) as monto_total')
            ->where('created_at >=', $inicioMes)
            ->where('estado', 'pagado') // Solo ventas pagadas
            ->first();
        
        // Datos para la vista (paginación muestra todos los pedidos)
        $data = [
            'pedidos' => $this->pedidoModel->orderBy('created_at', 'DESC')->paginate(10),
            'pager' => $this->pedidoModel->pager,
            'title' => 'Punto de venta',
            'resumenDia' => $resumenDia,
            'resumenSemana' => $resumenSemana,
            'resumenMes' => $resumenMes
        ];
        
        return view('Panel/pos', $data);
    }
    public function new()
    {
        $data['articulos'] = $this->articulosModel
            ->select('
                a.id_articulo, 
                a.nombre, 
                a.modelo,
                a.precio_pub as precio_venta,
                a.img,
                a.clave_producto,
                COALESCE(SUM(i.cantidad), 0) as stock_disponible
            ')
            ->from('sellopro_articulos a') // 'a' es alias para la tabla de artículos
            ->join('sellopro_inventario i', 'a.id_articulo = i.id_articulo', 'left')
            ->where('a.stock', 1)
            ->where('a.venta', 1) // Asumiendo que este campo indica si está disponible para venta
            ->groupBy('a.id_articulo, a.nombre, a.modelo, a.precio_pub, a.img, a.clave_producto')
            ->having('stock_disponible >', 0)
            ->orderBy('a.nombre', 'ASC')
            ->findAll();

        $data['titulo'] = 'Nuevo Pedido POS';
        return view('Panel/index_view', $data);
    }
    public function articulos()
    {
        $articulos = $this->articulosModel
            ->select('
                sellopro_articulos.id_articulo, 
                sellopro_articulos.nombre, 
                sellopro_articulos.modelo,
                sellopro_articulos.precio_pub,
                sellopro_articulos.img,
                sellopro_articulos.clave_producto,
                COALESCE(SUM(sellopro_inventario.cantidad), 0) as stock_disponible
            ')
            ->join('sellopro_inventario', 'sellopro_articulos.id_articulo = sellopro_inventario.id_articulo', 'left')
            ->where('sellopro_articulos.stock', 1)
            ->where('sellopro_articulos.venta', 1) // Solo artículos disponibles para venta
            ->groupBy('sellopro_articulos.id_articulo')
            ->having('stock_disponible >', 0) // Solo con stock disponible
            ->findAll();

        return $this->response->setJSON($articulos);
    }
    
    public function create()
    {
        // Validar los datos del formulario
        $rules = [
            'cliente_nombre' => 'required|min_length[3]',
            'cliente_telefono' => 'permit_empty|numeric',
            'anticipo' => 'required|numeric',
            'detalle' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Obtener los datos del formulario
        $data = [
            'cliente_nombre' => $this->request->getPost('cliente_nombre'),
            'cliente_telefono' => $this->request->getPost('cliente_telefono'),
            'total' => $this->request->getPost('total_final_hidden'),
            'estado' => 'pendiente', // o 'completado' según tu lógica
            'anticipo' => $this->request->getPost('anticipo')
        ];

        // Iniciar transacción para asegurar la integridad de los datos
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Guardar en la tabla Pedidos
            $pedidosModel = new PedidoModel();
            $pedidoId = $pedidosModel->insert($data);

            // Guardar los detalles del pedido
            $detalleModel = new DetallePedidoModel();
            $detalles = $this->request->getPost('detalle');

            foreach ($detalles as $item) {
                $detalleData = [
                    'pedido_id' => $pedidoId,
                    'id_articulo' => $item['id_articulo'],
                    'descripcion' => $item['descripcion'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['cantidad'] * $item['precio_unitario']
                ];
                
                $detalleModel->insert($detalleData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error al guardar el pedido');
            }

            return redirect()->to('/ventas/ticket/' . $pedidoId)
                             ->with('success', 'Pedido creado con éxito.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
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
    public function pagar($id)
    {
        $pedido = $this->pedidoModel->find($id);
        
        if (!$pedido) {
            return redirect()->back()->with('error', 'Pedido no encontrado');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Obtener detalles del pedido
            $detalles = $this->detallePedidoModel->where('pedido_id', $id)->findAll();

            foreach ($detalles as $item) {
                $inventario = $this->inventarioModel
                    ->where('id_articulo', $item['id_articulo'])
                    ->first();

                if ($inventario) {
                    $nuevaCantidad = $inventario['cantidad'] - $item['cantidad'];

                    if ($nuevaCantidad < 0) {
                        throw new \Exception('No hay suficiente inventario para el artículo: ' . $item['descripcion']);
                    }

                    $this->inventarioModel->update($inventario['id_entrada'], [
                        'cantidad' => $nuevaCantidad
                    ]);
                } else {
                    throw new \Exception('Artículo no encontrado en inventario: ' . $item['descripcion']);
                }
            }

            // Marcar pedido como pagado
            $this->pedidoModel->update($id, [
                'anticipo' => $pedido['total'],
                'estado' => 'pagado'
            ]);


            $db->transComplete();

            return redirect()->back()->with('success', 'Pedido marcado como pagado correctamente');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function delete($id = null)
    {
        if ($this->pedidoModel->delete($id)) {
             return redirect()->to('ventas/pos')->with('success', 'Pedido eliminado (o marcado como eliminado).');
        } else {
             return redirect()->to('ventas/pos')->with('error', 'No se pudo eliminar el pedido.');
        }
         // Para eliminar permanentemente si usas soft deletes: $this->pedidoModel->delete($id, true);
    }
}