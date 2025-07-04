<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container-fluid p-4" id="app">
    <h2 class="mb-3">Flujo de Trabajo</h2>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    
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
                        <td>{{ orden.cliente_nombre }}</td>
                        <td>{{ orden.cliente_telefono }}</td>
                        <td>
                            <a v-if="orden.imagen_path" :href="'/writable/uploads/ordenes/' + orden.imagen_path" target="_blank">
                                <img :src="'/writable/uploads/ordenes/' + orden.imagen_path" class="img-thumbnail" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                            </a>
                            <span v-else class="badge bg-secondary">Sin imagen</span>
                        </td>
                        <td>
                            <select class="form-select form-select-sm" @change="actualizarEstado(orden.id_ot, $event.target.value)" style="width: auto; display: inline-block;">
                                <option value="Dibujo" selected>Dibujo</option>
                                <option value="Elaboracion">Elaboración</option>
                            </select>
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
                        <td>{{ orden.cliente_nombre }}</td>
                        <td>{{ orden.cliente_telefono }}</td>
                        <td>
                            <a v-if="orden.imagen_path" :href="'/writable/uploads/ordenes/' + orden.imagen_path" target="_blank">
                                <img :src="'/writable/uploads/ordenes/' + orden.imagen_path" class="img-thumbnail" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                            </a>
                            <span v-else class="badge bg-secondary">Sin imagen</span>
                        </td>
                        <td>
                            <select class="form-select form-select-sm" @change="actualizarEstado(orden.id_ot, $event.target.value)" style="width: auto; display: inline-block;">
                                <option value="Dibujo">Dibujo</option>
                                <option value="Entrega">Entrega</option>
                            </select>
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
                            <select v-if="orden.status.toLowerCase() === 'entrega'" class="form-select form-select-sm" @change="actualizarEstado(orden.id_ot, $event.target.value)" style="width: auto; display: inline-block;">
                                <option value="Elaboracion">Elaboración</option>
                                <option value="Entregado">Marcar como Entregado</option>
                                <option value="Facturacion">Facturar</option>
                            </select>
                            <button v-else class="btn btn-danger btn-sm rounded-0" @click="eliminarOrden(orden.id_ot)">
                                <i class="bi bi-trash3"></i>
                            </button>
                            <div v-if="orden.estado_pedido !== 'pagado'">
                                <button @click="confirmarPago(orden.pedido_id)" class="btn btn-success btn-sm rounded-0">
                                    <i class="bi bi-cash"></i>
                                </button>
                            </div>
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
                            <select class="form-select form-select-sm" @change="actualizarEstado(orden.id_ot, $event.target.value)" style="width: auto; display: inline-block;">
                                <option value="">Selecconar...</option>
                                <option value="Entregado">Marcar como Entregado</option>
                            </select>
                            <button class="btn btn-success btn-sm rounded-0 ms-2" @click="marcarComoFacturado(orden.id_ot)">
                                <i class="bi bi-file-earmark-check"></i> Facturado
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
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
                    <button type="button" class="btn btn-primary" @click="imprimirOrden(ordenSeleccionada.id_ot)">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('public/js/admin.js'); ?>"></script>
<?php echo $this->endSection()?>