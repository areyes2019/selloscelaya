<?= $this->extend('Panel/panel_template') ?>

<?= $this->section('titulo') ?>
    <?= $titulo_pagina ?? 'Formulario de Inventario' ?>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
             <h6 class="m-0 font-weight-bold text-primary"><?= esc($titulo_pagina) ?></h6>
        </div>
        <div class="card-body">

            <!-- Mostrar errores de validación -->
            <?php if (isset($validation)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $validation->listErrors() ?>
                </div>
            <?php endif; ?>

            <?php
            // Determinar la URL de acción del formulario y si estamos editando
            $is_editing = isset($inventario); // Si la variable $inventario existe, estamos editando
            $form_action = $is_editing ? site_url('/existencias/actualizar/' . $inventario['id_inventario']) : site_url('/existencias/crear');
            ?>

            <?= form_open($form_action) ?>
                <?= csrf_field() ?>

                <!-- Campo Artículo -->
                <div class="form-group">
                    <label for="id_articulo">Artículo</label>
                    <?php if ($is_editing): ?>
                        <!-- Modo Edición: Mostrar nombre del artículo (no editable) -->
                        <input type="text" class="form-control" value="<?= esc($articulo['nombre'] ?? 'Artículo no encontrado') ?>" disabled>
                        <!-- Podrías incluir un campo oculto si necesitas el id_articulo en el post, aunque no es necesario para actualizar la cantidad -->
                        <!-- <input type="hidden" name="id_articulo" value="<?= esc($inventario['id_articulo']) ?>"> -->
                    <?php else: ?>
                        <!-- Modo Creación: Dropdown para seleccionar artículo -->
                        <select class="form-control <?= (isset($validation) && $validation->hasError('id_articulo')) ? 'is-invalid' : '' ?>" id="id_articulo" name="id_articulo" required>
                            <option value="">-- Selecciona un Artículo --</option>
                            <?php if (!empty($articulos)): ?>
                                <?php foreach ($articulos as $articulo): ?>
                                    <option value="<?= esc($articulo['id_articulo']) ?>" <?= set_select('id_articulo', $articulo['id_articulo']) ?>>
                                        <?= esc($articulo['nombre']) ?> (MOD: <?= esc($articulo['modelo']) ?>)
                                    </option>
                                <?php endforeach; ?>
                             <?php else: ?>
                                 <option value="" disabled>No hay artículos disponibles para añadir</option>
                            <?php endif; ?>
                        </select>
                         <?php if (isset($validation) && $validation->hasError('id_articulo')): ?>
                            <div class="invalid-feedback">
                                <?= $validation->getError('id_articulo') ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Campo Cantidad -->
                <div class="form-group">
                    <label for="cantidad">Cantidad en Stock</label>
                    <input type="number"
                           class="form-control <?= (isset($validation) && $validation->hasError('cantidad')) ? 'is-invalid' : '' ?>"
                           id="cantidad"
                           name="cantidad"
                           value="<?= set_value('cantidad', $inventario['cantidad'] ?? '1') ?>"
                           required
                           min="<?= $is_editing ? '0' : '1' ?>"  <?php // Permitir 0 al editar, requerir >0 al crear ?>
                           step="1">
                     <?php if (isset($validation) && $validation->hasError('cantidad')): ?>
                        <div class="invalid-feedback">
                            <?= $validation->getError('cantidad') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Botones -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> <?= $is_editing ? 'Actualizar Cantidad' : 'Añadir al Inventario' ?>
                    </button>
                    <a href="<?= site_url('existencias/existencias_admin') ?>" class="btn btn-secondary">
                       <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                </div>

            <?= form_close() ?>

        </div> <!-- end card-body -->
    </div> <!-- end card -->
</div> <!-- end container-fluid -->
<?= $this->endSection() ?>