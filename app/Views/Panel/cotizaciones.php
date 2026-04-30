<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show mt-2 mx-3" id="alert-flash" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show mt-2 mx-3" id="alert-flash" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- ── Vue App ─────────────────────────────────────────────────────── -->
<div id="app-cotizaciones" class="container mt-3">

    <!-- Header -->
    <div class="my-card d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Cotizaciones</h2>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" @click="cargar" :disabled="cargando">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
            <button type="button" class="btn-my" data-bs-toggle="modal" data-bs-target="#modalNuevaCotizacion">
                <i class="bi bi-file-earmark-plus"></i> Crear Cotización
            </button>
        </div>
    </div>

    <!-- Loading -->
    <div v-if="cargando" class="text-center py-5">
        <div class="spinner-border text-secondary" role="status"></div>
        <p class="mt-2 text-muted">Cargando cotizaciones…</p>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="alert alert-danger">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ error }}
        <button class="btn btn-sm btn-outline-danger ms-3" @click="cargar">Reintentar</button>
    </div>

    <template v-else>

        <!-- Búsqueda global -->
        <div class="mb-3">
            <div class="input-group" style="max-width:360px">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input v-model="busqueda" type="text" class="form-control"
                       placeholder="Buscar por nombre, correo o ID…">
                <button v-if="busqueda" class="btn btn-outline-secondary" @click="busqueda=''">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>

        <!-- Filtro por rango de fecha -->
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>Período:</span>
            <button v-for="f in [{val:'hoy',label:'Hoy'},{val:'semana',label:'Esta semana'},{val:'mes',label:'Este mes'}]"
                    :key="f.val"
                    class="btn btn-sm"
                    :class="filtroFecha===f.val ? 'btn-dark' : 'btn-outline-secondary'"
                    @click="filtroFecha=f.val">
                {{ f.label }}
            </button>
            <span class="text-muted small ms-auto">
                <i class="bi bi-funnel me-1"></i>Mostrando: <strong>{{ etiquetaFecha }}</strong>
            </span>
        </div>

        <!-- Tabs nav -->
        <ul class="nav nav-tabs mb-0">
            <li v-for="tab in TABS" :key="tab.id" class="nav-item">
                <button class="nav-link"
                        :class="{ active: tabActiva === tab.id }"
                        @click="tabActiva = tab.id">
                    <i :class="'bi ' + tab.icon + ' me-1'"></i>{{ tab.label }}
                </button>
            </li>
        </ul>

        <div class="tab-content border border-top-0 rounded-bottom p-3 bg-white">

            <!-- ════════════════════════════════════
                 TAB: Cotizaciones (estado comercial)
                 ════════════════════════════════════ -->
            <div v-show="tabActiva === 'cotizaciones'">
                <!-- Sub-filtros estatus -->
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button v-for="f in [
                        {val:'todos', label:'Todos'},
                        {val:'0',     label:'Pendiente'},
                        {val:'1',     label:'Enviada'},
                        {val:'2',     label:'Pagada'},
                        {val:'3',     label:'Entregada'},
                    ]" :key="f.val"
                        class="btn btn-sm"
                        :class="filtroEstatus===f.val ? 'btn-dark' : 'btn-outline-secondary'"
                        @click="filtroEstatus=f.val">
                        {{ f.label }}
                        <span v-if="f.val==='todos'" class="badge bg-white text-dark ms-1">
                            {{ listaComercial.length }}
                        </span>
                    </button>
                </div>

                <div class="responsive-table-container">
                    <table class="advanced-responsive-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Email</th>
                                <th>Total</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="listaComercial.length === 0">
                                <td colspan="6" class="text-center text-muted py-4">Sin resultados</td>
                            </tr>
                            <tr v-for="c in listaComercial" :key="c.id_cotizacion">
                                <td data-label="ID">#{{ c.id_cotizacion }}</td>
                                <td data-label="Cliente">{{ c.nombre }}</td>
                                <td data-label="Email">{{ c.correo || '—' }}</td>
                                <td data-label="Total">{{ moneda(c.total) }}</td>
                                <td data-label="Estatus">
                                    <span class="badge" :class="badgeEstatus(c.estatus).cls">
                                        {{ badgeEstatus(c.estatus).label }}
                                    </span>
                                </td>
                                <td data-label="Acciones">
                                    <a :href="'/pagina_cotizador/'+c.slug" class="btn btn-view btn-sm"><i class="bi bi-eye"></i></a>
                                    <a :href="'/descargar_cotizacion/'+c.id_cotizacion" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i></a>
                                    <button class="btn btn-delete btn-sm" @click="eliminar(c.id_cotizacion)"><i class="bi bi-trash3"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ════════════════════════════════════
                 TAB: Pagos (estado financiero)
                 ════════════════════════════════════ -->
            <div v-show="tabActiva === 'pagos'">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button v-for="f in [
                        {val:'todos',     label:'Todos'},
                        {val:'pendiente', label:'Pendiente'},
                        {val:'anticipo',  label:'Anticipo'},
                        {val:'parcial',   label:'Parcial'},
                        {val:'pagado',    label:'Pagado'},
                    ]" :key="f.val"
                        class="btn btn-sm"
                        :class="filtroFinanciero===f.val ? 'btn-dark' : 'btn-outline-secondary'"
                        @click="filtroFinanciero=f.val">
                        {{ f.label }}
                    </button>
                </div>

                <div class="responsive-table-container">
                    <table class="advanced-responsive-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Anticipo</th>
                                <th>Saldo</th>
                                <th>Estado Financiero</th>
                                <th>Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="listaFinanciero.length === 0">
                                <td colspan="7" class="text-center text-muted py-4">Sin resultados</td>
                            </tr>
                            <tr v-for="c in listaFinanciero" :key="c.id_cotizacion">
                                <td data-label="ID">#{{ c.id_cotizacion }}</td>
                                <td data-label="Cliente">{{ c.nombre }}</td>
                                <td data-label="Total">{{ moneda(c.total) }}</td>
                                <td data-label="Anticipo">{{ moneda(c.anticipo) }}</td>
                                <td data-label="Saldo">{{ moneda(parseFloat(c.total||0) - parseFloat(c.anticipo||0)) }}</td>
                                <td data-label="Estado Financiero">
                                    <span class="badge" :class="badgeFinanciero(c.estado_financiero).cls">
                                        {{ badgeFinanciero(c.estado_financiero).label }}
                                    </span>
                                </td>
                                <td>
                                    <a :href="'/pagina_cotizador/'+c.slug" class="btn btn-view btn-sm"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ════════════════════════════════════
                 TAB: Facturación (estado fiscal)
                 ════════════════════════════════════ -->
            <div v-show="tabActiva === 'facturacion'">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button v-for="f in [
                        {val:'todos',        label:'Todos'},
                        {val:'sin_facturar', label:'Sin facturar'},
                        {val:'facturada',    label:'Facturada'},
                        {val:'cancelada',    label:'Cancelada'},
                    ]" :key="f.val"
                        class="btn btn-sm"
                        :class="filtroFiscal===f.val ? 'btn-dark' : 'btn-outline-secondary'"
                        @click="filtroFiscal=f.val">
                        {{ f.label }}
                    </button>
                </div>

                <div class="responsive-table-container">
                    <table class="advanced-responsive-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Factura</th>
                                <th>Estado Fiscal</th>
                                <th>Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="listaFiscal.length === 0">
                                <td colspan="6" class="text-center text-muted py-4">Sin resultados</td>
                            </tr>
                            <tr v-for="c in listaFiscal" :key="c.id_cotizacion">
                                <td data-label="ID">#{{ c.id_cotizacion }}</td>
                                <td data-label="Cliente">{{ c.nombre }}</td>
                                <td data-label="Total">{{ moneda(c.total) }}</td>
                                <td data-label="Factura">
                                    <span v-if="c.factura_id" class="badge bg-success">F-{{ c.factura_id }}</span>
                                    <span v-else class="text-muted">—</span>
                                </td>
                                <td data-label="Estado Fiscal">
                                    <span class="badge" :class="badgeFiscal(c.estado_fiscal).cls">
                                        {{ badgeFiscal(c.estado_fiscal).label }}
                                    </span>
                                </td>
                                <td>
                                    <a :href="'/pagina_cotizador/'+c.slug" class="btn btn-view btn-sm"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ════════════════════════════════════
                 TAB: Acciones (combinaciones críticas)
                 ════════════════════════════════════ -->
            <div v-show="tabActiva === 'acciones'">
                <div class="row g-3">

                    <!-- Aceptadas sin pago -->
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning bg-opacity-25 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                    <strong>Aceptadas sin pago</strong>
                                    <span class="badge bg-warning text-dark ms-2">{{ aceptadasSinPago.length }}</span>
                                </span>
                                <small class="text-muted">estatus ≥ Enviada · financiero = Pendiente</small>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-hover mb-0" v-if="aceptadasSinPago.length">
                                    <thead class="table-light">
                                        <tr><th>ID</th><th>Cliente</th><th>Total</th><th>Estatus</th><th></th></tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="c in aceptadasSinPago" :key="c.id_cotizacion">
                                            <td>#{{ c.id_cotizacion }}</td>
                                            <td>{{ c.nombre }}</td>
                                            <td>{{ moneda(c.total) }}</td>
                                            <td><span class="badge" :class="badgeEstatus(c.estatus).cls">{{ badgeEstatus(c.estatus).label }}</span></td>
                                            <td><a :href="'/pagina_cotizador/'+c.slug" class="btn btn-view btn-sm"><i class="bi bi-eye"></i></a></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p v-else class="text-muted text-center py-3 mb-0">Sin elementos</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pagadas sin facturar -->
                    <div class="col-12">
                        <div class="card border-info">
                            <div class="card-header bg-info bg-opacity-25 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-receipt text-info me-2"></i>
                                    <strong>Pagadas sin facturar</strong>
                                    <span class="badge bg-info text-dark ms-2">{{ pagadasSinFacturar.length }}</span>
                                </span>
                                <small class="text-muted">financiero = Pagado · fiscal = Sin facturar</small>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-hover mb-0" v-if="pagadasSinFacturar.length">
                                    <thead class="table-light">
                                        <tr><th>ID</th><th>Cliente</th><th>Total</th><th>Financiero</th><th></th></tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="c in pagadasSinFacturar" :key="c.id_cotizacion">
                                            <td>#{{ c.id_cotizacion }}</td>
                                            <td>{{ c.nombre }}</td>
                                            <td>{{ moneda(c.total) }}</td>
                                            <td><span class="badge" :class="badgeFinanciero(c.estado_financiero).cls">{{ badgeFinanciero(c.estado_financiero).label }}</span></td>
                                            <td><a :href="'/pagina_cotizador/'+c.slug" class="btn btn-view btn-sm"><i class="bi bi-eye"></i></a></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p v-else class="text-muted text-center py-3 mb-0">Sin elementos</p>
                            </div>
                        </div>
                    </div>

                    <!-- Facturadas sin pago -->
                    <div class="col-12">
                        <div class="card border-danger">
                            <div class="card-header bg-danger bg-opacity-25 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-cash-coin text-danger me-2"></i>
                                    <strong>Facturadas sin cobrar</strong>
                                    <span class="badge bg-danger ms-2">{{ facturadaSinPago.length }}</span>
                                </span>
                                <small class="text-muted">fiscal = Facturada · financiero ≠ Pagado</small>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-hover mb-0" v-if="facturadaSinPago.length">
                                    <thead class="table-light">
                                        <tr><th>ID</th><th>Cliente</th><th>Total</th><th>Fiscal</th><th></th></tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="c in facturadaSinPago" :key="c.id_cotizacion">
                                            <td>#{{ c.id_cotizacion }}</td>
                                            <td>{{ c.nombre }}</td>
                                            <td>{{ moneda(c.total) }}</td>
                                            <td><span class="badge" :class="badgeFiscal(c.estado_fiscal).cls">{{ badgeFiscal(c.estado_fiscal).label }}</span></td>
                                            <td><a :href="'/pagina_cotizador/'+c.slug" class="btn btn-view btn-sm"><i class="bi bi-eye"></i></a></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p v-else class="text-muted text-center py-3 mb-0">Sin elementos</p>
                            </div>
                        </div>
                    </div>

                </div><!-- /row -->
            </div>

        </div><!-- /tab-content -->
    </template>

</div><!-- /#app-cotizaciones -->

<!-- ── Modal: Crear Cotización ─────────────────────────────────────── -->
<div class="modal fade" id="modalNuevaCotizacion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog rounded-0">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title">Seleccionar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table id="modal-clientes" class="table table-bordered w-100">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo Venta</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?= esc($cliente['nombre']) ?></td>
                            <td>
                                <select class="form-select tipo-venta-select"
                                        data-cliente="<?= $cliente['id_cliente'] ?>">
                                    <option value="1">Sello completo</option>
                                    <option value="2">Mecanismos</option>
                                </select>
                            </td>
                            <td>
                                <a href="#" class="btn-my crear-cotizacion"
                                   data-cliente="<?= $cliente['id_cliente'] ?>">
                                    <i class="bi bi-check"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="my-btn-danger p-2" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // DataTable solo para el modal de clientes
    new DataTable('#modal-clientes')

    // Crear cotización desde el modal
    document.querySelectorAll('.crear-cotizacion').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault()
            const clienteId = this.getAttribute('data-cliente')
            const row       = this.closest('tr')
            const tipoVenta = row.querySelector('.tipo-venta-select').value
            window.location.href = `/nueva_cotizacion/${clienteId}?tipo_venta=${tipoVenta}`
        })
    })

    // Auto-cerrar flash después de 4s
    const flash = document.getElementById('alert-flash')
    if (flash) setTimeout(() => bootstrap.Alert.getOrCreateInstance(flash).close(), 4000)
})
</script>
<script src="<?= base_url('public/js/cotizaciones_lista.js') ?>"></script>

<?php echo $this->endSection(); ?>
