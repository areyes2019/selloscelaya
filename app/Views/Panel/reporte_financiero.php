<?= $this->extend('Panel/panel_template'); ?>

<?= $this->section('contenido'); ?>
<div class="container-fluid mt-3">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Reporte Financiero</h3>
                </div>
                <div class="card-body">
                    <!-- Filtros por fecha (se mantiene igual) -->
                    
                    <!-- Sección de Beneficios -->
                    <div class="row mt-4">
                        <!-- Beneficio Neto -->
                        <div class="col-md-6">
                            <div class="card card-outline card-success h-100">
                                <div class="card-header">
                                    <h3 class="card-title">Beneficio Neto</h3>
                                </div>
                                <div class="card-body text-center">
                                    <h1 class="display-4 text-success">
                                        $<?= number_format($beneficio_total, 2) ?>
                                    </h1>
                                    <p class="text-muted">Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> - <?= date('d/m/Y', strtotime($fecha_fin)) ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Presupuesto Publicidad (10%) -->
                        <div class="col-md-6">
                            <div class="card card-outline card-info h-100">
                                <div class="card-header">
                                    <h3 class="card-title">Presupuesto Publicidad (10%)</h3>
                                </div>
                                <div class="card-body text-center">
                                    <h1 class="display-4 text-info">
                                        $<?= number_format($beneficio_total * 0.10, 2) ?>
                                    </h1>
                                    <p class="text-muted">Disponible para campañas de marketing</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de resumen (actualizada) -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Detalle Financiero</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Concepto</th>
                                                <th class="text-end">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Total Ventas</td>
                                                <td class="text-end">$<?= number_format($ventas_brutas, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Inversión Material</td>
                                                <td class="text-end">$<?= number_format($inversion_total, 2) ?></td>
                                            </tr>
                                            <tr>
                                                <td>Gastos Operativos</td>
                                                <td class="text-end">$<?= number_format($gastos_operativos ?? 0, 2) ?></td>
                                            </tr>
                                            <tr class="table-success fw-bold">
                                                <td>Beneficio Bruto</td>
                                                <td class="text-end">$<?= number_format(($ventas_brutas - $inversion_total), 2) ?></td>
                                            </tr>
                                            <tr class="table-primary fw-bold">
                                                <td>Presupuesto Publicidad (10%)</td>
                                                <td class="text-end">$<?= number_format($beneficio_total * 0.10, 2) ?></td>
                                            </tr>
                                            <tr class="table-success fw-bold">
                                                <td>Beneficio Neto Final</td>
                                                <td class="text-end">$<?= number_format($beneficio_total * 0.90, 2) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Saldos Bancarios (se mantiene igual) -->
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>