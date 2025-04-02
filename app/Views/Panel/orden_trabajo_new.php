<?= $this->extend('Panel/panel_template') ?>

<?= $this->section('contenido') ?>

<h1><?= esc($title) ?></h1>

<!-- ... (mensajes de error/éxito) ... -->

<div class="card">
    <div class="card-header">
        Detalles de la Orden de Trabajo
    </div>
    <div class="card-body">
        <form action="<?= site_url('ordenes/create') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <!-- Campo oculto: Usa la clave correcta 'id_ot' -->
            <input type="hidden" name="pedido_id" value="<?= esc($pedido['id']) ?>">

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Pedido Original #:</label>
                        <!-- Usa la clave correcta 'id_ot' -->
                        <input type="text" class="form-control" value="<?= esc($pedido['id']) ?>" readonly disabled>
                    </div>
                </div>
                 <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Fecha Pedido:</label>
                        <!-- Usa la clave correcta 'created_at' -->
                        <input type="text" class="form-control" value="<?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?>" readonly disabled>
                    </div>
                </div>
            </div>

             <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Cliente:</label>
                         <!-- Usa la clave correcta 'cliente_nombre' -->
                        <input type="text" class="form-control" value="<?= esc($pedido['cliente_nombre']) ?>" readonly disabled>
                    </div>
                </div>
                 <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Teléfono Cliente:</label>
                        <!-- Usa la clave correcta 'cliente_telefono' -->
                        <input type="text" class="form-control" value="<?= esc($pedido['cliente_telefono']) ?>" readonly disabled>
                    </div>
                </div>
            </div>

            <hr>

            <div class="mb-3">
                <label for="observaciones" class="form-label">Observaciones / Detalles del Trabajo:</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="4"><?= old('observaciones') ?></textarea>
            </div>

            <!-- ... (resto del formulario: color_tinta, status_inicial, imagen_orden) ... -->
             <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="color_tinta" class="form-label">Color de Tinta/Material:</label>
                        <select class="form-select" id="color_tinta" name="color_tinta">
                            <option value="">-- Selecciona (Opcional) --</option>
                            <?php foreach ($colores_tinta as $color): ?>
                                <option value="<?= esc($color) ?>" <?= old('color_tinta') == $color ? 'selected' : '' ?>>
                                    <?= esc($color) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                 <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status_inicial" class="form-label">Estado Inicial:</label>
                        <select class="form-select" id="status_inicial" name="status_inicial" required>
                            <?php
                            $estadosPosibles = ['Diseño', 'Elaboracion', 'Entrega'];
                            $statusSeleccionado = old('status_inicial', 'Diseño'); // Default a Diseño
                            ?>
                            <?php foreach ($estadosPosibles as $estado): ?>
                                <option value="<?= esc($estado) ?>" <?= $statusSeleccionado == $estado ? 'selected' : '' ?>>
                                    <?= esc($estado) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                         <div class="form-text">Define en qué etapa inicia esta orden.</div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="imagen_orden" class="form-label">Adjuntar Imagen (Opcional, max 2MB):</label>
                <input class="form-control" type="file" id="imagen_orden" name="imagen_orden" accept="image/png, image/jpeg, image/gif, image/webp">
                 <?php if(old('imagen_path')): // Mostrar si hubo error y ya había una imagen ?>
                    <div class="mt-2">Imagen previamente subida: <?= esc(old('imagen_path')) ?> (Se deberá volver a subir si hay errores)</div>
                 <?php endif; ?>
            </div>


            <div class="d-flex justify-content-end">
                 <!-- Botón Cancelar: Usa la clave correcta 'id_ot' -->
                 <a href="<?= site_url('pedidos/ticket/' . $pedido['id']) ?>" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-success">Guardar Orden de Trabajo</button>
            </div>

        </form>
    </div>
</div>


<?= $this->endSection() ?>