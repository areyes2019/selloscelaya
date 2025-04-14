<?php echo $this->extend('Panel/panel_template')?>

<?= $this->section('contenido') ?>
<h1><?= esc($title) ?></h1>
<?php if (session()->has('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>
<?php if (session()->has('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<!-- Sección de resúmenes -->
<div class="row mb-4">
    <!-- Resumen del día -->
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">
                <i class="bi bi-calendar-day me-2"></i>Ventas del Día
            </div>
            <div class="card-body">
                <h5 class="card-title"><?= $resumenDia['total_ventas'] ?? 0 ?> ventas</h5>
                <p class="card-text">Total: $<?= number_format($resumenDia['monto_total'] ?? 0, 2) ?></p>
            </div>
            <div class="card-footer">
                <?= date('d/m/Y') ?>
            </div>
        </div>
    </div>
    
    <!-- Resumen de la semana -->
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">
                <i class="bi bi-calendar-week me-2"></i>Ventas de la Semana
            </div>
            <div class="card-body">
                <h5 class="card-title"><?= $resumenSemana['total_ventas'] ?? 0 ?> ventas</h5>
                <p class="card-text">Total: $<?= number_format($resumenSemana['monto_total'] ?? 0, 2) ?></p>
            </div>
            <div class="card-footer">
                Semana <?= date('W') ?> del año
            </div>
        </div>
    </div>
    
    <!-- Resumen del mes -->
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3">
            <div class="card-header">
                <i class="bi bi-calendar-month me-2"></i>Ventas del Mes
            </div>
            <div class="card-body">
                <h5 class="card-title"><?= $resumenMes['total_ventas'] ?? 0 ?> ventas</h5>
                <p class="card-text">Total: $<?= number_format($resumenMes['monto_total'] ?? 0, 2) ?></p>
            </div>
            <div class="card-footer">
                <?= ucfirst(strftime('%B %Y')) ?>
            </div>
        </div>
    </div>
</div>

<div class="mb-3">
    <a href="<?= site_url('ventas/new') ?>" class="btn btn-primary text-decoration-none">
        <i class="bi bi-plus-circle me-2"></i>Nuevo Pedido POS
    </a>
</div>

<table class="table table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Teléfono</th>
            <th>Total</th>
            <th>Pago</th>
            <th>Saldo</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($pedidos)): ?>
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td><?= esc($pedido['id']) ?></td>
                    <td><?= esc($pedido['cliente_nombre']) ?></td>
                    <td><?= esc($pedido['cliente_telefono']) ?></td>
                    <td>$<?= number_format($pedido['total'], 2) ?></td>
                    <td>$<?= number_format($pedido['anticipo'] ?? 0, 2) ?></td>
                    <td class="<?= ($pedido['total'] - ($pedido['anticipo'] ?? 0)) > 0 ? 'text-danger fw-bold' : 'text-success' ?>">
                        $<?= number_format(($pedido['total'] - ($pedido['anticipo'] ?? 0)), 2) ?>
                    </td>
                    <td>
                        <span class="badge bg-<?= 
                            $pedido['estado'] == 'pagado' ? 'success' : 
                            ($pedido['estado'] == 'parcial' ? 'warning' : 'secondary') 
                        ?>">
                            <?= esc(ucfirst($pedido['estado'])) ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="<?= site_url('ventas/show/' . $pedido['id']) ?>" class="btn btn-info btn-sm" title="Ver Detalles">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?= site_url('ventas/ticket/' . $pedido['id']) ?>" class="btn btn-secondary btn-sm" title="Ver Ticket">
                                <i class="bi bi-receipt"></i>
                            </a>
                            
                            <?php if ($pedido['estado'] != 'pagado'): ?>
                                <form action="<?= site_url('ventas/pagar/' . $pedido['id']) ?>" method="post" style="display: inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-success btn-sm" title="Pagar" onclick="return confirm('¿Marcar este pedido como pagado?')">
                                        <i class="bi bi-cash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <a href="<?= site_url('ventas/delete/' . $pedido['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de querer eliminar este pedido?');" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" class="text-center py-4">No hay pedidos registrados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<div class="pagination-container">
    <?= $pager->links() ?>
</div>

<?= $this->endSection() ?>