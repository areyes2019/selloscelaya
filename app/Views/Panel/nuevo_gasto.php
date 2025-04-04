<?= $this->extend('Panel/panel_template'); ?>

<?= $this->section('contenido'); ?>
<div class="container mt-4">
    <h1><?= $title; ?></h1>

    <?php if (isset($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-6">
            <form action="/gastos/guardar" method="post">
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                </div>
                
                <div class="mb-3">
                    <label for="monto" class="form-label">Monto</label>
                    <input type="number" step="0.01" class="form-control" id="monto" name="monto" required>
                </div>
                
                <div class="mb-3">
                    <label for="fecha_gasto" class="form-label">Fecha del Gasto</label>
                    <input type="date" class="form-control" id="fecha_gasto" name="fecha_gasto" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="<?php echo base_url('gastos/inicio'); ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>