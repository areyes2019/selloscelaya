<?= $this->extend('Panel/panel_template') ?> <!-- O un layout más simple si prefieres -->

<?= $this->section('contenido') ?>
<h1><?= esc($title) ?></h1>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>

<div class="ticket-container" style="max-width: 400px; margin: auto; border: 1px solid #ccc; padding: 20px; background: #fff;">
    <!-- Incluimos el contenido real del ticket desde un parcial -->
    <?= view('Panel/_ticket_content', ['pedido' => $pedido, 'detalles' => $detalles]) ?>
</div>

<div class="text-center mt-4 mb-4">
     <a href="<?= site_url('pedidos/download/' . $pedido['id']) ?>" class="btn btn-success"><i class="bi bi-download"></i> Descargar Ticket (HTML)</a>
     <a href="<?= site_url('pedidos/new') ?>" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Nuevo Pedido</a>
     <a href="<?= site_url('pedidos/pos') ?>" class="btn btn-secondary"><i class="bi bi-list-ul"></i> Ver Historial</a>
     <button onclick="window.print();" class="btn btn-info"><i class="bi bi-printer"></i> Imprimir</button>
     <button onclick="copyToWhatsApp()" class="btn btn-warning"><i class="bi bi-whatsapp"></i> Copiar para WhatsApp</button>
     <a href="<?= site_url('ordenes/new/' . $pedido['id']) ?>" class="btn btn-warning"><i class="bi bi-clipboard-plus"></i> Crear Orden de Trabajo</a>
</div>


<style>
    /* Estilos básicos para impresión */
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
            border: none !important; /* Sin borde al imprimir */
            padding: 0 !important;
            margin: 0 !important;
        }
        .btn { /* Ocultar botones al imprimir */
            display: none !important;
        }
         a[href]:after { /* Ocultar URLs en impresión */
            content: none !important;
        }
        /* Ajusta márgenes, fuentes, etc. según tu impresora de tickets */
         @page {
            margin: 5mm; /* O el margen que necesites */
            size: 80mm auto; /* Tamaño típico de rollo de ticket, ajusta */
        }
        body {
             margin: 0;
             font-size: 10pt; /* Tamaño típico para tickets */
        }

    }
     /* Estilos generales para el contenedor del ticket en pantalla */
    .ticket-container {
        font-family: 'Courier New', Courier, monospace;
        line-height: 1.4;
    }
    .ticket-container hr {
        border: none;
        border-top: 1px dashed #555;
        margin: 5px 0;
    }
    .ticket-header {
        text-align: center;
        margin-bottom: 10px;
    }
    .ticket-item-line {
        display: flex;
        justify-content: space-between;
        font-size: 0.9em;
        margin-bottom: 2px;
        word-break: break-word; /* Para descripciones largas */
    }
    .ticket-item-line span:nth-child(1) { width: 15%; text-align: right; padding-right: 5px;} /* Cant */
    .ticket-item-line span:nth-child(2) { width: 45%; } /* Desc */
    .ticket-item-line span:nth-child(3) { width: 20%; text-align: right;} /* PU */
    .ticket-item-line span:nth-child(4) { width: 20%; text-align: right;} /* Subt */

    .ticket-total {
        text-align: right;
        font-weight: bold;
        margin-top: 5px;
    }
    .ticket-footer {
        text-align: center;
        margin-top: 10px;
        font-size: 0.9em;
    }
</style>
<script>
// Función global accesible desde onclick
async function copyToWhatsApp() {
    //alert('Función ejecutándose'); // Primera verificación
    
    try {
        const ticketContainer = document.querySelector('.ticket-container');
        //alert('Elemento encontrado'); // Segunda verificación
        
        // Convertir a imagen
        const canvas = await html2canvas(ticketContainer, {
            scale: 2,
            logging: true // Para ver el proceso en consola
        });
        //alert('Canvas creado'); // Tercera verificación
        
        // Copiar al portapapeles
        canvas.toBlob(async function(blob) {
            try {
                await navigator.clipboard.write([
                    new ClipboardItem({ 'image/png': blob })
                ]);
                alert('¡Ticket copiado! Ahora puedes pegarlo en WhatsApp');
            } catch (err) {
                console.error('Error al copiar:', err);
                alert('Error al copiar: ' + err.message);
            }
        }, 'image/png');
    } catch (err) {
        console.error('Error general:', err);
        alert('Error: ' + err.message);
    }
}
</script>

<?= $this->endSection() ?>