<?= $this->extend('Panel/panel_template'); ?>

<?= $this->section('contenido'); ?>
<div class="container mt-4">
    <h1><?= $title; ?></h1>

    <?php if (session('message')): ?>
        <div class="alert alert-success">
            <?= session('message'); ?>
        </div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="/gastos/nuevo" class="btn btn-primary">Nuevo Gasto</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($gastos as $gasto): ?>
                <tr>
                    <td><?= $gasto['id_registro']; ?></td>
                    <td><?= esc($gasto['descripcion']); ?></td>
                    <td><?= number_format($gasto['monto'], 2); ?></td>
                    <td><?= date('d/m/Y', strtotime($gasto['fecha_gasto'])); ?></td>
                    <td>
                        <a href="<?php echo base_url('gastos/mostrar/').$gasto['id_registro']?>" class="btn btn-sm btn-info">Ver</a>
                        <a href="/gastos/editar/<?= $gasto['id_registro']; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <form action="/gastos/eliminar/<?= $gasto['id_registro']; ?>" method="post" style="display: inline;">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este gasto?');">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection(); ?>