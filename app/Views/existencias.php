<?= $this->extend('Plantilla/panel_template') ?>

<?= $this->section('contenido') ?>

<div class="container-fluid">
    <!-- Paneles superiores -->
    <div class="row mb-4">
        <!-- Panel: Valor de inventario -->
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Valor de Inventario</h5>
                </div>
                <div class="card-body">
                    <h2 class="card-text">$<?= number_format($super_total, 2) ?></h2>
                    <p class="card-text small">Valor total del inventario basado en precios públicos.</p>
                </div>
            </div>
        </div>

        <!-- Panel: Valor de las utilidades -->
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Valor de las Utilidades</h5>
                </div>
                <div class="card-body">
                    <h2 class="card-text">$<?= number_format($super_total * 0.30, 2) ?></h2>
                    <p class="card-text small">Estimado de utilidades (30% sobre valor de inventario).</p>
                </div>
            </div>
        </div>

        <!-- Panel: Valor neto -->
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Valor Neto</h5>
                </div>
                <div class="card-body">
                    <h2 class="card-text">$<?= number_format($super_total * 0.70, 2) ?></h2>
                    <p class="card-text small">Valor de inventario menos utilidades estimadas.</p>
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