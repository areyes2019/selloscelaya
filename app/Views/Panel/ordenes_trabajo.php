<?= $this->extend('Panel/panel_template') ?>
<?= $this->section('contenido') ?>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<h1>Ordenes de Trabajo</h1>
<a href="<?= site_url('ordenes/descargar_ordenes') ?>" target="_blank" class="btn btn-danger rounded-0 mb-3">
    <i class="fas fa-file-pdf"></i> Descargar Órdenes (PDF)
</a>

<a href="<?= site_url('ordenes/pedidos-pendientes') ?>" class="btn btn-warning rounded-0 mb-3" target="_blank">
    <i class="fas fa-tags"></i> Generar Etiquetas Pendientes (PDF)
</a>

<!-- Pestañas -->
<ul class="nav nav-tabs" id="ordenesTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="dibujo-tab" data-bs-toggle="tab" data-bs-target="#dibujo" type="button" role="tab">
            Dibujo <span class="badge bg-primary ms-1"><?= count(array_filter($lista, fn($o) => strtolower($o->status) === 'dibujo')) ?></span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="elaboracion-tab" data-bs-toggle="tab" data-bs-target="#elaboracion" type="button" role="tab">
            Elaboración <span class="badge bg-warning ms-1"><?= count(array_filter($lista, fn($o) => strtolower($o->status) === 'elaboracion')) ?></span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="entrega-tab" data-bs-toggle="tab" data-bs-target="#entrega" type="button" role="tab">
            Entrega <span class="badge bg-success ms-1"><?= count(array_filter($lista, fn($o) => strtolower($o->status) === 'entrega' || strtolower($o->status) === 'entregado')) ?></span>
        </button>
    </li>
</ul>

<!-- Contenido de las pestañas -->
<div class="tab-content card rounded-0 shadow-sm border-top-0" id="ordenesTabContent">
    <!-- Pestaña Dibujo -->
    <div class="tab-pane fade show active" id="dibujo" role="tabpanel">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Img</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_filter($lista, fn($o) => strtolower($o->status) === 'dibujo') as $orden): ?>
                    <tr>
                        <td><?= esc($orden->cliente_nombre) ?></td>
                        <td><?= esc($orden->cliente_telefono) ?></td>
                        <td>
                            <?php $rutaImagen = 'writable/uploads/ordenes/' . $orden->imagen_path; ?>
                            <?php if (!empty($orden->imagen_path) && file_exists($rutaImagen)): ?>
                                <img src="<?= base_url($rutaImagen) ?>" class="img-thumbnail" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                            <?php else: ?>
                                <span class="badge bg-secondary">Sin imagen</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="<?= base_url('ordenes/actualizar-status/' . $orden->id_ot) ?>" method="post">
                                <?= csrf_field() ?>
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto; display: inline-block;">
                                    <option value="Dibujo" selected>Dibujo</option>
                                    <option value="Elaboracion">Elaboración</option>
                                    <option value="Entrega">Entrega</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pestaña Elaboración -->
    <div class="tab-pane fade" id="elaboracion" role="tabpanel">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Img</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_filter($lista, fn($o) => strtolower($o->status) === 'elaboracion') as $orden): ?>
                    <tr>
                        <td><?= esc($orden->cliente_nombre) ?></td>
                        <td><?= esc($orden->cliente_telefono) ?></td>
                        <td>
                            <?php $rutaImagen = 'writable/uploads/ordenes/' . $orden->imagen_path; ?>
                            <?php if (!empty($orden->imagen_path) && file_exists($rutaImagen)): ?>
                                <img src="<?= base_url($rutaImagen) ?>" class="img-thumbnail" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                            <?php else: ?>
                                <span class="badge bg-secondary">Sin imagen</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="<?= base_url('ordenes/actualizar-status/' . $orden->id_ot) ?>" method="post">
                                <?= csrf_field() ?>
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto; display: inline-block;">
                                    <option value="Dibujo">Dibujo</option>
                                    <option value="Elaboracion" selected>Elaboración</option>
                                    <option value="Entrega">Entrega</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pestaña Entrega -->
    <div class="tab-pane fade" id="entrega" role="tabpanel">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Img</th>
                    <th>Status</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_filter($lista, fn($o) => strtolower($o->status) === 'entrega' || strtolower($o->status) === 'entregado') as $orden): ?>
                    <tr>
                        <td><?= esc($orden->cliente_nombre) ?></td>
                        <td><?= esc($orden->cliente_telefono) ?></td>
                        <td>
                            <?php $rutaImagen = 'writable/uploads/ordenes/' . $orden->imagen_path; ?>
                            <?php if (!empty($orden->imagen_path) && file_exists($rutaImagen)): ?>
                                <img src="<?= base_url($rutaImagen) ?>" class="img-thumbnail" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                            <?php else: ?>
                                <span class="badge bg-secondary">Sin imagen</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= strtolower($orden->status) === 'entregado' ? 'success' : 'info' ?>">
                                <?= ucfirst(esc($orden->status)) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (strtolower($orden->status) === 'entrega'): ?>
                                <form action="<?= base_url('ordenes/actualizar-status/' . $orden->id_ot) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto; display: inline-block;">
                                        <option value="Elaboracion">Elaboración</option>
                                        <option value="Entrega" selected>Entrega</option>
                                        <option value="Entregado">Marcar como Entregado</option>
                                    </select>
                                </form>
                            <?php else: ?>
                                <a href="<?= base_url('ordenes/eliminar/'.$orden->id_ot) ?>" 
                                   class="btn btn-danger btn-sm rounded-0"
                                   onclick="return confirm('¿Estás seguro de eliminar esta orden?')">
                                   <span class="bi bi-trash3"></span> Eliminar
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Inicializar DataTables en cada tabla
        $('.tab-pane table').each(function() {
            $(this).DataTable();
        });
        
        // Actualizar badges cuando cambia el estado
        $('select[name="status"]').on('change', function() {
            setTimeout(() => {
                location.reload(); // Recargar para actualizar las pestañas
            }, 500);
        });
    });
</script>

<?= $this->endSection() ?>