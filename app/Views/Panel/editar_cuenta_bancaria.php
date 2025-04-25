<?php echo $this->extend('Panel/panel_template')?>

<?php echo $this->section('contenido')?>

    <div class="container mt-4">
        <h2><?= esc($titulo) ?></h2>

        <?php if (isset($validation)): ?>
            <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
        <?php endif; ?>

        <form action="<?= base_url('cuentas/actualizar/' . $cuenta['id_cuenta']) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="post">
            <div class="form-group">
                <label for="Banco">Banco</label>
                <input type="text" class="form-control" id="Banco" name="banco" value="<?= old('banco', $cuenta['banco']) ?>">
            </div>

            <div class="form-group">
                <label for="NoCta">No. Cuenta</label>
                <input type="number" class="form-control" id="NoCta" name="cuenta" value="<?= old('cuenta', $cuenta['cuenta']) ?>">
            </div>

            <div class="form-group">
                <label for="Saldo">Saldo</label>
                <input type="text" class="form-control" id="Saldo" name="saldo" value="<?= old('saldo', $cuenta['saldo']) ?>">
                <small class="form-text text-muted">Utilice el formato decimal (ej: 100.50)</small>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="<?= base_url('cuentas') ?>" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

<?php echo $this->endSection() ?>