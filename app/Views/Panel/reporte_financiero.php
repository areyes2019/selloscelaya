<?= $this->extend('Panel/panel_template') ?>

<?= $this->section('contenido') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        <a href="<?= base_url('admin/gastos') ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver a Gastos
        </a>
    </div>

    <!-- Filtros de Fecha -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtrar por Fecha</h6>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('gastos/reporte') ?>" class="form-inline">
                <div class="form-group mr-3 mb-2">
                    <label for="fecha_inicio" class="mr-2">Desde:</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                           value="<?= $fecha_inicio ?>" required>
                </div>
                <div class="form-group mr-3 mb-2">
                    <label for="fecha_fin" class="mr-2">Hasta:</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                           value="<?= $fecha_fin ?>" required>
                </div>
                <button type="submit" class="btn btn-primary mb-2">
                    <i class="fas fa-search fa-sm"></i> Buscar
                </button>
                
                <!-- Botones de periodos rápidos -->
                <div class="btn-group ml-3 mb-2" role="group">
                    <button type="button" class="btn btn-outline-secondary periodo-btn" data-dias="0">
                        Hoy
                    </button>
                    <button type="button" class="btn btn-outline-secondary periodo-btn" data-dias="6">
                        Esta Semana
                    </button>
                    <button type="button" class="btn btn-outline-secondary periodo-btn" data-mes="1">
                        Este Mes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Período: <?= date('d/m/Y', strtotime($fecha_inicio)) ?> al <?= date('d/m/Y', strtotime($fecha_fin)) ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Ingresos -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Ventas</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        $<?= number_format($total_ventas, 2) ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gastos -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Total Gastos</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        $<?= number_format($total_gastos, 2) ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inversión -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Inversión en Productos</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        $<?= number_format($total_invertido, 2) ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Utilidades -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Utilidades Netas</div>
                                    <div class="h5 mb-0 font-weight-bold <?= $utilidades_netas >= 0 ? 'text-success' : 'text-danger' ?>">
                                        $<?= number_format($utilidades_netas, 2) ?>
                                    </div>
                                    <div class="mt-1 text-xs font-weight-bold <?= $margen_ganancia >= 0 ? 'text-success' : 'text-danger' ?>">
                                        (<?= number_format($margen_ganancia, 2) ?>% margen)
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen Detallado -->
            <div class="row mt-4">
                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Resumen de Ingresos vs Egresos</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Concepto</th>
                                            <th class="text-right">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Total Ventas</td>
                                            <td class="text-right text-success">$<?= number_format($total_ventas, 2) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Total Gastos</td>
                                            <td class="text-right text-danger">-$<?= number_format($total_gastos, 2) ?></td>
                                        </tr>
                                        <tr>
                                            <td>Inversión en Productos</td>
                                            <td class="text-right text-danger">-$<?= number_format($total_invertido, 2) ?></td>
                                        </tr>
                                        <tr class="font-weight-bold">
                                            <td>Utilidad Neta</td>
                                            <td class="text-right <?= $utilidades_netas >= 0 ? 'text-success' : 'text-danger' ?>">
                                                $<?= number_format($utilidades_netas, 2) ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Distribución de Egresos</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="egresosChart"></canvas>
                            </div>
                            <div class="mt-4 text-center small">
                                <span class="mr-2">
                                    <i class="fas fa-circle text-danger"></i> Gastos
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-circle text-warning"></i> Inversión
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="row mt-3">
                <div class="col-12">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print fa-sm"></i> Imprimir Reporte
                    </button>
                    <a href="#" class="btn btn-success">
                        <i class="fas fa-file-excel fa-sm"></i> Exportar a Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de distribución de egresos
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('egresosChart').getContext('2d');
        var myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Gastos', 'Inversión en Productos'],
                datasets: [{
                    data: [<?= $total_gastos ?>, <?= $total_invertido ?>],
                    backgroundColor: ['#e74a3b', '#f6c23e'],
                    hoverBackgroundColor: ['#d62c1a', '#e0a800'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                },
                cutoutPercentage: 80,
            },
        });

        // Manejar botones de periodos rápidos
        document.querySelectorAll('.periodo-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                let fechaInicio = new Date();
                let fechaFin = new Date();
                
                if (this.hasAttribute('data-dias')) {
                    const dias = parseInt(this.getAttribute('data-dias'));
                    fechaInicio.setDate(fechaInicio.getDate() - dias);
                } else if (this.hasAttribute('data-mes')) {
                    fechaInicio.setDate(1);
                    fechaFin = new Date(fechaInicio.getFullYear(), fechaInicio.getMonth() + 1, 0);
                }
                
                document.getElementById('fecha_inicio').value = formatDate(fechaInicio);
                document.getElementById('fecha_fin').value = formatDate(fechaFin);
                document.querySelector('form').submit();
            });
        });
        
        function formatDate(date) {
            const year = date.getFullYear();
            let month = date.getMonth() + 1;
            let day = date.getDate();
            
            if (month < 10) month = '0' + month;
            if (day < 10) day = '0' + day;
            
            return `${year}-${month}-${day}`;
        }
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>