<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\GastosModel;
use App\Models\PedidoModel;
use App\Models\ArticulosModel;
use App\Models\DetallePedidoModel;
use App\Models\CuentasModel;
use CodeIgniter\Exceptions\PageNotFoundException;
class GastosController extends BaseController
{
    protected $gastoModel;
    protected $pedidoModel;
    protected $articulosModel;
    protected $detallePedidoModel;

    public function __construct()
    {
        $this->gastoModel = new GastosModel();
        $this->pedidoModel = new PedidoModel();
        $this->articulosModel = new ArticulosModel();
        $this->detallePedidoModel = new DetallePedidoModel();
        $this->cuentasModel = new CuentasModel();
    }

    // Listar todos los gastos
    public function index()
    {
        $data = [
            'title' => 'Gestión de Gastos',
            'gastos' => $this->gastoModel->orderBy('fecha_gasto', 'DESC')->findAll(),
        ];

        return view('Panel/gastos', $data);
    }

    // Mostrar formulario de creación
    public function nuevo()
    {
        $data = [
            'title' => 'Registrar Nuevo Gasto',
            'cuentas'=> $this->cuentasModel->findAll()
        ];

        return view('Panel/nuevo_gasto', $data);
    }
    public function guardar()
    {
        // Validar con reglas del modelo
        if (!$this->validate($this->gastoModel->validationRules, $this->gastoModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Obtener inputs
        $descripcion     = $this->request->getPost('descripcion');
        $monto           = floatval($this->request->getPost('monto'));
        $fecha           = $this->request->getPost('fecha_gasto');
        $cuentaOrigenId  = intval($this->request->getPost('cuenta_origen'));
        $esTransferencia = $this->request->getPost('es_transferencia') ? true : false;

        // Solo procesar cuenta_destino si es transferencia
        $cuentaDestinoId = null; // Por defecto NULL (no 0)
        if ($esTransferencia) {
            $cuentaDestinoId = intval($this->request->getPost('cuenta_destino'));
        }

        $cuentasModel = new \App\Models\CuentasModel();
        $cuentaOrigen = $cuentasModel->find($cuentaOrigenId);

        if (!$cuentaOrigen) {
            return redirect()->back()->withInput()->with('errors', ['Cuenta de origen no encontrada.']);
        }

        if ($cuentaOrigen['saldo'] < $monto) {
            return redirect()->back()->withInput()->with('errors', ['Saldo insuficiente en la cuenta de origen.']);
        }

        if ($esTransferencia) {
            if ($cuentaOrigenId === $cuentaDestinoId) {
                return redirect()->back()->withInput()->with('errors', ['No puedes transferir a la misma cuenta.']);
            }

            $cuentaDestino = $cuentasModel->find($cuentaDestinoId);
            if (!$cuentaDestino) {
                return redirect()->back()->withInput()->with('errors', ['Cuenta de destino no encontrada.']);
            }
        }

        // Iniciar transacción
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insertar gasto (cuenta_destino será NULL si no es transferencia)
            $this->gastoModel->insert([
                'descripcion'    => $descripcion,
                'monto'          => $monto,
                'fecha_gasto'    => $fecha,
                'cuenta_origen'  => $cuentaOrigenId,
                'cuenta_destino' => $esTransferencia ? $cuentaDestinoId : null, // Forzamos NULL si no es transferencia
            ]);

            // Restar saldo cuenta origen
            $cuentasModel->update($cuentaOrigenId, [
                'saldo' => $cuentaOrigen['saldo'] - $monto
            ]);

            // Si es transferencia, sumar saldo a cuenta destino
            if ($esTransferencia) {
                $cuentasModel->update($cuentaDestinoId, [
                    'saldo' => $cuentaDestino['saldo'] + $monto
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error al procesar la transacción.');
            }

            return redirect()->to('/gastos/inicio')->with('message', 'Gasto registrado exitosamente');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('errors', [$e->getMessage()]);
        }
    }

    // Mostrar detalles de un gasto
   // Mostrar detalles de un gasto
    public function mostrar($id)
    {

        $gasto = $this->gastoModel->find($id);

        if (empty($gasto)) {
            throw new PageNotFoundException('No se encontró el gasto con ID: ' . $id);
        }

        $data = [
            'title' => 'Detalles del Gasto',
            'gasto' => $gasto
        ];

        // Si necesitas devolver JSON en algún caso específico
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($gasto);
        }

        return view('Panel/ver_gasto', $data);
    }

    // Mostrar formulario de edición
    public function editar($id)
    {
        $gasto = $this->gastoModel->find($id);

        if (empty($gasto)) {
            throw new PageNotFoundException('No se encontró el gasto con ID: ' . $id);
        }

        $data = [
            'title' => 'Editar Gasto',
            'gasto' => $gasto
        ];

        return view('Panel/editar_gasto', $data);
    }

    // Actualizar un gasto
    public function actualizar($id)
    {
        // Validar datos
        if (!$this->validate($this->gastoModel->validationRules, $this->gastoModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Obtener datos del formulario
        $data = [
            'descripcion' => $this->request->getPost('descripcion'),
            'monto'       => $this->request->getPost('monto'),
            'fecha_gasto' => $this->request->getPost('fecha_gasto')
        ];

        // Actualizar en la base de datos
        $this->gastoModel->update($id, $data);

        return redirect()->to('/gastos/inicio')->with('message', 'Gasto actualizado exitosamente');
    }

    // Eliminar un gasto
    public function eliminar($id)
    {

        $gasto = $this->gastoModel->find($id);

        if (empty($gasto)) {
            throw new PageNotFoundException('No se encontró el gasto con ID: ' . $id);
        }

        $this->gastoModel->delete($id);

        return redirect()->to('/gastos/inicio')->with('message', 'Gasto eliminado exitosamente');
    }
    public function reporteFinanciero()
    {
        // Valores por defecto (este mes)
        $defaultStart = date('Y-m-01');
        $defaultEnd = date('Y-m-t');
        
        $fechaInicio = $this->request->getGet('fecha_inicio') ?? $defaultStart;
        $fechaFin = $this->request->getGet('fecha_fin') ?? $defaultEnd;

        
        // Validar y formatear fechas
        try {
            $dateInicio = new \DateTime($fechaInicio);
            $dateFin = new \DateTime($fechaFin);
            
            if ($dateInicio > $dateFin) {
                list($dateInicio, $dateFin) = [$dateFin, $dateInicio];
                list($fechaInicio, $fechaFin) = [$fechaFin, $fechaInicio];
            }
            
            $fechaInicioFull = $dateInicio->format('Y-m-d 00:00:00');
            $fechaFinFull = $dateFin->format('Y-m-d 23:59:59');
            
        } catch (\Exception $e) {
            $fechaInicioFull = (new \DateTime($defaultStart))->format('Y-m-d 00:00:00');
            $fechaFinFull = (new \DateTime($defaultEnd))->format('Y-m-d 23:59:59');
        }
        
        // 1. Obtener total de gastos
        $totalGastos = $this->gastoModel
            ->where('fecha_gasto >=', $fechaInicioFull)
            ->where('fecha_gasto <=', $fechaFinFull)
            ->selectSum('monto')
            ->get()
            ->getRowArray()['monto'] ?? 0;
        
        // 2. Obtener total de ventas (solo pedidos pagados)
        $totalVentas = $this->pedidoModel
            ->where('created_at >=', $fechaInicioFull)
            ->where('created_at <=', $fechaFinFull)
            ->where('estado', 'pagado')
            ->selectSum('total')
            ->get()
            ->getRowArray()['total'] ?? 0;
        
        // 3. Obtener total invertido en productos
        $pedidosPeriodo = $this->pedidoModel
            ->where('created_at >=', $fechaInicioFull)
            ->where('created_at <=', $fechaFinFull)
            ->where('estado', 'pagado')
            ->findAll();
        
        $totalInvertido = 0;
        
        foreach ($pedidosPeriodo as $pedido) {
            $detalles = $this->detallePedidoModel->where('pedido_id', $pedido['id'])->findAll();
            
            foreach ($detalles as $detalle) {
                $articulo = $this->articulosModel->find($detalle['id_articulo']);
                if ($articulo) {
                    $totalInvertido += $articulo['precio_prov'] * $detalle['cantidad'];
                }
            }
        }
        
        // 4. Calcular utilidades
        $utilidadesNetas = $totalVentas - $totalGastos - $totalInvertido;
        $margenGanancia = $totalVentas > 0 ? ($utilidadesNetas / $totalVentas) * 100 : 0;
        
        return view('Panel/reporte_financiero', [
            'title' => 'Reporte Financiero',
            'fecha_inicio' => substr($fechaInicioFull, 0, 10),
            'fecha_fin' => substr($fechaFinFull, 0, 10),
            'total_gastos' => $totalGastos,
            'total_ventas' => $totalVentas,
            'total_invertido' => $totalInvertido,
            'utilidades_netas' => $utilidadesNetas,
            'margen_ganancia' => $margenGanancia
        ]);
    }

}