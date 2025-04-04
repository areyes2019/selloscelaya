<?= $this->extend('Panel/panel_template'); ?>

<?= $this->section('contenido'); ?>
<div class="container mt-4">
    <h1><?= $title; ?></h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Detalles del Gasto</h5>
            
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9"><?= $gasto['id_registro']; ?></dd>
                
                <dt class="col-sm-3">Descripción</dt>
                <dd class="col-sm-9"><?= esc($gasto['descripcion']); ?></dd>
                
                <dt class="col-sm-3">Monto</dt>
                <dd class="col-sm-9"><?= number_format($gasto['monto'], 2); ?></dd>
                
                <dt class="col-sm-3">Fecha</dt>
                <dd class="col-sm-9"><?= date('d/m/Y', strtotime($gasto['fecha_gasto'])); ?></dd>
            </dl>
            
            <div class="mt-3">
                <a href="/gastos/editar/<?= $gasto['id_registro']; ?>" class="btn btn-warning">Editar</a>
                <form action="<?php echo base_url('/gastos/eliminar/').$gasto['id_registro']; ?>" method="post" style="display: inline;">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar este gasto?');">Eliminar</button>
                </form>
                <a href="/gastos" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>