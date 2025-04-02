<?= $this->extend('Panel/panel_template') ?>

<?= $this->section('contenido') ?>

<div class="container-fluid">
    <div class="row mb-4 d-flex align-items-stretch">
        <!-- Panel Inventario -->
        <div class="col-md-4 d-flex">
            <div class="card mb-3 flex-fill rounded-0">
                <div class="card-header bg-dark text-white rounded-0">
                    <h5 class="card-title mb-0">Valor de Inventario</h5>
                </div>
                <div class="card-body bg-light">
                    <h2 class="card-text text-dark">$<?= number_format($super_total, 2) ?></h2>
                    <p class="card-text small text-muted">
                        Valor total del inventario basado en precios públicos.
                    </p>
                </div>
            </div>
        </div>

        <!-- Panel Utilidades -->
        <div class="col-md-4 d-flex">
            <div class="card mb-3 flex-fill rounded-0">
                <div class="card-header bg-dark text-white rounded-0">
                    <h5 class="card-title mb-0">Valor de las Utilidades</h5>
                </div>
                <div class="card-body bg-light">
                    <h2 class="card-text text-dark">$<?= number_format($super_total * 0.30, 2) ?></h2>
                    <p class="card-text small text-muted">
                        Estimado de utilidades (30% sobre valor de inventario).
                    </p>
                </div>
            </div>
        </div>

        <!-- Panel Valor Neto -->
        <div class="col-md-4 d-flex">
            <div class="card mb-3 flex-fill rounded-0">
                <div class="card-header bg-dark text-white rounded-0">
                    <h5 class="card-title mb-0">Valor Neto</h5>
                </div>
                <div class="card-body bg-light">
                    <h2 class="card-text text-dark">$<?= number_format($super_total * 0.70, 2) ?></h2>
                    <p class="card-text small text-muted">
                        Valor de inventario menos utilidades estimadas.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de existencias -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Existencias en Inventario</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Artículo</th>
                            <th>Cantidad</th>
                            <th>Precio Público</th>
                            <th>Valor Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista as $item): ?>
                        <tr>
                            <td><?= $item['id_articulo'] ?></td>
                            <td><?= $item['nombre_articulo'] ?? 'N/A' ?></td>
                            <td><?= $item['cantidad'] ?></td>
                            <td>$<?= number_format($item['precio_pub'], 2) ?></td>
                            <td>$<?= number_format($item['precio_pub'] * $item['cantidad'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>