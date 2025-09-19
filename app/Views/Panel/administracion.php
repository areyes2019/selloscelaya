<?php echo $this->extend('Panel/panel_template') ?>
<?php echo $this->section('contenido')?>
<div class="container-fluid p-4" id="app">
    <div class="row">
        <!-- Columna principal (contenido existente) -->
        <div class="col-lg-9">
            <h2 class="mb-3">Flujo de Trabajo</h2>
            <div v-if="error" class="alert alert-danger">{{ error }}</div>

            <!-- Toast de notificaciones -->
            <div id="liveToast" class="toast position-fixed bottom-0 end-0 mb-4 me-4" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <span id="toastMessage">Mensaje de notificación</span>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
            
            <!-- Pestañas y contenido -->
            <ul class="nav nav-tabs" id="ordenesTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="dibujo-tab" data-bs-toggle="tab" data-bs-target="#dibujo" type="button" role="tab">
                        Dibujo <span class="badge bg-primary ms-1">{{ ordenes.dibujo.length }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="elaboracion-tab" data-bs-toggle="tab" data-bs-target="#elaboracion" type="button" role="tab">
                        Elaboración <span class="badge bg-warning ms-1">{{ ordenes.elaboracion.length }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="entrega-tab" data-bs-toggle="tab" data-bs-target="#entrega" type="button" role="tab">
                        Entrega <span class="badge bg-success ms-1">{{ ordenes.entrega.length }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="facturacion-tab" data-bs-toggle="tab" data-bs-target="#facturacion" type="button" role="tab">
                        Para Facturar <span class="badge bg-info ms-1">{{ ordenes.facturacion.length }}</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content card rounded-0 shadow-sm border-top-0" id="ordenesTabContent">
                <!-- Pestaña Dibujo -->
                <div class="tab-pane fade show active" id="dibujo" role="tabpanel">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th># Orden</th>
                                <th>No. Ped.</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Img</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="orden in ordenes.dibujo" :key="orden.id_ot">
                                <td>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#ordenModal" @click="cargarDetalleOrden(orden.id_ot)">
                                        {{ orden.id_ot }}
                                    </a>
                                </td>
                                <td>
                                    <a :href="'/ventas/ticket/' + orden.pedido_id" 
                                       target="_blank" 
                                       class="text-decoration-none" 
                                       v-if="orden.pedido_id">
                                        {{ orden.pedido_id }}
                                    </a>
                                    <span v-else class="text-muted">N/A</span>
                                </td>
                                <td>{{ orden.cliente_nombre }}</td>
                                <td>{{ orden.cliente_telefono }}</td>
                                <td>
                                    <a v-if="orden.imagen_path" :href="'/writable/uploads/ordenes/' + orden.imagen_path" target="_blank">
                                        <img :src="'/writable/uploads/ordenes/' + orden.imagen_path" class="img-thumbnail" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                    </a>
                                    <span v-else class="badge bg-secondary">Sin imagen</span>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" @click="actualizarEstado(orden.id_ot, 'Elaboracion')">
                                        A Elaboración
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pestaña Elaboración -->
                <div class="tab-pane fade" id="elaboracion" role="tabpanel">
                    <!-- Mostrar botones solo si hay órdenes en elaboración -->
                    <div class="mb-3" v-if="ordenes.elaboracion.length > 0">
                        <a href="<?= site_url('ordenes/descargar_ordenes') ?>" target="_blank" class="btn btn-danger rounded-0 me-2">
                            <i class="bi bi-file-earmark-pdf"></i> Descargar Órdenes (PDF)
                        </a>
                        <a href="<?= site_url('ordenes/pedidos-pendientes') ?>" class="btn btn-warning rounded-0" target="_blank">
                            <i class="fas fa-tags"></i> Generar Etiquetas Pendientes (PDF)
                        </a>
                    </div>

                    <!-- Tabla de órdenes -->
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th># Orden</th>
                                <th>No. Ped.</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Img</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="orden in ordenes.elaboracion" :key="orden.id_ot">
                                <td>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#ordenModal" @click="cargarDetalleOrden(orden.id_ot)">
                                        {{ orden.id_ot }}
                                    </a>
                                </td>
                                <td>
                                    <a :href="'/ventas/ticket/' + orden.pedido_id" 
                                       target="_blank" 
                                       class="text-decoration-none" 
                                       v-if="orden.pedido_id">
                                        {{ orden.pedido_id }}
                                    </a>
                                    <span v-else class="text-muted">N/A</span>
                                </td>
                                <td>{{ orden.cliente_nombre }}</td>
                                <td>{{ orden.cliente_telefono }}</td>
                                <td>
                                    <a v-if="orden.imagen_path" :href="'/writable/uploads/ordenes/' + orden.imagen_path" target="_blank">
                                        <img :src="'/writable/uploads/ordenes/' + orden.imagen_path" class="img-thumbnail" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                    </a>
                                    <span v-else class="badge bg-secondary">Sin imagen</span>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm" @click="actualizarEstado(orden.id_ot, 'Entrega')">
                                        A Entrega
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pestaña Entrega -->
                <div class="tab-pane fade" id="entrega" role="tabpanel">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th># Orden</th>
                                <th>No. Ped.</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Img</th>
                                <th>Status</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="orden in ordenes.entrega" :key="orden.id_ot">
                                <td>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#ordenModal" @click="cargarDetalleOrden(orden.id_ot)">
                                        {{ orden.id_ot }}
                                    </a>
                                </td>
                                <td>
                                    <a :href="'/ventas/ticket/' + orden.pedido_id" 
                                       target="_blank" 
                                       class="text-decoration-none" 
                                       v-if="orden.pedido_id">
                                        {{ orden.pedido_id }}
                                    </a>
                                    <span v-else class="text-muted">N/A</span>
                                </td>
                                <td>{{ orden.cliente_nombre }}</td>
                                <td>{{ orden.cliente_telefono }}</td>
                                <td>
                                    <a v-if="orden.imagen_path" :href="'/writable/uploads/ordenes/' + orden.imagen_path" target="_blank">
                                        <img :src="'/writable/uploads/ordenes/' + orden.imagen_path" class="img-thumbnail" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                    </a>
                                    <span v-else class="badge bg-secondary">Sin imagen</span>
                                </td>
                                <td>
                                    <span class="badge" :class="{
                                        'bg-success': orden.status.toLowerCase() === 'entregado',
                                        'bg-info': orden.status.toLowerCase() === 'entrega'
                                    }">
                                        {{ orden.status }}
                                    </span>
                                </td>
                                <td>
                                    <!-- Botón Entregado (visible siempre que status sea 'entrega') -->
                                    <button v-if="orden.status.toLowerCase() === 'entrega'" 
                                            class="btn btn-success btn-sm me-1" 
                                            @click="actualizarEstado(orden.id_ot, 'Entregado')">
                                        Entregado
                                    </button>
                                    
                                    <!-- Botón Pagado (solo visible cuando status es 'entregado' y no está pagado) -->
                                    <button v-if="orden.status.toLowerCase() === 'entregado' && orden.estado_pedido !== 'pagado'" 
                                            class="btn btn-primary btn-sm me-1" 
                                            @click="confirmarPago(orden.pedido_id)">
                                        Pagado
                                    </button>
                                    
                                    <!-- Botón A Facturar (solo visible cuando está pagado) -->
                                    <button v-if="orden.estado_pedido === 'pagado'" 
                                            class="btn btn-info btn-sm" 
                                            @click="actualizarEstado(orden.id_ot, 'Facturacion')">
                                        A Facturar
                                    </button>
                                    
                                    <!-- Botón Eliminar (para órdenes ya entregadas) -->
                                    <button v-if="orden.status.toLowerCase() === 'entregado' && orden.estado_pedido === 'pagado'"
                                            class="btn btn-danger btn-sm ms-1"
                                            @click="eliminarOrden(orden.id_ot)">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pestaña Facturación -->
                <div class="tab-pane fade" id="facturacion" role="tabpanel">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th># Orden</th>
                                <th>No. Ped.</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Img</th>
                                <th>Status</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="orden in ordenes.facturacion" :key="orden.id_ot">
                                <td>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#ordenModal" @click="cargarDetalleOrden(orden.id_ot)">
                                        {{ orden.id_ot }}
                                    </a>
                                </td>
                                <td>
                                    <a :href="'/ventas/ticket/' + orden.pedido_id" 
                                       target="_blank" 
                                       class="text-decoration-none" 
                                       v-if="orden.pedido_id">
                                        {{ orden.pedido_id }}
                                    </a>
                                    <span v-else class="text-muted">N/A</span>
                                </td>
                                <td>{{ orden.cliente_nombre }}</td>
                                <td>{{ orden.cliente_telefono }}</td>
                                <td>
                                    <a v-if="orden.imagen_path" :href="'/writable/uploads/ordenes/' + orden.imagen_path" target="_blank">
                                        <img :src="'/writable/uploads/ordenes/' + orden.imagen_path" class="img-thumbnail" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                    </a>
                                    <span v-else class="badge bg-secondary">Sin imagen</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ orden.status }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-success btn-sm" @click="marcarComoFacturado(orden.id_ot, ordenes.facturacion.indexOf(orden))">
                                        <i class="bi bi-file-earmark-check"></i> Facturado
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Modal para seleccionar cuenta bancaria -->
            <div class="modal fade" id="cuentaModal" tabindex="-1" aria-labelledby="cuentaModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cuentaModalLabel">Seleccionar cuenta bancaria</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div v-if="cargandoCuentas" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                            <div v-else>
                                <div class="mb-3">
                                    <label for="cuentaSelect" class="form-label">Seleccione una cuenta:</label>
                                    <select class="form-select" id="cuentaSelect" v-model="cuentaSeleccionada">
                                        <option v-for="cuenta in cuentasBancarias" :value="cuenta.id_cuenta">
                                            {{ cuenta.banco }} - {{ cuenta.cuenta }} (Saldo: ${{ cuenta.saldo }})
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" @click="procesarPago">Confirmar Pago</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal para detalles de la orden -->
            <div class="modal fade" id="ordenModal" tabindex="-1" aria-labelledby="ordenModalLabel">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ordenModalLabel">Detalles de la Orden #{{ ordenSeleccionada.id_ot }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div v-if="cargandoDetalle" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                            <div v-else>
                                <!-- Aquí irá el contenido del detalle de la orden -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Información del Cliente</h6>
                                        <p><strong>Nombre:</strong> {{ ordenSeleccionada.cliente_nombre }}</p>
                                        <p><strong>Teléfono:</strong> {{ ordenSeleccionada.cliente_telefono }}</p>
                                        <p><strong>Color de tinta:</strong> {{ ordenSeleccionada.color_tinta}}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Detalles de la Orden</h6>
                                        <p><strong>Observaciones</strong></p>
                                        <p>{{ordenSeleccionada.observaciones}}</p>
                                        <p><strong>Modelo</strong></p>
                                        <p>{{ordenSeleccionada.modelo}}</p>
                                        <p><strong>Estado:</strong> 
                                            <span class="badge" :class="{
                                                'bg-primary': ordenSeleccionada.status === 'Dibujo',
                                                'bg-warning': ordenSeleccionada.status === 'Elaboracion',
                                                'bg-success': ordenSeleccionada.status === 'Entregado',
                                                'bg-info': ordenSeleccionada.status === 'Facturacion'
                                            }">
                                                {{ ordenSeleccionada.status }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>Imagen de referencia</h6>
                                        <div v-if="ordenSeleccionada.imagen_path" class="text-center">
                                            <img :src="'/writable/uploads/ordenes/' + ordenSeleccionada.imagen_path" class="img-fluid" style="max-height: 300px;">
                                        </div>
                                        <div v-else class="alert alert-warning">
                                            No hay imagen adjunta
                                        </div>
                                    </div>
                                </div>
                                <!-- Más detalles pueden agregarse aquí -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            
                            <!-- Botón de Editar -->
                            <!-- Botón de Editar como enlace -->
                            <a :href="'/ordenes/edit/' + ordenSeleccionada.id_ot" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            
                            <button type="button" class="btn btn-primary" @click="imprimirOrden(ordenSeleccionada.id_ot)">
                                <i class="bi bi-printer"></i> Imprimir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna lateral derecha para tareas -->
        <div class="col-lg-3">
            <div class="sticky-top pt-3" style="top: 20px;">
                <!-- Panel de tareas estilo checklist -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0">
                        <h6 class="mb-0">📅 {{ fechaHoy }}</h6>
                    </div>
                    <div class="card-body p-3">
                        <!-- Formulario para nueva tarea con campo de teléfono -->
                        <div class="mb-3">
                            <input 
                                type="text" 
                                class="form-control form-control-sm mb-2 rounded-pill" 
                                placeholder="Nueva tarea..."
                                v-model="nuevaTarea"
                                @keyup.enter="agregarTarea"
                            >
                            <input 
                                type="text" 
                                class="form-control form-control-sm rounded-pill" 
                                placeholder="Teléfono..."
                                v-model="nuevoTelefono"
                                @keyup.enter="agregarTarea"
                            >
                            <div class="input-group mb-2">
                                <input 
                                    type="date" 
                                    class="form-control form-control-sm rounded-start" 
                                    v-model="nuevaFecha"
                                    @change="actualizarFiltroPorFecha"
                                >
                                <button 
                                    class="btn btn-sm btn-outline-secondary" 
                                    type="button"
                                    @click="establecerFechaHoy"
                                    title="Usar fecha de hoy"
                                >
                                    <i class="bi bi-calendar-day"></i>
                                </button>
                                <button 
                                    class="btn btn-sm btn-outline-secondary" 
                                    type="button"
                                    @click="establecerFechaManana"
                                    title="Usar fecha de mañana"
                                >
                                    <i class="bi bi-calendar-plus"></i>
                                </button>
                            </div>
                        </div>
                        <button 
                            class="btn btn-sm btn-outline-primary w-100 rounded-pill mb-3"
                            @click="agregarTarea"
                        >
                            Agregar Tarea
                        </button>
                        
                        <div class="task-list">
                             <div 
                                class="task-item d-flex align-items-center py-2 border-bottom"
                                v-for="(tarea, index) in tareasFiltradas"
                                :key="index"
                            >
                                <input 
                                    type="checkbox" 
                                    class="form-check-input me-2"
                                    v-model="tarea.completada"
                                    @change="actualizarTarea(tarea)"
                                >
                                <div class="flex-grow-1">
                                    <div class="fw-bold small">{{ tarea.titulo }}</div>
                                    <div class="text-muted x-small" v-if="tarea.descripcion">{{ tarea.descripcion }}</div>
                                    <div class="d-flex align-items-center mt-1">
                                        <span 
                                            class="badge bg-light text-dark me-2"
                                            :class="{
                                                'bg-danger text-white': tarea.prioridad === 'alta',
                                                'bg-warning': tarea.prioridad === 'media',
                                                'bg-info text-white': tarea.prioridad === 'baja'
                                            }"
                                            v-if="tarea.prioridad"
                                        >
                                            {{ tarea.prioridad }}
                                        </span>
                                        <span class="text-muted x-small">{{ tarea.telefono }}</span>
                                    </div>
                                </div>
                                <button 
                                    class="btn btn-sm btn-link text-danger"
                                    @click="eliminarTarea(index)"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Filtros (se mantienen igual) -->
                        <div class="d-flex justify-content-between mt-3">
                            <button class="btn btn-sm btn-outline-secondary" @click="filtrarTareas('hoy')" :class="{ 'active': filtroTareas === 'hoy' }">
                                Hoy
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" @click="filtrarTareas('mañana')" :class="{ 'active': filtroTareas === 'mañana' }">
                                Mañana
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" @click="filtrarTareas('todas')" :class="{ 'active': filtroTareas === 'todas' }">
                                Todas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.csrfToken = "<?= csrf_token() ?>";
    window.csrfHash = "<?= csrf_hash() ?>";
</script>
<script src="<?php echo base_url('public/js/admin.js'); ?>"></script>
<?php echo $this->endSection()?>