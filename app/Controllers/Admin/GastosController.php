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
    protected $gastosModel;
    protected $pedidoModel;
    protected $articulosModel;
    protected $detallePedidoModel;

    public function __construct()
    {
        $this->gastosModel = new GastosModel();
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
            'gastos' => $this->gastosModel->orderBy('fecha_gasto', 'DESC')->findAll(),
        ];

        return view('Panel/gastos', $data);
    }

    // Mostrar formulario de creación
    public function nuevo()
    {
        $data = [
            'title' => 'Nuevo Movimiento',
            'cuentas'=> $this->cuentasModel->findAll()
        ];

        return view('Panel/nuevo_gasto', $data);
    }
    public function guardar()
    {
        helper(['form']);

        $rules = [
            'descripcion'      => 'required|min_length[3]|max_length[255]',
            'monto'            => 'required|numeric|greater_than[0]',
            'fecha_gasto'      => 'required|valid_date',
            'cuenta_origen'    => 'required|integer|greater_than[0]',
            'tipo_movimiento'  => 'required|in_list[entrada,salida]' // sigue validándose pero no se guarda
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $postData = $this->request->getPost();

        // Buscar cuenta
        $cuentasModel = new \App\Models\CuentasModel();
        $cuenta = $cuentasModel->find((int)$postData['cuenta_origen']);

        if (!$cuenta) {
            return redirect()->back()->withInput()->with('error', 'Cuenta no encontrada.');
        }

        // Verificar saldo si es salida
        if ($postData['tipo_movimiento'] === 'salida' && $postData['monto'] > $cuenta['saldo']) {
            return redirect()->back()->withInput()->with('error', 'Saldo insuficiente en la cuenta.');
        }

        // Preparar datos
        $data = [
            'descripcion'    => $postData['descripcion'],
            'entrada'        => $postData['tipo_movimiento'] === 'entrada' ? $postData['monto'] : 0,
            'salida'         => $postData['tipo_movimiento'] === 'salida'  ? $postData['monto'] : 0,
            'cuenta_origen'  => (int)$postData['cuenta_origen'],
            'cuenta_destino' => null,
            'fecha_gasto'    => $postData['fecha_gasto'],
        ];

        $gastosModel = new \App\Models\GastosModel();

        if ($gastosModel->insert($data)) {
            // Actualizar saldo de cuenta
            $nuevoSaldo = $postData['tipo_movimiento'] === 'entrada'
                ? $cuenta['saldo'] + $postData['monto']
                : $cuenta['saldo'] - $postData['monto'];

            $cuentasModel->update($data['cuenta_origen'], ['saldo' => $nuevoSaldo]);

            return redirect()->to('/gastos/inicio')
                             ->with('message', 'Movimiento registrado correctamente.');
        } else {
            // Registrar errores del modelo si falla
            log_message('error', 'Error insertando gasto: ' . json_encode($gastosModel->errors()));
        }

        return redirect()->back()->withInput()->with('error', 'Error al registrar el movimiento.');
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
    public function procesar()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'cuenta_origen' => 'required|numeric',
            'cuenta_destino' => 'required|numeric|differs[cuenta_origen]',
            'monto' => 'required|numeric|greater_than[0]',
            'descripcion' => 'required|string|min_length[3]',
            'fecha' => 'required|valid_date'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $cuentaOrigenId = $this->request->getPost('cuenta_origen');
        $cuentaDestinoId = $this->request->getPost('cuenta_destino');
        $monto = (float)$this->request->getPost('monto');
        $descripcion = $this->request->getPost('descripcion');
        $fecha = $this->request->getPost('fecha');

        // Verificar saldo suficiente en cuenta origen
        $cuentaOrigen = $this->cuentasModel->find($cuentaOrigenId);
        if ($cuentaOrigen['saldo'] < $monto) {
            return redirect()->back()->withInput()->with('error', 'Saldo insuficiente en la cuenta de origen');
        }

        // Iniciar transacción para asegurar integridad de datos
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Registrar el gasto (salida) en cuenta origen
            $this->gastosModel->insert([
                'descripcion' => $descripcion,
                'salida' => $monto,
                'cuenta_origen' => $cuentaOrigenId,
                'cuenta_destino' => $cuentaDestinoId,
                'fecha_gasto' => $fecha
            ]);

            // Registrar el ingreso en cuenta destino
            $this->gastosModel->insert([
                'descripcion' => $descripcion,
                'entrada' => $monto,
                'cuenta_origen' => $cuentaOrigenId,
                'cuenta_destino' => $cuentaDestinoId,
                'fecha_gasto' => $fecha
            ]);

            // Actualizar saldos
            $this->cuentasModel->where('id_cuenta', $cuentaOrigenId)
                              ->set('saldo', 'saldo - ' . $monto, false)
                              ->update();

            $this->cuentasModel->where('id_cuenta', $cuentaDestinoId)
                              ->set('saldo', 'saldo + ' . $monto, false)
                              ->update();

            $db->transComplete();

            return redirect()->back()->with('success', 'Transferencia realizada con éxito');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Error al realizar la transferencia: ' . $e->getMessage());
        }
    }

}