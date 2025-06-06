<?php echo $this->extend('Panel/panel_template')?>

<?php echo $this->section('contenido')?>

    <div class="container mt-4">
        <h2><?= esc($titulo) ?></h2>

        <?php if (isset($validation)): ?>
            <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
        <?php endif; ?>

        <form action="<?= base_url('cuentas/guardar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="Banco">Banco</label>
                <input type="text" class="form-control" id="Banco" name="banco" value="<?= old('Banco') ?>">
            </div>

            <div class="form-group">
                <label for="NoCta">No. Cuenta</label>
                <input type="number" class="form-control" id="NoCta" name="cuenta" value="<?= old('NoCta') ?>">
            </div>

            <div class="form-group">
                <label for="Saldo">Saldo</label>
                <input type="text" class="form-control" id="Saldo" name="saldo" value="<?= old('Saldo') ?>">
                <small class="form-text text-muted">Utilice el formato decimal (ej: 100.50)</small>
            </div>

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="<?= base_url('cuentas') ?>" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

<?php echo $this->endSection() ?>