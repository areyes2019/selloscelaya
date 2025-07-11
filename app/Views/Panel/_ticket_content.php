<?php helper(['number', 'text']); ?>

<style>
    /* Estilos específicos para el ticket */
    .ticket-header, .ticket-footer {
        text-align: center;
    }
    
    .ticket-item-line {
        display: grid;
        grid-template-columns: 15% 45% 20% 20%;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }
    
    .ticket-total-line {
        display: grid;
        grid-template-columns: 70% 30%;
        margin-bottom: 3px;
    }
    
    .ticket-total-line span:last-child,
    .ticket-item-line span:last-child {
        text-align: right;
    }
    
    .ticket-items-list {
        margin-bottom: 10px;
    }
    
    .ticket-footer {
        margin-top: 15px;
        font-weight: bold;
    }
    
    /* Mejoras para impresión */
    @media print {
        .ticket-container {
            width: 80mm !important;
            max-width: 80mm !important;
            font-size: 12px !important;
        }
        
        .ticket-item-line {
            font-size: 0.8rem !important;
        }
    }
</style>

<div class="ticket-header">
    <!-- Logo centrado con mejor manejo de tamaños -->
    <div class="text-center mb-2">
        <img src="<?= base_url('public/img/logo2.png') ?>" 
             alt="Logo Sello Pronto" 
             style="height: auto; max-width: 200px; width: 100%;">
    </div>
    
    <div class="text-center mb-2">
        <strong>Sello Pronto</strong><br>
        Real del Seminario #122, Valle del Real<br>
        Celaya, Gto. • 461 358 1090<br>
    </div>
    <hr style="border-top: 1px dashed #ccc;">
</div>

<!-- Información del pedido -->
<div class="ticket-info">
    <div><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?></div>
    <div><strong>Pedido #:</strong> <?= esc($pedido['id']) ?></div>
    <div><strong>Cliente:</strong> <?= esc(character_limiter($pedido['cliente_nombre'], 30)) ?></div>
    <?php if (!empty($pedido['cliente_telefono'])): ?>
    <div><strong>Teléfono:</strong> <?= esc($pedido['cliente_telefono']) ?></div>
    <?php endif; ?>
    <hr style="border-top: 1px dashed #ccc;">
</div>

<!-- Encabezado de items -->
<div class="ticket-items-header">
    <div class="ticket-item-line font-weight-bold">
        <span>Cant.</span>
        <span>Descripción</span>
        <span>P.U.</span>
        <span>Subt.</span>
    </div>
    <hr style="margin-top: 3px; margin-bottom: 5px;">
</div>

<!-- Lista de items -->
<div class="ticket-items-list">
    <?php foreach ($detalles as $item): ?>
        <div class="ticket-item-line">
            <span><?= esc($item['cantidad']) ?></span>
            <span><?= esc(character_limiter($item['descripcion'], 30)) ?></span>
            <span><?= number_format($item['precio_unitario'], 2) ?></span>
            <span><?= number_format($item['subtotal'], 2) ?></span>
        </div>
    <?php endforeach; ?>
</div>

<!-- Totales -->
<hr style="border-top: 1px dashed #ccc;">
<div class="ticket-total">
    <div class="ticket-total-line">
        <span>TOTAL:</span>
        <span><?= number_format($pedido['total'], 2) ?></span>
    </div>
    
    <?php if ($pedido['descuento'] > 0): ?>
    <div class="ticket-total-line">
        <span>DESCUENTO (<?= $pedido['descuento'] ?>%):</span>
        <span>- <?= number_format(($pedido['total'] * $pedido['descuento'] / 100), 2) ?></span>
    </div>
    <?php endif; ?>
    
    <div class="ticket-total-line">
        <span>ANTICIPO:</span>
        <span><?= number_format($pedido['anticipo'], 2) ?></span>
    </div>
    
    <div class="ticket-total-line font-weight-bold">
        <span>SALDO:</span>
        <span><?= number_format($pedido['total'] - $pedido['anticipo'], 2) ?></span>
    </div>
</div>

<!-- Pie de ticket -->
<hr style="border-top: 1px dashed #ccc;">
<div class="ticket-footer">
    <div class="mb-2">¡Gracias por su compra!</div>
    <div style="font-size: 0.8rem;">
        <?php if (!empty($pedido['observaciones'])): ?>
        <div><strong>Notas:</strong> <?= esc($pedido['observaciones']) ?></div>
        <?php endif; ?>
        <div>Conserve este ticket para cualquier aclaración</div>
    </div>
</div>