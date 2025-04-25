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
        <a href="<?php echo base_url('/gastos/nuevo');?>" class="btn btn-primary">Nuevo Movimiento</a>
        <a href="<?php echo base_url('/reportes/reporte'); ?>" class="btn btn-primary">Informe Financiero</a>
        <a href="<?php echo base_url('/cuentas/'); ?>" class="btn btn-primary">Bancos</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th class="text-end">Entrada</th>
                <th class="text-end">Salida</th>
                <th>Fecha</th>
                <th>Cuenta</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalEntradas = 0;
            $totalSalidas = 0;
            
            foreach ($gastos as $gasto): 
                $totalEntradas += $gasto['entrada'];
                $totalSalidas += $gasto['salida'];
            ?>
                <tr>
                    <td><?= $gasto['id_registro']; ?></td>
                    <td><?= esc($gasto['descripcion']); ?></td>
                    <td class="text-end text-success"><?= $gasto['entrada'] > 0 ? number_format($gasto['entrada'], 2) : '-'; ?></td>
                    <td class="text-end text-danger"><?= $gasto['salida'] > 0 ? number_format($gasto['salida'], 2) : '-'; ?></td>
                    <td><?= date('d/m/Y', strtotime($gasto['fecha_gasto'])); ?></td>
                    <td><?= esc($gasto['banco'] ?? ''); ?></td>
                    <td>
                        <a href="<?php echo base_url('gastos/mostrar/').$gasto['id_registro']?>" class="btn btn-sm btn-info">Ver</a>
                        <a href="/gastos/editar/<?= $gasto['id_registro']; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <form action="/gastos/eliminar/<?= $gasto['id_registro']; ?>" method="post" style="display: inline;">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este movimiento?');">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="table-active">
                <th colspan="2">TOTALES</th>
                <th class="text-end text-success"><?= number_format($totalEntradas, 2); ?></th>
                <th class="text-end text-danger"><?= number_format($totalSalidas, 2); ?></th>
                <th colspan="3"></th>
            </tr>
            <tr class="table-secondary">
                <th colspan="2">BALANCE FINAL</th>
                <th colspan="2" class="text-end <?= ($totalEntradas - $totalSalidas) >= 0 ? 'text-success' : 'text-danger'; ?>">
                    <?= number_format($totalEntradas - $totalSalidas, 2); ?>
                </th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
</div>

<style>
    .text-success { color: #28a745 !important; font-weight: bold; }
    .text-danger { color: #dc3545 !important; font-weight: bold; }
</style>

<?= $this->endSection(); ?>