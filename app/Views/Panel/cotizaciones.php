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
<div id="app-cotizaciones"
     class="container mt-3"
     data-clientes='<?= json_encode($clientes, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>

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
            <button v-for="f in [{val:'todos',label:'Todas'},{val:'hoy',label:'Hoy'},{val:'semana',label:'Esta semana'},{val:'mes',label:'Este mes'}]"
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

        <div class="border rounded p-3 bg-white">

            <div>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button v-for="f in [{val:'todos',label:'Todos'},{val:'borrador',label:'Borrador'},{val:'anticipo',label:'Anticipo'},{val:'pagado',label:'Pagado'},{val:'facturada',label:'Facturada'}]" :key="f.val"
                        class="btn btn-sm"
                        :class="filtroEstado===f.val ? 'btn-dark' : 'btn-outline-secondary'"
                        @click="filtroEstado=f.val">
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
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="listaComercial.length === 0">
                                <td colspan="6" class="text-center text-muted py-4">Sin resultados</td>
                            </tr>
                            <tr v-for="c in listaComercialPaginada" :key="c.id_cotizacion">
                                <td data-label="ID">#{{ c.id_cotizacion }}</td>
                                <td data-label="Cliente">{{ c.nombre }}</td>
                                <td data-label="Email">{{ c.correo || '—' }}</td>
                                <td data-label="Total">{{ moneda(c.total) }}</td>
                                <td data-label="Estado">
                                    <span class="badge" :class="badgeComercial(c.estado_comercial).cls">
                                        {{ badgeComercial(c.estado_comercial).label }}
                                    </span>
                                </td>
                                <td data-label="Acciones">
                                    <a :href="'/pagina_cotizador/'+c.slug" class="btn btn-view btn-sm"><i class="bi bi-eye"></i></a>
                                    <a :href="'/descargar_cotizacion/'+c.id_cotizacion" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i></a>
                                    <button class="btn btn-sm btn-outline-primary" @click="abrirModalClonar(c)" title="Clonar cotización"><i class="bi bi-copy"></i></button>
                                    <button v-if="c.telefono" class="btn btn-sm text-white" style="background:#25D366; border-color:#25D366;" title="Enviar WhatsApp" @click="abrirWA(c)"><i class="bi bi-whatsapp"></i></button>
                                    <button class="btn btn-delete btn-sm" @click="eliminar(c.id_cotizacion)"><i class="bi bi-trash3"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                    <small class="text-muted">{{ infoRango }}</small>
                    <nav v-if="totalPaginas > 1" aria-label="Paginación cotizaciones">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item" :class="{ disabled: paginaActual === 1 }">
                                <button class="page-link" @click="cambiarPagina(paginaActual - 1)" aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </button>
                            </li>
                            <li v-for="p in paginasVisibles" :key="p"
                                class="page-item"
                                :class="{ active: p === paginaActual, disabled: p === '...' }">
                                <button class="page-link" @click="p !== '...' && cambiarPagina(p)">{{ p }}</button>
                            </li>
                            <li class="page-item" :class="{ disabled: paginaActual === totalPaginas }">
                                <button class="page-link" @click="cambiarPagina(paginaActual + 1)" aria-label="Siguiente">
                                    <span aria-hidden="true">&raquo;</span>
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

        </div>
    </template>

    <!-- ── Modal: Clonar Cotización ──────────────────────────────────────── -->
    <div class="modal fade" id="modalClonar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg rounded-0">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-copy me-2"></i>Clonar Cotización #{{ cotizacionAClonar?.id_cotizacion }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Original: <strong>{{ cotizacionAClonar?.nombre }}</strong>
                    </p>

                    <!-- Campo de búsqueda de clientes -->
                    <label class="form-label fw-semibold">Cliente destino</label>
                    <div class="input-group mb-3">
                        <input v-model="busquedaCliente" type="text" class="form-control"
                               placeholder="Buscar cliente por nombre…">
                        <button v-if="busquedaCliente" class="btn btn-outline-secondary" @click="busquedaCliente=''">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>

                    <!-- Tabla de clientes con paginación -->
                    <div v-if="clientesFiltrados.length === 0" class="text-center text-muted py-3">
                        <i class="bi bi-people me-1"></i> No se encontraron clientes
                    </div>
                    <div v-else class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                        <table class="table table-sm table-hover table-bordered mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width:50px">#</th>
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th style="width:90px">Seleccionar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cli in clientesPaginados" :key="cli.id_cliente"
                                    :class="{ 'table-primary': clienteClonId == cli.id_cliente }"
                                    @dblclick="seleccionarCliente(cli)">
                                    <td>{{ cli.id_cliente }}</td>
                                    <td>{{ cli.nombre }}</td>
                                    <td>{{ cli.telefono || '—' }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm"
                                                :class="clienteClonId == cli.id_cliente ? 'btn-success' : 'btn-outline-success'"
                                                @click="seleccionarCliente(cli)">
                                            <i v-if="clienteClonId == cli.id_cliente" class="bi bi-check-circle-fill"></i>
                                            <i v-else class="bi bi-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación de clientes -->
                    <div v-if="totalPagClientes > 1" class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted">
                            {{ infoRangoClientes }}
                        </small>
                        <nav aria-label="Paginación clientes">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item" :class="{ disabled: pagCliActual === 1 }">
                                    <button class="page-link" @click="cambiarPagCli(pagCliActual - 1)">&laquo;</button>
                                </li>
                                <li v-for="p in pagCliVisibles" :key="p"
                                    class="page-item"
                                    :class="{ active: p === pagCliActual, disabled: p === '...' }">
                                    <button class="page-link" @click="p !== '...' && cambiarPagCli(p)">{{ p }}</button>
                                </li>
                                <li class="page-item" :class="{ disabled: pagCliActual === totalPagClientes }">
                                    <button class="page-link" @click="cambiarPagCli(pagCliActual + 1)">&raquo;</button>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <!-- Cliente seleccionado -->
                    <div v-if="clienteSeleccionado" class="alert alert-success mt-3 mb-0 py-2">
                        <i class="bi bi-person-check-fill me-1"></i>
                        Cliente seleccionado: <strong>{{ clienteSeleccionado.nombre }}</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="my-btn-danger p-2" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn-my" @click="confirmarClonar" :disabled="!clienteClonId || clonando">
                        <span v-if="clonando" class="spinner-border spinner-border-sm me-1"></span>
                        <i v-else class="bi bi-copy me-1"></i>
                        Replicar
                    </button>
                </div>
            </div>
        </div>
    </div>

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
    // DataTable para el modal de clientes (con searchable para mejor UX)
    new DataTable('#modal-clientes', {
        pageLength: 10,
        language: {
            search: 'Buscar:',
            lengthMenu: 'Mostrar _MENU_',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ clientes',
            infoEmpty: 'Sin resultados',
            infoFiltered: '(filtrados de _MAX_ total)',
        }
    })

    // ── Delegación de eventos para crear cotización ──
    // DataTable recrea las filas al paginar/buscar, por lo que NO podemos
    // usar querySelectorAll + addEventListener.
    // En su lugar, escuchamos clicks en el <tbody> de la tabla (delegación).
    const tabla = document.querySelector('#modal-clientes tbody')
    if (tabla) {
        tabla.addEventListener('click', function (e) {
            const btn = e.target.closest('.crear-cotizacion')
            if (!btn) return

            e.preventDefault()
            const clienteId = btn.getAttribute('data-cliente')
            const row       = btn.closest('tr')
            const tipoVenta = row.querySelector('.tipo-venta-select').value
            window.location.href = `/nueva_cotizacion/${clienteId}?tipo_venta=${tipoVenta}`
        })
    }

    // Auto-cerrar flash después de 4s
    const flash = document.getElementById('alert-flash')
    if (flash) setTimeout(() => bootstrap.Alert.getOrCreateInstance(flash).close(), 4000)
})
</script>
<script src="<?= base_url('public/js/whatsapp.js') ?>"></script>
<script src="<?= base_url('public/js/cotizaciones_lista.js') ?>?v=<?= filemtime(ROOTPATH . 'public/js/cotizaciones_lista.js') ?>"></script>

<?php echo $this->endSection(); ?>
