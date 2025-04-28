<?= $this->extend('Panel/panel_template') ?>

<?= $this->section('contenido') ?>

<h1><?= esc($title) ?></h1>
<a href="<?= site_url('ordenes/descargar_ordenes') ?>" class="btn btn-danger rounded-0 mb-3">
    <i class="fas fa-file-pdf"></i> <!-- Icono opcional (ej. Font Awesome) -->
    Descargar Órdenes (PDF)
</a>
<!-- Mostrar mensajes generales -->
<?php if (session()->has('error')): ?>
    <div class="alert alert-danger"><?= session('error') ?></div>
<?php endif; ?>
<?php if (session()->has('success')): ?>
    <div class="alert alert-success"><?= session('success') ?></div>
<?php endif; ?>
<?php if (session()->has('info')): ?>
    <div class="alert alert-info"><?= session('info') ?></div>
<?php endif; ?>


<!-- Pestañas (Tabs) -->
<ul class="nav nav-tabs" id="ordenesTab" role="tablist">
    <?php $first = true; ?>
    <?php foreach ($statuses as $status): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $first ? 'active' : '' ?>" id="<?= strtolower($status) ?>-tab" data-bs-toggle="tab" data-bs-target="#<?= strtolower($status) ?>-tab-pane" type="button" role="tab" aria-controls="<?= strtolower($status) ?>-tab-pane" aria-selected="<?= $first ? 'true' : 'false' ?>">
                <?= esc($status) ?>
                <span class="badge bg-secondary"><?= count($ordenesPorStatus[$status] ?? []) ?></span>
            </button>
        </li>
        <?php $first = false; ?>
    <?php endforeach; ?>
</ul>

<!-- Contenido de las Pestañas -->
<div class="tab-content" id="ordenesTabContent">
     <?php $first = true; ?>
    <?php foreach ($statuses as $status): ?>
        <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" id="<?= strtolower($status) ?>-tab-pane" role="tabpanel" aria-labelledby="<?= strtolower($status) ?>-tab" tabindex="0">

            <div class="table-responsive mt-3">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th># Orden</th>
                            <th># Pedido</th>
                            <th>Cliente</th>
                            <th>Fecha Creación</th>
                            <th>Color Tinta</th>
                            <th>Imagen</th>
                            <th>Observaciones</th>
                            <th>Acciones / Cambiar Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ordenesPorStatus[$status])): ?>
                            <?php foreach ($ordenesPorStatus[$status] as $orden): ?>
                                <tr>
                                    <td><?= esc($orden->id_ot) ?></td>
                                    <td>
                                        <a href="<?= site_url('pedidos/ticket/' . $orden->pedido_id) ?>" title="Ver Ticket Original">
                                            <?= esc($orden->pedido_id) ?>
                                        </a>
                                    </td>
                                    <td><?= esc($orden->cliente_nombre) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($orden->created_at)) ?></td>
                                    <td><?= esc($orden->color_tinta ?: '-') ?></td>
                                    <td>
                                        <?php if ($orden->imagen_path): ?>
                                            <a href="<?= site_url(route_to('orden_imagen', $orden->imagen_path)) ?>" target="_blank" title="Ver Imagen">
                                                <i class="bi bi-image"></i> Ver
                                             </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td title="<?= esc($orden->observaciones) ?>">
                                        <?= character_limiter(esc($orden->observaciones ?: '-'), 50) // Muestra solo una parte ?>
                                    </td>
                                    <td>
                                        <!-- Formulario para cambiar status -->
                                        <form action="<?= site_url('ordenes/cambiar_status/' . $orden->id_ot) ?>" method="post" class="d-inline-flex align-items-center">
                                            <?= csrf_field() ?>
                                            <select name="nuevo_status" class="form-select form-select-sm me-1" style="min-width: 120px;" onchange="this.form.submit()">
                                                <?php foreach ($statuses as $stat): ?>
                                                     <option value="<?= esc($stat) ?>" <?= ($stat == $orden->status) ? 'selected' : '' ?>>
                                                         <?= esc($stat) ?>
                                                     </option>
                                                <?php endforeach; ?>
                                            </select>
                                             <button type="submit" class="btn btn-sm btn-outline-primary" title="Guardar Status">
                                                 <i class="bi bi-check-lg"></i>
                                             </button>
                                             <!-- Opcional: Botón Editar/Ver Detalles -->
                                             <!-- <a href="<?= site_url('ordenes/edit/' . $orden->id_ot) ?>" class="btn btn-sm btn-outline-secondary ms-1" title="Editar Orden"><i class="bi bi-pencil"></i></a> -->
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No hay órdenes en estado "<?= esc($status) ?>".</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
         <?php $first = false; ?>
    <?php endforeach; ?>
</div>

<script>
    // Puedes añadir JS específico para el dashboard aquí si es necesario
    // Por ejemplo, para confirmaciones antes de cambiar status, etc.
</script>
<?= $this->endSection() ?>