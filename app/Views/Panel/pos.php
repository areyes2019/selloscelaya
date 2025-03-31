<?php echo $this->extend('Panel/panel_template')?>

<?= $this->section('contenido') ?>
<h1><?= esc($title) ?></h1>
<?php if (session()->has('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>
<?php if (session()->has('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>

<div class="mb-3">
    <a href="<?= site_url('pedidos/new') ?>" class="btn-my text-decoration-none">Nuevo Pedido POS</a>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Teléfono</th>
            <th>Total</th>
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
                    <td><?= number_format($pedido['total'], 2) ?></td>
                    <td><?= esc(ucfirst($pedido['estado'])) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?></td>
                    <td>
                        <a href="<?= site_url('pedidos/show/' . $pedido['id']) ?>" class="btn btn-info btn-sm" title="Ver Detalles"><i class="bi bi-eye"></i> Ver</a>
                        <a href="<?= site_url('pedidos/ticket/' . $pedido['id']) ?>" class="btn btn-secondary btn-sm" title="Ver Ticket"><i class="bi bi-receipt"></i> Ticket</a>
                        <!-- Cuidado con el delete GET, usar POST/DELETE con confirmación en producción -->
                        <a href="<?= site_url('pedidos/delete/' . $pedido['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de querer eliminar este pedido?');" title="Eliminar"><i class="bi bi-trash"></i> Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">No hay pedidos registrados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $pager->links() ?>

<?= $this->endSection() ?>