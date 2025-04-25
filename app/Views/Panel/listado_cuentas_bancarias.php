<?php echo $this->extend('Panel/panel_template')?>

<?php echo $this->section('contenido')?>

    <div class="container mt-4">
        <h2><?= esc($titulo) ?></h2>

        <?php if (session()->get('mensaje')): ?>
            <div class="alert alert-success"><?= session()->get('mensaje') ?></div>
        <?php endif; ?>

        <?php if (session()->get('error')): ?>
            <div class="alert alert-danger"><?= session()->get('error') ?></div>
        <?php endif; ?>

        <a href="<?= base_url('cuentas/nuevo') ?>" class="btn btn-success mb-3">Nueva Cuenta</a>

        <?php if (! empty($cuentas) && is_array($cuentas)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Banco</th>
                        <th>No. Cuenta</th>
                        <th>Saldo</th>
                        <th>Creado</th>
                        <th>Actualizado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cuentas as $cuenta): ?>
                        <tr>
                            <td><?= esc($cuenta['id_cuenta']) ?></td>
                            <td><?= esc($cuenta['banco']) ?></td>
                            <td><?= esc($cuenta['cuenta']) ?></td>
                            <td><?= esc(number_format($cuenta['saldo'], 2)) ?></td>
                            <td><?= esc($cuenta['created_at']) ?></td>
                            <td><?= esc($cuenta['updated_at']) ?></td>
                            <td>
                                <a href="<?= base_url('cuentas/editar/' . $cuenta['id_cuenta']) ?>" class="btn btn-primary btn-sm">Editar</a>
                                <a href="<?= base_url('cuentas/borrar/' . $cuenta['id_cuenta']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta cuenta?')">Borrar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay cuentas registradas.</p>
        <?php endif; ?>
    </div>

<?php echo $this->endSection() ?>