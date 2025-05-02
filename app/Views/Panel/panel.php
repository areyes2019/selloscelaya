<?= $this->extend('Panel/panel_template') ?>
<?= $this->section('contenido') ?>

<h1>Ordenes de Trabajo</h1>
<a href="<?= site_url('ordenes/descargar_ordenes') ?>" target="_blank" class="btn btn-danger rounded-0 mb-3">
    <i class="fas fa-file-pdf"></i> <!-- Icono opcional (ej. Font Awesome) -->
    Descargar Órdenes (PDF)
</a>

<a href="<?= site_url('ordenes/pedidos-pendientes') ?>" class="btn btn-warning rounded-0 mb-3" target="_blank">
    <i class="fas fa-tags"></i> Generar Etiquetas Pendientes (PDF)
</a>

<!-- Contenido de las Pestañas -->
<div class="card rounded-0 shadow-sm">
    <!-- Panel/ordenes_dashboard.php -->
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Clave</th>
                <th>Saldo</th>
                <th>Status</th>
                <th>Acción</th> <!-- Nueva columna -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ordenes as $orden): ?>
                <tr>
                    <td><?= esc($orden->cliente_nombre) ?></td>
                    <td><?= esc($orden->cliente_telefono) ?></td>
                    <td><?= esc($orden->clave) ?></td>
                    <td>
                        <?php if ($orden->saldo === 'Pagado'): ?>
                            <span class="badge bg-success"><?= esc($orden->saldo) ?></span>
                        <?php else: ?>
                            <?= esc($orden->saldo) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                            $badgeClass = 'secondary'; // valor por defecto

                            $statusLower = strtolower($orden->status); // Convertimos a minúsculas

                            if ($statusLower === 'dibujo') {
                                $badgeClass = 'primary';
                            } elseif ($statusLower === 'elaboracion') {
                                $badgeClass = 'warning';
                            } elseif ($statusLower === 'entrega') {
                                $badgeClass = 'success';
                            } elseif ($statusLower === 'entregado') {
                                $badgeClass = 'success'; // también success para entregado
                            }
                        ?>
                        <span class="badge bg-<?= $badgeClass ?>">
                            <?= ucfirst(esc($orden->status)) ?>
                        </span>
                    </td>

                    <td>
                        <?php if ($orden->status !== 'Entregado'): ?>
                        <form action="<?= base_url('ordenes/actualizar-status/' . $orden->id_ot) ?>" method="post" style="display:inline;">
                            <?= csrf_field() ?>
                            <?php
                                if ($orden->status == 'Dibujo') {
                                    echo '<button type="submit" class="btn btn-primary btn-sm">A Elaboración</button>';
                                } elseif ($orden->status == 'Elaboracion') {
                                    echo '<button type="submit" class="btn btn-warning btn-sm">A Entrega</button>';
                                } elseif ($orden->status == 'Entrega') {
                                    echo '<button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-truck"></i> Entregado
                                          </button>';
                                }
                            ?>
                        </form>
                        <?php else: ?>
                        <span class="badge bg-success">
                            <i class="bi bi-check-lg"></i>
                        </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
    $( document ).ready(function() {
        new DataTable('#tabla');
    });
</script>
<?= $this->endSection() ?>