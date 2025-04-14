<?= $this->extend('Panel/panel_template') ?>

<?= $this->section('contenido') ?>
<h1><?= esc($title) ?></h1>

<div class="card">
    <div class="card-header">
        <h4>Datos del Pedido #<?= $pedido['id'] ?></h4>
    </div>
    <div class="card-body">
        <p><strong>Cliente:</strong> <?= esc($pedido['cliente_nombre']) ?></p>
        <p><strong>Teléfono:</strong> <?= esc($pedido['cliente_telefono']) ?></p>
        <p><strong>Fecha:</strong> <?= date('d/m/Y H:i:s', strtotime($pedido['created_at'])) ?></p>
        <p><strong>Estado:</strong> <?= esc(ucfirst($pedido['estado'])) ?></p>
        <p><strong>Total:</strong> <?= number_format($pedido['total'], 2) ?></p>
    </div>
</div>

<div class="card mt-3">
     <div class="card-header">
        <h4>Detalles del Pedido</h4>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                 <?php foreach ($detalles as $item): ?>
                <tr>
                    <td><?= esc($item['descripcion']) ?></td>
                    <td><?= esc($item['cantidad']) ?></td>
                    <td><?= number_format($item['precio_unitario'], 2) ?></td>
                    <td><?= number_format($item['subtotal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
             <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                    <td><strong><?= number_format($pedido['total'], 2) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mt-3">
     <a href="<?= site_url('ventas/ticket/' . $pedido['id']) ?>" class="btn btn-info"><i class="bi bi-receipt"></i> Ver Ticket</a>
    <a href="<?= site_url('/ventas/pos') ?>" class="btn btn-secondary">Volver al Historial</a>
</div>

<?= $this->endSection() ?>