<?= $this->extend('Panel/panel_template') ?>

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
                    <input type="text" class="form-control" id="item_descripcion" autocomplete="off">
                    <input type="hidden"  id="item_articulo_id">
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
                    <?php if (old('detalle')): ?>
                        <?php foreach (old('detalle') as $key => $item): ?>
                            <tr class="item-row">
                                <td>
                                    <?= esc($item['descripcion']) ?>
                                    <input type="hidden" name="detalle[<?= $key ?>][descripcion]" value="<?= esc($item['descripcion']) ?>">
                                    <input type="hidden" name="detalle[<?= $key ?>][id_articulo]" value="<?= esc($item['id_articulo'] ?? '') ?>">
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

            <div class="row mt-3">
                <div class="col-md-6 offset-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Resumen de Pago</h5>
                            
                            <div class="row mb-2">
                                <div class="col-6"><strong>Total:</strong></div>
                                <div class="col-6 text-end" id="resumen-total">0.00</div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-6">
                                    <label for="anticipo" class="form-label"><strong>Anticipo:</strong></label>
                                </div>
                                <div class="col-6">
                                    <input type="number" step="0.01" min="0" class="form-control" id="anticipo" name="anticipo" value="<?= old('anticipo', 0) ?>">
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-6"><strong>Saldo:</strong></div>
                                <div class="col-6 text-end" id="resumen-saldo">0.00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        </div>
    </div>
</form>

<script>
    // Primero define la variable con PHP
    const initialItemIndex = <?= old('detalle') ? count(old('detalle')) : 0 ?>;
    $(document).ready(function() {
        const articulos = [
            <?php foreach ($articulos as $articulo): ?>
            {
                id: <?= $articulo['id_articulo'] ?>,
                nombre: '<?= addslashes($articulo['modelo']) ?>',
                precio: <?= $articulo['precio_pub'] ?>
            },
            <?php endforeach; ?>
        ];
        
        $('#item_descripcion').typeahead({
            source: articulos,
            displayText: function(item) {
                return item.nombre + ' - $' + item.precio.toFixed(2);
            },
            afterSelect: function(item) {
                $('#item_articulo_id').val(item.id);
                $('#item_precio').val(item.precio).trigger('change');
            }
        });
    });
</script>
<script type="" src="<?php echo base_url('public/js/ticket.js'); ?>"></script>

<?= $this->endSection() ?>