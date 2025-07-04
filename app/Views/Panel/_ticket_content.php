<?php // Helper para formatear números
helper('number'); 
$tieneDescuento = $pedido['descuento'] > 0;
?>

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

<?php if ($tieneDescuento): ?>
<!-- Sección de resumen con descuentos (solo si aplica) -->
<div class="ticket-summary">
    <div class="ticket-summary-line">
        <span>SUBTOTAL:</span>
        <span>$<?= number_format($pedido['total_sin_descuento'], 2) ?></span>
    </div>
    <div class="ticket-summary-line">
        <span>DESCUENTO (<?= $pedido['descuento'] ?>%):</span>
        <span>-$<?= number_format($pedido['monto_descuento'], 2) ?></span>
    </div>
    <div class="ticket-summary-line" style="font-weight: bold;">
        <span>TOTAL:</span>
        <span>$<?= number_format($pedido['total'], 2) ?></span>
    </div>
</div>
<hr>
<?php endif; ?>

<!-- Detalles de los artículos -->
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
            <span>$<?= number_format($item['precio_unitario'], 2) ?></span>
            <span>$<?= number_format($item['subtotal'], 2) ?></span>
        </div>
    <?php endforeach; ?>
</div>
<hr>

<!-- Totales finales -->
<div class="ticket-total">
    <div class="ticket-total-line">
        <span>TOTAL:</span>
        <span>$<?= number_format($pedido['total'], 2) ?></span>
    </div>
    <div class="ticket-total-line">
        <span>ANTICIPO:</span>
        <span>$<?= number_format($pedido['anticipo'], 2) ?></span>
    </div>
    <div class="ticket-total-line" style="font-weight: bold;">
        <span>SALDO PENDIENTE:</span>
        <span>$<?= number_format($pedido['total'] - $pedido['anticipo'], 2) ?></span>
    </div>
</div>
<hr>

<div class="ticket-footer">
    ¡Gracias por su compra!
</div>

<style>
    .ticket-header, .ticket-footer {
        text-align: center;
        margin-bottom: 10px;
    }
    .ticket-item-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 3px;
    }
    .ticket-item-line span:nth-child(1) { width: 15%; }
    .ticket-item-line span:nth-child(2) { width: 45%; }
    .ticket-item-line span:nth-child(3), 
    .ticket-item-line span:nth-child(4) { width: 20%; text-align: right; }
    .ticket-summary-line, .ticket-total-line {
        display: flex;
        justify-content: space-between;
        margin-bottom: 3px;
    }
    .ticket-summary-line span:nth-child(2),
    .ticket-total-line span:nth-child(2) {
        text-align: right;
    }
    hr {
        margin: 5px 0;
        border: 0;
        border-top: 1px dashed #ccc;
    }
</style>