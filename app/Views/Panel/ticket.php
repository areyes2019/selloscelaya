<?= $this->extend('Panel/panel_template') ?>

<?= $this->section('contenido') ?>
<div class="container-fluid py-3">
    <div class="row flex-column flex-lg-row"> <!-- Flex column en móvil, row en desktop -->
        <h1 class="h3 mb-4"><?= esc($title) ?></h1>
        <!-- Contenido principal del ticket -->
        <div class="col-lg-4 order-2 order-lg-1"> <!-- Orden 2 en móvil, 1 en desktop -->

            <?php if (session()->has('success')): ?>
                <div class="alert alert-success"><?= session('success') ?></div>
            <?php endif; ?>

            <div class="ticket-container bg-white p-3 shadow-sm mb-3 mb-lg-0">
                <?= view('Panel/_ticket_content', ['pedido' => $pedido, 'detalles' => $detalles]) ?>
            </div>
        </div>

        <!-- Panel lateral de botones - ahora completamente responsivo -->
        <div class="col-lg-3 order-1 order-lg-2 mb-3 mb-lg-0"> <!-- Orden 1 en móvil, 2 en desktop -->
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light py-2">
                    <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <span>Acciones</span>
                        <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#actionsPanel">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </h5>
                </div>
                <div class="card-body collapse d-lg-block" id="actionsPanel">
                    <div class="d-grid gap-2">
                        <!-- Botones responsivos -->
                        <a href="<?= site_url('pedidos/download/' . $pedido['id']) ?>" class="btn btn-success">
                            <i class="bi bi-download"></i> <span class="d-none d-md-inline">Descargar</span>
                        </a>
                        <a href="<?= site_url('ventas/new') ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> <span class="d-none d-md-inline">Nuevo Pedido</span>
                        </a>
                        <a href="<?= site_url('ventas/pos') ?>" class="btn btn-secondary">
                            <i class="bi bi-list-ul"></i> <span class="d-none d-md-inline">Historial</span>
                        </a>
                        <button onclick="window.print();" class="btn btn-info">
                            <i class="bi bi-printer"></i> <span class="d-none d-md-inline">Imprimir</span>
                        </button>
                        <button onclick="copyToWhatsApp()" class="btn btn-warning">
                            <i class="bi bi-whatsapp"></i> <span class="d-none d-md-inline">WhatsApp</span>
                        </button>
                        <a href="<?= site_url('ordenes/new/' . $pedido['id']) ?>" class="btn btn-warning">
                            <i class="bi bi-clipboard-plus"></i> <span class="d-none d-md-inline">Crear Orden</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos base para el ticket */
    .ticket-container {
        font-family: 'Courier New', Courier, monospace;
        line-height: 1.4;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        max-width: 100%;
    }
    
    /* Estilos responsivos para el panel lateral */
    @media (max-width: 992px) {
        .card {
            margin-bottom: 1rem;
        }
        
        #actionsPanel.collapse:not(.show) {
            display: none;
        }
        
        #actionsPanel.collapse.show {
            display: block;
        }
    }
    
    @media (min-width: 992px) {
        .card {
            height: auto;
            position: sticky;
            top: 20px;
        }
        
        #actionsPanel {
            display: block !important;
        }
    }
    
    /* Estilos para los botones */
    .btn {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 0.375rem 0.75rem;
    }
    
    /* Estilos para impresión */
    @media print {
        body * {
            visibility: hidden;
        }
        
        .ticket-container, .ticket-container * {
            visibility: visible;
        }
        
        .ticket-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            max-width: 100%;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
        }
        
        @page {
            margin: 5mm;
            size: auto;
        }
        
        body {
            margin: 0;
            font-size: 10pt;
        }
    }
</style>

<script>
// Función mejorada para copiar a WhatsApp
async function copyToWhatsApp() {
    try {
        if (typeof html2canvas !== 'function') {
            const script = document.createElement('script');
            script.src = 'https://html2canvas.hertzen.com/dist/html2canvas.min.js';
            document.head.appendChild(script);
            await new Promise(resolve => script.onload = resolve);
        }
        
        const ticketContainer = document.querySelector('.ticket-container');
        const canvas = await html2canvas(ticketContainer, {
            scale: 2,
            logging: false,
            useCORS: true
        });
        
        canvas.toBlob(async function(blob) {
            try {
                await navigator.clipboard.write([
                    new ClipboardItem({ 'image/png': blob })
                ]);
                alert('¡Ticket copiado! Ahora puedes pegarlo en WhatsApp');
            } catch (err) {
                console.error('Error al copiar:', err);
                alert('Error: ' + err.message);
            }
        }, 'image/png');
    } catch (err) {
        console.error('Error general:', err);
        alert('Error: ' + err.message);
    }
}
</script>

<?= $this->endSection() ?>