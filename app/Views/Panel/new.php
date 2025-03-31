<?= $this->extend('Panel/panel_template') ?> <!-- Asume que tienes un layout base -->

<?= $this->section('contenido') ?>
<h1><?= esc($title) ?></h1>

<!-- Mostrar errores de validación -->
<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Mostrar mensajes de éxito/error generales -->
<?php if (session()->has('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>
<?php if (session()->has('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>


<form action="<?= site_url('pedidos/create') ?>" method="post" id="pos-form">
    <?= csrf_field() ?>

    <div class="row">
        <!-- Columna Izquierda: Formulario -->
        <div class="col-md-7">
            <h2>Datos del Pedido</h2>

            <div class="mb-3">
                <label for="cliente_nombre" class="form-label">Nombre del Cliente:</label>
                <input type="text" class="form-control" id="cliente_nombre" name="cliente_nombre" value="<?= old('cliente_nombre') ?>" required>
            </div>

            <div class="mb-3">
                <label for="cliente_telefono" class="form-label">Teléfono del Cliente (Opcional):</label>
                <input type="tel" class="form-control" id="cliente_telefono" name="cliente_telefono" value="<?= old('cliente_telefono') ?>">
            </div>

            <hr>

            <h3>Añadir Productos/Servicios</h3>
            <div class="row g-3 align-items-end mb-3" id="add-item-section">
                <div class="col-md-5">
                    <label for="item_descripcion" class="form-label">Descripción:</label>
                    <input type="text" class="form-control" id="item_descripcion">
                </div>
                <div class="col-md-2">
                    <label for="item_cantidad" class="form-label">Cantidad:</label>
                    <input type="number" class="form-control" id="item_cantidad" value="1" min="1">
                </div>
                <div class="col-md-3">
                    <label for="item_precio" class="form-label">Precio Unit.:</label>
                    <input type="number" step="0.01" class="form-control" id="item_precio" min="0">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary w-100" id="btn-add-item">Añadir</button>
                </div>
            </div>

            <hr>

            <h3>Items del Pedido</h3>
            <table class="table" id="items-table">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="items-list">
                    <!-- Las filas de items se añadirán aquí con JS -->
                     <?php if (old('detalle')): // Repoblar si hay error de validación ?>
                        <?php foreach (old('detalle') as $key => $item): ?>
                            <tr class="item-row">
                                <td>
                                    <?= esc($item['descripcion']) ?>
                                    <input type="hidden" name="detalle[<?= $key ?>][descripcion]" value="<?= esc($item['descripcion']) ?>">
                                </td>
                                <td class="cantidad">
                                    <?= esc($item['cantidad']) ?>
                                    <input type="hidden" name="detalle[<?= $key ?>][cantidad]" value="<?= esc($item['cantidad']) ?>">
                                </td>
                                <td class="precio-unitario">
                                    <?= number_format($item['precio_unitario'], 2) ?>
                                    <input type="hidden" name="detalle[<?= $key ?>][precio_unitario]" value="<?= esc($item['precio_unitario']) ?>">
                                </td>
                                <td class="subtotal">
                                    <?= number_format($item['cantidad'] * $item['precio_unitario'], 2) ?>
                                 </td>
                                <td><button type="button" class="btn btn-danger btn-sm btn-remove-item">Quitar</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                        <td id="total-display"><strong>0.00</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            <input type="hidden" name="total_final_hidden" id="total_final_hidden" value="0">

             <button type="submit" class="btn btn-success btn-lg" id="btn-finalizar">Finalizar Venta</button>

        </div>

        <!-- Columna Derecha: Previsualización Ticket -->
        <div class="col-md-5">
            <h2>Ticket (Previsualización)</h2>
            <div id="ticket-preview" style="border: 1px dashed #ccc; padding: 15px; font-family: 'Courier New', Courier, monospace; background-color: #f9f9f9;">
                <div style="text-align: center; margin-bottom: 10px;">
                    <strong>[NOMBRE DE TU NEGOCIO]</strong><br>
                    [Tu Dirección]<br>
                    [Tu Teléfono/RIF]<br>
                    <hr style="border-top: 1px dashed #ccc;">
                </div>
                <div>Fecha: <span id="ticket-fecha"><?= date('d/m/Y H:i') ?></span></div>
                <div>Pedido #: <span id="ticket-pedido-num">Nuevo</span></div>
                <div>Cliente: <span id="ticket-cliente-nombre"></span></div>
                <div>Teléfono: <span id="ticket-cliente-tel"></span></div>
                <hr style="border-top: 1px dashed #ccc;">
                <div style="margin-bottom: 5px;">
                    <span>Cant.</span> | <span>Descripción</span> | <span>P.U.</span> | <span>Subt.</span>
                </div>
                 <hr style="border-top: 1px dashed #ccc;">
                <div id="ticket-items-list" style="min-height: 50px;">
                    <!-- Items del ticket se añadirán aquí -->
                </div>
                 <hr style="border-top: 1px dashed #ccc;">
                <div style="text-align: right; font-weight: bold;">
                    TOTAL: <span id="ticket-total">0.00</span>
                </div>
                 <hr style="border-top: 1px dashed #ccc;">
                 <div style="text-align: center; margin-top: 10px; font-size: 0.9em;">
                     ¡Gracias por su compra!
                 </div>
            </div>
             <!-- El botón de descarga aparecerá en la vista del ticket generado -->
        </div>
    </div>
</form>


<!-- Incluye jQuery si no lo tienes globalmente -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

<script>
$(document).ready(function() {
    let itemIndex = <?= old('detalle') ? count(old('detalle')) : 0 ?>; // Para nombres de campo únicos

    // Actualizar previsualización del ticket al cargar (si hay datos 'old')
    updateTicketPreview();

    // Añadir Item
    $('#btn-add-item').on('click', function() {
        const descripcion = $('#item_descripcion').val().trim();
        const cantidad = parseInt($('#item_cantidad').val());
        const precio = parseFloat($('#item_precio').val());

        if (!descripcion || isNaN(cantidad) || cantidad <= 0 || isNaN(precio) || precio < 0) {
            alert('Por favor, complete la descripción, cantidad válida y precio válido.');
            return;
        }

        const subtotal = (cantidad * precio).toFixed(2);
        const precioFormatted = precio.toFixed(2);

        const newRow = `
            <tr class="item-row">
                <td>
                    ${descripcion}
                    <input type="hidden" name="detalle[${itemIndex}][descripcion]" value="${descripcion}">
                </td>
                <td class="cantidad">
                    ${cantidad}
                     <input type="hidden" name="detalle[${itemIndex}][cantidad]" value="${cantidad}">
                </td>
                <td class="precio-unitario">
                    ${precioFormatted}
                    <input type="hidden" name="detalle[${itemIndex}][precio_unitario]" value="${precio}">
                 </td>
                <td class="subtotal">${subtotal}</td>
                <td><button type="button" class="btn btn-danger btn-sm btn-remove-item">Quitar</button></td>
            </tr>
        `;

        $('#items-list').append(newRow);
        itemIndex++;

        // Limpiar campos de añadir item
        $('#item_descripcion').val('');
        $('#item_cantidad').val('1');
        $('#item_precio').val('');
        $('#item_descripcion').focus(); // Poner foco de nuevo en descripción

        updateTotal();
        updateTicketPreview();
    });

    // Quitar Item
    $('#items-table').on('click', '.btn-remove-item', function() {
        $(this).closest('tr').remove();
        updateTotal();
        updateTicketPreview();
        // Nota: No reindexamos aquí para simplificar, el backend manejará los índices que lleguen.
    });

    // Actualizar Total
    function updateTotal() {
        let total = 0;
        $('#items-list tr').each(function() {
            const subtotalText = $(this).find('.subtotal').text().replace(',', ''); // Quita comas si las hubiera
            const subtotal = parseFloat(subtotalText);
            if (!isNaN(subtotal)) {
                total += subtotal;
            }
        });
        $('#total-display').html(`<strong>${total.toFixed(2)}</strong>`);
        $('#total_final_hidden').val(total.toFixed(2)); // Campo oculto por si acaso
    }

     // --- Funciones para actualizar la previsualización del Ticket ---
    function updateTicketPreview() {
        $('#ticket-cliente-nombre').text($('#cliente_nombre').val() || '[Cliente no ingresado]');
        $('#ticket-cliente-tel').text($('#cliente_telefono').val() || '');

        const itemsHtml = [];
        $('#items-list tr').each(function() {
            const desc = $(this).find('td:nth-child(1)').text().trim();
            const cant = $(this).find('.cantidad').text().trim();
            const pu = $(this).find('.precio-unitario').text().trim();
            const subt = $(this).find('.subtotal').text().trim();

             // Estilo simple para ticket de texto
             const itemLine = `
                <div style="display: flex; justify-content: space-between; font-size: 0.9em; margin-bottom: 2px; word-break: break-word;">
                     <span style="width: 15%; text-align: right;">${cant}</span>
                     <span style="width: 45%; padding-left: 5px;">${desc}</span>
                     <span style="width: 20%; text-align: right;">${pu}</span>
                     <span style="width: 20%; text-align: right;">${subt}</span>
                 </div>`;
            itemsHtml.push(itemLine);
        });
        $('#ticket-items-list').html(itemsHtml.join(''));

        $('#ticket-total').text($('#total-display').text());
        $('#ticket-fecha').text(new Date().toLocaleString('es-VE')); // O tu formato local
    }

    // Actualizar previsualización cuando cambian los datos del cliente
    $('#cliente_nombre, #cliente_telefono').on('input', updateTicketPreview);

    // Calcular total inicial si hay items 'old'
    updateTotal();
});
</script>
<?= $this->endSection() ?>