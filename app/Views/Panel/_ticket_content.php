<?php // Helper para formatear números
helper('number'); ?>

<div class="ticket-header">
    <!-- Logo centrado -->
    <div class="text-center mb-2">
        <img src="<?= base_url('public/img/logo2.png') ?>" alt="Logo Sello Pronto" style="max-height: 80px; max-width: 100%;">
    </div>
    
    <strong>Sello Pronto</strong><br>
    Real del Seminario # 122<br>
    Valle del Real, Celaya Gto  461 358 1090<br>
    <hr>
</div>
<div>Fecha: <?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?></div>
<div>Pedido #: <?= esc($pedido['id']) ?></div>
<div>Cliente: <?= esc($pedido['cliente_nombre']) ?></div>
<?php if (!empty($pedido['cliente_telefono'])): ?>
<div>Teléfono: <?= esc($pedido['cliente_telefono']) ?></div>
<?php endif; ?>
<hr>
<div style="margin-bottom: 5px; font-weight: bold;">
    <div class="ticket-item-line">
        <span>Cant.</span>
        <span>Descripción</span>
        <span>P.U.</span>
        <span>Subt.</span>
    </div>
</div>
<hr>
<div class="ticket-items-list">
    <?php foreach ($detalles as $item): ?>
        <div class="ticket-item-line">
            <span><?= esc($item['cantidad']) ?></span>
            <span><?= esc($item['descripcion']) ?></span>
            <span><?= number_format($item['precio_unitario'], 2) ?></span>
            <span><?= number_format($item['subtotal'], 2) ?></span>
        </div>
    <?php endforeach; ?>
</div>
<hr>
<div class="ticket-total">
    <div class="ticket-total-line">
        <span>TOTAL:</span>
        <span><?= number_format($pedido['total'], 2) ?></span>
    </div>
    <div class="ticket-total-line">
        <span>ANTICIPO:</span>
        <span><?= number_format($pedido['anticipo'], 2) ?></span>
    </div>
    <div class="ticket-total-line">
        <span>SALDO:</span>
        <span><?= number_format($pedido['total'] - $pedido['anticipo'], 2) ?></span>
    </div>
</div>
<hr>
<div class="ticket-footer">
    ¡Gracias por su compra!
</div>