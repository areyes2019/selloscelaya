<?= $this->extend('Panel/panel_template'); ?>

<?= $this->section('contenido'); ?>
<div class="container mt-4">
    <h1><?= $title; ?></h1>

    <?php if (session('message')): ?>
        <div class="alert alert-success">
            <?= session('message'); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm rounded-0 mt-3">
        <div class="card-body ">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="gastos">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Descripción</th>
                            <th class="text-end">Entrada</th>
                            <th class="text-end">Salida</th>
                            <th>Fecha</th>
                            <th>Cuenta Origen</th>
                            <th>Cuenta Destino</th>
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
                            
                            // Determinar texto para cuentas
                            $cuentaOrigen = ($gasto['cuenta_origen'] == 0) ? 'Externo' : esc($gasto['banco_origen'] ?? '');
                            $cuentaDestino = ($gasto['cuenta_destino'] == 0) ? 'Externo' : esc($gasto['banco_destino'] ?? '');
                        ?>
                            <tr>
                                <td><?= $gasto['id_registro']; ?></td>
                                <td><?= esc($gasto['descripcion']); ?></td>
                                <td class="text-end <?= $gasto['entrada'] > 0 ? 'text-success' : ''; ?>">
                                    <?= $gasto['entrada'] > 0 ? '$'.number_format($gasto['entrada'], 2) : '-'; ?>
                                </td>
                                <td class="text-end <?= $gasto['salida'] > 0 ? 'text-danger' : ''; ?>">
                                    <?= $gasto['salida'] > 0 ? '$'.number_format($gasto['salida'], 2) : '-'; ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($gasto['fecha_gasto'])); ?></td>
                                <td><?= $cuentaOrigen; ?></td>
                                <td><?= $cuentaDestino; ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('gastos/mostrar/'.$gasto['id_registro']); ?>" class="btn btn-sm btn-dark" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= base_url('gastos/editar/'.$gasto['id_registro']); ?>" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?= base_url('gastos/eliminar/'.$gasto['id_registro']); ?>" method="post" class="d-inline">
                                            <?= csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este movimiento?');">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-group-divider">
                        <tr class="table-active">
                            <th colspan="2">TOTALES</th>
                            <th class="text-end text-success">$<?= number_format($totalEntradas, 2); ?></th>
                            <th class="text-end text-danger">$<?= number_format($totalSalidas, 2); ?></th>
                            <th colspan="4"></th>
                        </tr>
                        <tr class="table-secondary">
                            <th colspan="2">BALANCE FINAL</th>
                            <th colspan="2" class="text-end <?= ($totalEntradas - $totalSalidas) >= 0 ? 'text-success' : 'text-danger'; ?>">
                                $<?= number_format($totalEntradas - $totalSalidas, 2); ?>
                            </th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .text-success { color: #28a745 !important; font-weight: bold; }
    .text-danger { color: #dc3545 !important; font-weight: bold; }
    .table th { white-space: nowrap; }
    .btn-group .btn { padding: 0.25rem 0.5rem; }
</style>
<script type="text/javascript">
    new DataTable('#gastos');
</script>
<?= $this->endSection(); ?>