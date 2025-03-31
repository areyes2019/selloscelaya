<?php // Helper para formatear números
helper('number'); ?>

<div class="ticket-header">
    <strong>Sello Pronto</strong><br> <!-- Usa una configuración o pon tu nombre -->
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
    TOTAL: <?= number_format($pedido['total'], 2) ?>
</div>
<hr>
<div class="ticket-footer">
    ¡Gracias por su compra!
</div>