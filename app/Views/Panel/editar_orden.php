<?= $this->extend('Panel/panel_template') ?>

<?= $this->section('contenido') ?>

<h1><?= esc($title) ?></h1>

<!-- Mostrar mensajes de éxito/error -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        Editar Orden de Trabajo #<?= $orden->id_ot ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('ordenes/update/' . $orden->id_ot) ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Orden #:</label>
                        <input type="text" class="form-control" value="<?= $orden->id_ot ?>" readonly disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Fecha Creación:</label>
                        <input type="text" class="form-control" value="<?= date('d/m/Y H:i', strtotime($orden->created_at)) ?>" readonly disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Pedido Original #:</label>
                        <input type="text" class="form-control" value="<?= $pedido['id'] ?>" readonly disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Fecha Pedido:</label>
                        <input type="text" class="form-control" value="<?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?>" readonly disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Cliente:</label>
                        <input type="text" class="form-control" value="<?= esc($pedido['cliente_nombre']) ?>" readonly disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Teléfono Cliente:</label>
                        <input type="text" class="form-control" value="<?= esc($pedido['cliente_telefono']) ?>" readonly disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Anticipo:</label>
                        <input type="text" class="form-control" value="$<?= number_format($pedido['anticipo'], 2) ?>" readonly disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Total:</label>
                        <input type="text" class="form-control" value="$<?= number_format($pedido['total'], 2) ?>" readonly disabled>
                    </div>
                </div>
            </div>

            <hr>

            <div class="mb-3">
                <label for="observaciones" class="form-label">Observaciones / Detalles del Trabajo:</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="4"><?= old('observaciones', $orden->observaciones) ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="color_tinta" class="form-label">Color de Tinta/Material:</label>
                        <select class="form-select" id="color_tinta" name="color_tinta">
                            <option value="">-- Selecciona (Opcional) --</option>
                            <?php foreach ($colores_tinta as $color): ?>
                                <option value="<?= esc($color) ?>" <?= (old('color_tinta', $orden->color_tinta) == $color) ? 'selected' : '' ?>>
                                    <?= esc($color) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Estado:</label>
                        <select class="form-select" id="status" name="status" required>
                            <?php
                            $estadosPosibles = ['Dibujo', 'Elaboracion', 'Facturacion', 'Entregado'];
                            $statusSeleccionado = old('status', $orden->status);
                            ?>
                            <?php foreach ($estadosPosibles as $estado): ?>
                                <option value="<?= esc($estado) ?>" <?= $statusSeleccionado == $estado ? 'selected' : '' ?>>
                                    <?= esc($estado) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="imagen_orden" class="form-label">Cambiar Imagen (Opcional, max 2MB):</label>
                <input class="form-control" type="file" id="imagen_orden" name="imagen_orden" accept="image/png, image/jpeg, image/gif, image/webp">
                <?php if ($orden->imagen_path): ?>
                    <div class="mt-2">
                        <p>Imagen actual:</p>
                        <img src="<?= site_url('writable/uploads/ordenes/' . $orden->imagen_path) ?>" class="img-fluid" style="max-height: 150px;">
                        <p class="small text-muted"><?= $orden->imagen_path ?></p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="eliminar_imagen" name="eliminar_imagen" value="1">
                            <label class="form-check-label" for="eliminar_imagen">
                                Eliminar imagen actual
                            </label>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mt-2">
                        <p class="text-muted">No hay imagen actual</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-flex justify-content-end">
                <a href="<?= site_url('administracion') ?>" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-success">Actualizar Orden</button>
            </div>

        </form>
    </div>
</div>

<?= $this->endSection() ?>