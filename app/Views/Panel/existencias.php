<?= $this->extend('Panel/panel_template') ?>

<?= $this->section('titulo') ?>
    <?= $titulo_pagina ?? 'Inventario' ?>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<div class="container-fluid" id="app">

    <!-- Mostrar mensajes flash (feedback al usuario) -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    <?php endif; ?>
     <?php if (session()->getFlashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('warning') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    <?php endif; ?>


    <!-- Paneles de Resumen -->
    <div class="row mb-4 d-flex align-items-stretch">
        <!-- Valor Total Inventario (Precio Público) -->
        <div class="col-md-4 d-flex">
            <div class="card mb-3 flex-fill rounded-0 shadow-sm">
                <div class="card-header bg-primary text-white rounded-0">
                    <h5 class="card-title mb-0"><i class="fas fa-dollar-sign"></i> Valor de Inventario (Público)</h5>
                </div>
                <div class="card-body bg-light">
                    <h2 class="card-text text-dark"><?= number_to_currency($valor_total_inventario, 'MXM', 'es_MX', 2) ?></h2>
                    <p class="card-text small text-muted">
                        Valor total del inventario basado en precios de venta al público.
                    </p>
                </div>
            </div>
        </div>

        <!-- Valor Utilidades Estimadas -->
        <div class="col-md-4 d-flex">
            <div class="card mb-3 flex-fill rounded-0 shadow-sm">
                <div class="card-header bg-success text-white rounded-0">
                    <h5 class="card-title mb-0"><i class="fas fa-chart-line"></i> Utilidad Bruta Estimada</h5>
                </div>
                <div class="card-body bg-light">
                     <!-- Calculado como Venta_Total - Costo_Total -->
                    <h2 class="card-text text-dark"><?= number_to_currency($valor_utilidades, 'MXM', 'es_MX', 2) ?></h2>
                    <p class="card-text small text-muted">
                        Diferencia entre valor de venta y costo de proveedor. (Venta - Costo).
                    </p>
                </div>
            </div>
        </div>

        <!-- Valor Neto Inventario (Costo) -->
        <div class="col-md-4 d-flex">
            <div class="card mb-3 flex-fill rounded-0 shadow-sm">
                <div class="card-header bg-info text-white rounded-0">
                    <h5 class="card-title mb-0"><i class="fas fa-coins"></i> Valor Neto Inventario (Costo)</h5>
                </div>
                <div class="card-body bg-light">
                    <h2 class="card-text text-dark"><?= number_to_currency($valor_neto_inventario, 'MXM', 'es_MX', 2) ?></h2>
                    <p class="card-text small text-muted">
                        Valor total del inventario basado en precios de proveedor (costo).
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de existencias -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Existencias en Inventario</h6>
            <a href="<?= site_url('existencias/nuevo') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Añadir Artículo al Inventario
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>ID Inv.</th>
                            <th>ID Art.</th>
                            <th>Artículo</th>
                            <th>Modelo</th>
                            <th>Cant</th>
                            <th>Mín</th>
                            <th>Var</th>
                            <th>Precio Púb.</th>
                            <th>Precio Prov.</th>
                            <th>Valor Total (Púb.)</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista as $item): ?>
                        <tr>
                            <td><?= esc($item['id_entrada']) ?></td>
                            <td><?= esc($item['id_articulo']) ?></td>
                            <td><?= esc($item['nombre'] ?? 'N/A') ?></td>
                            <td><?= esc($item['modelo'] ?? 'N/A') ?></td>
                            <td class="text-center"><?= esc($item['cantidad']) ?></td>
                            <td class="text-center"><?= esc($item['minimo'] ?? '') ?></td>
                            <?php
                                $variacion = ($item['cantidad'] ?? 0) - ($item['minimo'] ?? 0);
                                $clase_color = ($item['cantidad'] < $item['minimo']) ? 'text-danger text-center' : '';
                            ?>
                            <td class="text-center <?= $clase_color ?>"><?= esc($variacion) ?></td>
                            <td class="text-center"><?= number_to_currency($item['precio_pub'] ?? 0, 'MXN', 'es_MX', 2) ?></td>
                            <td class="text-right"><?= number_to_currency($item['precio_prov'] ?? 0, 'MNX', 'es_MX', 2) ?></td>
                            <td class="text-right"><?= number_to_currency(($item['precio_pub'] ?? 0) * ($item['cantidad'] ?? 0), 'MXN', 'es_MX', 2) ?></td>
                            <td class="text-center">
                                <a href="" class="btn btn-warning btn-sm rounded-0" title="Editar Cantidad" data-bs-toggle="modal" data-bs-target="#modalNumerico" @click = "cambiar_inventario(<?= $item['id_entrada'] ?>)">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <!-- Modal -->
                                <div class="modal fade" id="modalNumerico" tabindex="-1" aria-labelledby="modalNumericoLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalNumericoLabel">Ajustar valores</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="d-flex align-items-center">
                                                <label for="cantidad1" class="form-label mr-2">Cambiar la cantidad</label>
                                                <input type="number" class="form-control number-input mb-0 w-50" id="cantidad1" max="100" min="1" v-model="inventario.cantidad">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="button" class="btn btn-primary" @click = "guardar_rapido">Guardar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?= form_open('/existencias/eliminar/' . $item['id_entrada'], ['class' => 'd-inline', 'onsubmit' => "return confirm('¿Estás seguro de querer eliminar este registro del inventario?');"]) ?>
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm rounded-0" title="Eliminar Registro">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?= form_close() ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                     <tfoot>
                         <tr>
                             <th colspan="9" class="text-right">TOTALES:</th>
                             <th class="text-right"><?= number_to_currency($valor_total_inventario, 'USD', 'es_MX', 2) ?></th>
                             <th></th> <!-- Columna vacía para acciones -->
                         </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Incluye tu JS si tienes inicialización de DataTables u otros -->
<script src="<?php echo base_url('public/js/existencias.js'); ?>"></script>
<!-- Asegúrate que DataTables esté inicializado si usas id="dataTable" -->
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable(); // Descomenta si usas DataTables y está incluido en tu template
    });
</script>

<?= $this->endSection() ?>