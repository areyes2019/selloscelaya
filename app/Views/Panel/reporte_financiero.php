<?= $this->extend('Panel/panel_template'); ?>

<?= $this->section('contenido'); ?>
    <div class="container-fluid mt-3" id="app">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Reporte Financiero</h3>
                        <div class="card-tools">
                            <button class="btn btn-sm btn-light">HOY</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filtros por fecha -->
                        <form class="row mb-4">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="fecha-inicio">Fecha Inicio</label>
                                    <input type="date" class="form-control" id="fecha-inicio" value="2023-11-01">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="fecha-fin">Fecha Fin</label>
                                    <input type="date" class="form-control" id="fecha-fin" value="2023-11-30">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-center">
                                <button type="button" class="btn btn-primary btn-block rounded-0">Filtrar</button>
                            </div>
                        </form>

                        <!-- Resumen financiero -->
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-info text-white p-3">
                                    <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Entradas Brutas</span>
                                        <span class="info-box-number">$12,450.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-warning text-white p-3">
                                    <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Inversión Material</span>
                                        <span class="info-box-number">$3,780.50</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-success text-white p-3">
                                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Beneficio</span>
                                        <span class="info-box-number">$8,669.50</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-danger text-white p-3">
                                    <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Gastos</span>
                                        <span class="info-box-number">$2,340.75</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Beneficio neto destacado -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card card-outline card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">Beneficio Neto</h3>
                                    </div>
                                    <div class="card-body text-center">
                                        <h1 class="display-4 text-success">
                                            $6,328.75
                                        </h1>
                                        <p class="text-muted">Período: 01/11/2023 - 30/11/2023</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de resumen -->
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
                                                    <th class="text-end">Porcentaje</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Entradas Brutas</td>
                                                    <td class="text-end">$12,450.00</td>
                                                    <td class="text-end">100%</td>
                                                </tr>
                                                <tr>
                                                    <td>Inversión Material</td>
                                                    <td class="text-end">$3,780.50</td>
                                                    <td class="text-end">30.4%</td>
                                                </tr>
                                                <tr>
                                                    <td>Gastos Operativos</td>
                                                    <td class="text-end">$2,340.75</td>
                                                    <td class="text-end">18.8%</td>
                                                </tr>
                                                <tr class="table-success fw-bold">
                                                    <td>Beneficio Neto</td>
                                                    <td class="text-end">$6,328.75</td>
                                                    <td class="text-end">50.8%</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url('/public/js/balance.js'); ?>"></script>
<?= $this->endSection(); ?>
