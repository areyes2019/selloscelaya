<?php 
//coment'
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;
use App\Models\ArticulosModel;
use App\Models\InventarioModel;
use App\Models\CuentasModel;
use App\Models\BalanceModel;
use App\Models\GastosModel;
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
    protected $cuentasModel;
    public function __construct()
    {
        // Puedes cargar helpers, librerías o modelos aquí
        helper(['form', 'url']);
        $this->pedidoModel = new PedidoModel();
        $this->detallePedidoModel = new DetallePedidoModel();
        $this->articulosModel = new ArticulosModel();
        $this->inventarioModel = new InventarioModel();
        $this->balanceModel = new BalanceModel();
        $this->cuentasModel = new CuentasModel();
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
            ->where('estado', 'pagado')
            ->first();
        
        $resumenSemana = $this->pedidoModel
            ->select('COUNT(*) as total_ventas, SUM(total) as monto_total')
            ->where('created_at >=', $inicioSemana)
            ->where('estado', 'pagado')
            ->first();
        
        $resumenMes = $this->pedidoModel
            ->select('COUNT(*) as total_ventas, SUM(total) as monto_total')
            ->where('created_at >=', $inicioMes)
            ->where('estado', 'pagado')
            ->first();
        
        // Obtener todas las cuentas bancarias según tu modelo
        $cuentasBancarias = $this->cuentasModel
            ->select('id_cuenta, banco, cuenta, saldo')
            ->orderBy('banco', 'ASC')
            ->findAll();
        
        // Calcular saldo total de todas las cuentas
        $saldoTotal = 0;
        foreach ($cuentasBancarias as $cuenta) {
            $saldoTotal += $cuenta['saldo'];
        }
        
        // Formatear saldo total
        $saldoTotalFormateado = number_format($saldoTotal, 2);
        
        // Datos para la vista
        $data = [
            'pedidos' => $this->pedidoModel->orderBy('created_at', 'DESC')->paginate(10),
            'pager' => $this->pedidoModel->pager,
            'title' => 'Punto de venta',
            'resumenDia' => $resumenDia,
            'resumenSemana' => $resumenSemana,
            'resumenMes' => $resumenMes,
            'cuentasBancarias' => $cuentasBancarias,
            'saldoTotal' => $saldoTotal,
            'saldoTotalFormateado' => $saldoTotalFormateado
        ];
        
        return view('Panel/pos', $data);
    }
    public function new()
    {
        // Obtener las cuentas
        $cuentasModel = new CuentasModel();
        $data['cuentas'] = $cuentasModel->findAll();

        // Obtener artículos con su stock (versión mejorada)
        $data['articulos'] = $this->articulosModel
            ->select('a.*, SUM(i.cantidad) as stock_inventario')  // SUM por si hay múltiples registros
            ->from('sellopro_articulos a')
            ->join('sellopro_inventario i', 'a.id_articulo = i.id_articulo', 'inner') // INNER JOIN explícito
            ->where('a.venta', 1)
            ->where('i.id_articulo IS NOT NULL') // Validación adicional
            ->groupBy('a.id_articulo') // Agrupar para evitar duplicados
            ->findAll();

        $data['titulo'] = 'Nuevo Pedido POS';
        return view('Panel/index_view', $data);
    }
    public function mostrar_stock()
    {
        try {
            // Consulta optimizada para el autocomplete
            $articulos = $this->articulosModel
                ->select('
                    a.id_articulo,
                    a.nombre,
                    a.modelo,
                    a.precio_pub,
                    a.clave_producto as clave,
                    a.categoria,
                    COALESCE(SUM(i.cantidad), 0) as stock
                ')
                ->from('sellopro_articulos a')
                ->join('sellopro_inventario i', 'a.id_articulo = i.id_articulo', 'left')
                ->where('a.venta', 1)
                ->groupBy('a.id_articulo, a.nombre, a.modelo, a.precio_pub, a.clave_producto, a.categoria')
                ->having('stock >', 0) // Solo artículos con stock disponible
                ->orderBy('a.nombre', 'ASC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $articulos
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en mostrar_stock: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al cargar el stock de artículos',
                'error' => $e->getMessage()
            ])->setStatusCode(500);
        }
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
            'anticipo' => 'required|numeric|greater_than[0]',
            'detalle' => 'required',
            'banco_id' => 'required|numeric',
            'descuento' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Obtener datos del formulario
        $descuento = (float)$this->request->getPost('descuento') ?? 0;
        $totalSinDescuento = (float)$this->request->getPost('total_final_hidden');
        $montoDescuento = ($totalSinDescuento * $descuento) / 100;
        $totalConDescuento = $totalSinDescuento - $montoDescuento;

        $data = [
            'cliente_nombre' => $this->request->getPost('cliente_nombre'),
            'cliente_telefono' => $this->request->getPost('cliente_telefono'),
            'total' => $totalConDescuento, // Guardamos el total con descuento aplicado
            'total_sin_descuento' => $totalSinDescuento, // Guardamos el total sin descuento
            'descuento' => $descuento, // Porcentaje de descuento
            'monto_descuento' => $montoDescuento, // Cantidad descontada en pesos
            'estado' => 'pendiente',
            'anticipo' => $this->request->getPost('anticipo')
        ];

        // Iniciar transacción
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Obtener cuenta bancaria
            $bancoId = $this->request->getPost('banco_id');
            $cuentasModel = new CuentasModel();
            $banco = $cuentasModel->find($bancoId);
            
            if (!$banco) {
                throw new \Exception('La cuenta bancaria seleccionada no existe');
            }
            
            $montoASumar = (float)$data['anticipo'];

            // 2. Guardar pedido (con validación adicional)
            $pedidosModel = new PedidoModel();
            $pedidoId = $pedidosModel->insert($data);
            
            if (!$pedidoId) {
                throw new \Exception('Error al insertar el pedido: ' . implode(', ', $pedidosModel->errors()));
            }

            // 3. Guardar detalles del pedido
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
                
                if (!$detalleModel->insert($detalleData)) {
                    throw new \Exception('Error al insertar detalle: ' . implode(', ', $detalleModel->errors()));
                }
            }

            // 4. Actualizar saldo del banco
            $nuevoSaldo = $banco['saldo'] + $montoASumar;
            if (!$cuentasModel->update($banco['id_cuenta'], ['saldo' => $nuevoSaldo])) {
                throw new \Exception('Error al actualizar saldo bancario');
            }

            // 5. Registrar movimiento
            $gastosModel = new GastosModel();
            $gastoData = [
                'descripcion' => 'Pago del pedido no. ' . $pedidoId,
                'entrada' => $montoASumar,
                'salida' => 0,
                'cuenta_origen' => 0,
                'cuenta_destino' => $bancoId,
                'fecha_gasto' => date('Y-m-d H:i:s')
            ];
            
            if (!$gastosModel->insert($gastoData)) {
                throw new \Exception('Error al registrar gasto: ' . implode(', ', $gastosModel->errors()));
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error en la transacción de base de datos');
            }

            return redirect()->to('/ventas/ticket/' . $pedidoId)
                             ->with('success', 'Pedido creado con éxito. Se sumó $'.number_format($montoASumar, 2).' al saldo. Nuevo saldo: $'.number_format($nuevoSaldo, 2));

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error en ventas/create: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al procesar el pedido: ' . $e->getMessage());
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
        $request = \Config\Services::request();
        $pedidoId = $request->getPost('pedido_id');
        $cuentaId = $request->getPost('cuenta_id');
        
        // Validaciones básicas
        if (!$pedidoId || !$cuentaId) {
            return redirect()->back()->with('error', 'Datos incompletos');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Obtener el pedido y sus detalles
            $pedido = $this->pedidoModel->find($pedidoId);
            if (!$pedido) {
                throw new \Exception('Pedido no encontrado');
            }

            // Calcular el saldo pendiente (total - anticipo)
            $saldoPendiente = $pedido['total'] - $pedido['anticipo'];
            
            // Si no hay saldo pendiente, no proceder
            if ($saldoPendiente <= 0) {
                throw new \Exception('No hay saldo pendiente por pagar');
            }

            // 2. Calcular inversión y beneficio (proporcional al saldo pagado)
            $detalles = $this->detallePedidoModel->where('pedido_id', $pedidoId)->findAll();
            $totalInversion = 0;
            
            foreach ($detalles as $item) {
                $articulo = $this->articulosModel->find($item['id_articulo']);
                if ($articulo) {
                    $totalInversion += $articulo['precio_prov'] * $item['cantidad'];
                }
            }

            // Calcular proporción del beneficio correspondiente al saldo pagado
            $proporcionPago = $saldoPendiente / $pedido['total'];
            $beneficio = $saldoPendiente - ($totalInversion * $proporcionPago);

            // 3. Registrar la venta (opcional, depende de tu lógica de negocio)
            $ventasModel = new \App\Models\VentasModel();
            $ventasModel->insert([
                'ref' => 'VENTA-'.$pedidoId.'-'.date('YmdHis'),
                'total_neto' => $saldoPendiente, // Registrar solo el saldo pagado
                'inversion' => $totalInversion * $proporcionPago,
                'beneficio' => $beneficio
            ]);

            // 4. Registrar el GASTO (entrada de dinero) - SOLO EL SALDO PENDIENTE
            $gastosModel = new \App\Models\GastosModel();
            $gastosModel->insert([
                'descripcion' => 'Pago del saldo pendiente del pedido #'.$pedidoId,
                'entrada' => $saldoPendiente, // Solo el saldo pendiente
                'salida' => 0,
                'cuenta_origen' => 0,
                'cuenta_destino' => $cuentaId,
                'fecha_gasto' => date('Y-m-d H:i:s')
            ]);

            // 5. Actualizar saldo de la cuenta
            $cuentasModel = new \App\Models\CuentasModel();
            $cuenta = $cuentasModel->find($cuentaId);
            $nuevoSaldo = $cuenta['saldo'] + $saldoPendiente;
            $cuentasModel->update($cuentaId, ['saldo' => $nuevoSaldo]);

            // 6. Actualizar estado del pedido
            $this->pedidoModel->update($pedidoId, [
                'estado' => 'pagado',
                'anticipo' => $pedido['total'], // El anticipo ahora es igual al total (se pagó completo)
            ]);

            $db->transComplete();

            return redirect()->back()->with('success', 
                'Pago registrado. Beneficio: $'.number_format($beneficio, 2).
                ' | Nuevo saldo: $'.number_format($nuevoSaldo, 2)
            );

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Error: '.$e->getMessage());
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