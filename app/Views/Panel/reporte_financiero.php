<?= $this->extend('Panel/panel_template'); ?>

<?= $this->section('contenido'); ?>
    <div class="container-fluid mt-3" id="app">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Reporte Financiero</h3>
                        <div class="card-tools">
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filtros por fecha -->
                        <form class="row mb-4">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="fecha-inicio">Fecha Inicio</label>
                                    <input type="date" class="form-control" id="fecha-inicio" value="2025-11-01">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="fecha-fin">Fecha Fin</label>
                                    <input type="date" class="form-control" id="fecha-fin" value="2025-11-30">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-center">
                                <button type="button" class="btn btn-primary btn-block rounded-0">Filtrar</button>
                            </div>
                        </form>


                        <!-- Beneficio neto destacado -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card card-outline card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">Beneficio Neto</h3>
                                    </div>
                                    <div class="card-body text-center">
                                        <h1 class="display-4 text-success">
                                            ${{resumen.beneficio_neto}}
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
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Entradas Brutas</td>
                                                    <td class="text-end">${{resumen.total_bruto}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Inversión Material</td>
                                                    <td class="text-end">${{resumen.capital}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Gastos Operativos</td>
                                                    <td class="text-end">${{resumen.gastos}}</td>
                                                </tr>
                                                <tr class="table-success fw-bold">
                                                    <td>Beneficio Bruto</td>
                                                    <td class="text-end">${{resumen.beneficio_bruto}}</td>
                                                </tr>
                                                <tr class="table-success fw-bold">
                                                    <td>Beneficio Neto</td>
                                                    <td class="text-end">${{resumen.beneficio_neto}}</td>
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
