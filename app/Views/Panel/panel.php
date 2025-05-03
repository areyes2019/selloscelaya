<?= $this->extend('Panel/panel_template') ?>
<?= $this->section('contenido') ?>

<h1 class="mb-4">Dashboard General</h1>
<!-- Tarjetas resumen con igual altura -->
<div class="row mb-4 align-items-stretch">
    <!-- Ventas Semana -->
    <div class="col-md-3">
        <div class="card text-white bg-primary rounded-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <h5 class="card-title">Ventas Semana</h5>
                <p class="card-text fs-4 mb-1">$15,230</p>
                <p class="card-text"><small>23 ventas</small></p>
            </div>
        </div>
    </div>

    <!-- Ventas Mes -->
    <div class="col-md-3">
        <div class="card text-white bg-success rounded-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <h5 class="card-title">Ventas Mes</h5>
                <p class="card-text fs-4 mb-1">$54,800</p>
                <p class="card-text"><small>112 ventas</small></p>
            </div>
        </div>
    </div>

    <!-- Saldo Bancario -->
    <div class="col-md-3">
        <div class="card text-white bg-info rounded-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <h5 class="card-title">Saldo Bancario Total</h5>
                <p class="card-text fs-4 mb-1">
                    $<?= number_format(array_sum(array_column($cuentasBancarias, 'saldo')), 2) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Gastos -->
    <div class="col-md-3">
        <div class="card text-white bg-danger rounded-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <h5 class="card-title">Gastos</h5>
                <p class="card-text fs-4 mb-1">$6,320</p>
            </div>
        </div>
    </div>
</div>


<!-- Sección en una sola fila: Cuentas, Cotizaciones, Ordenes -->
<div class="row mb-4">
    <!-- Cuentas Bancarias -->
    <div class="col-md-4 d-flex">
        <div class="card rounded-0 shadow-sm w-100">
            <div class="card-header bg-dark text-white">Cuentas Bancarias</div>
            <div class="card-body p-0 d-flex flex-column">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Banco</th>
                            <th>Cuenta</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalSaldo = 0; // Inicializamos el contador de total
                        foreach ($cuentasBancarias as $cuenta): 
                            $totalSaldo += $cuenta['saldo']; // Sumamos cada saldo
                        ?>
                            <tr>
                                <td><?= esc($cuenta['banco']) ?></td>
                                <td><?= esc($cuenta['cuenta']) ?></td>
                                <td>$<?= number_format($cuenta['saldo'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <!-- Fila del total -->
                        <tr class="table-active fw-bold">
                            <td colspan="2" class="text-end">Total:</td>
                            <td>$<?= number_format($totalSaldo, 2) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cotizaciones Recientes -->
    <div class="col-md-4 d-flex">
        <div class="card rounded-0 shadow-sm w-100">
            <div class="card-header bg-dark text-white">Últimas 5 Cotizaciones</div>
            <div class="card-body p-0 d-flex flex-column">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Cliente</th>
                            <th>Folio</th>
                            <th>Monto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimasCotizaciones as $cotizacion): ?>
                            <tr>
                                <td><?= esc($cotizacion['nombre_cliente']) ?></td>
                                <td><?= esc($cotizacion['id_cotizacion']) ?></td>
                                <td>$<?= number_format($cotizacion['total'], 2) ?></td>
                                <td class="text-nowrap">
                                    <a href="<?= base_url('pagina_cotizador/' . $cotizacion['slug']) ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Ver cotización">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= base_url('cotizaciones/pdf/' . $cotizacion['id_cotizacion']) ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       title="Descargar PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Órdenes de Trabajo Recientes -->
    <div class="col-md-4 d-flex">
        <div class="card rounded-0 shadow-sm w-100">
            <div class="card-header bg-dark text-white">Órdenes de Trabajo Recientes</div>
            <div class="card-body p-0 d-flex flex-column">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Cliente</th>
                            <th>OT</th>
                            <th>Status</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimasOrdenes as $orden): ?>
                            <?php 
                            // Mapeo de estados a clases de badge
                            $badgeClasses = [
                                'dibujo' => 'bg-primary',
                                'elaboracion' => 'bg-warning',
                                'entrega' => 'bg-success',
                                'entregado' => 'bg-success',
                                'cancelado' => 'bg-danger'
                            ];
                            $status = strtolower($orden->status);
                            $badgeClass = $badgeClasses[$status] ?? 'bg-secondary';
                            ?>
                            <tr>
                                <td><?= esc($orden->cliente_nombre) ?></td>
                                <td>OT-<?= esc($orden->id_ot) ?></td>
                                <td>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= ucfirst($orden->status) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('ordenes/ver/' . $orden->id_ot) ?>" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Órdenes de compra recientes -->
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card rounded-0 shadow-sm">
            <div class="card-header bg-dark text-white">Últimas 5 Órdenes de Compra</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Proveedor</th>
                            <th>Folio</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimasOrdenesCompra as $orden): ?>
                            <?php
                            $pagado = (bool)($orden['pagado'] ?? 0); // Accede como array
                            $entregada = (bool)($orden['entregada'] ?? 0);
                            
                            if ($pagado && $entregada) {
                                $estado = 'Completado';
                                $badgeClass = 'bg-success';
                            } elseif ($entregada) {
                                $estado = 'Entregado';
                                $badgeClass = 'bg-primary';
                            } elseif ($pagado) {
                                $estado = 'Pagado';
                                $badgeClass = 'bg-info';
                            } else {
                                $estado = 'Pendiente';
                                $badgeClass = 'bg-warning';
                            }
                            ?>
                            <tr>
                                <td><?= esc($orden['nombre_proveedor'] ?? 'Sin proveedor') ?></td>
                                <td><?= esc($orden['id_pedido']) ?></td>
                                <td>$<?= number_format($orden['total'], 2) ?></td>
                                <td><?= date('Y-m-d', strtotime($orden['created_at'])) ?></td>
                                <td>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= $estado ?>
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    <a href="<?= base_url('compras/ver/' . $orden['id_pedido']) ?>" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= base_url('compras/pdf/' . $orden['id_pedido']) ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       title="Descargar PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
