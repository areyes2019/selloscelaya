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
                            <button class="btn btn-primary btn-sm" @click="actualizarEstado(orden.id_ot, 'Elaboracion')">
                                A Producción
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
                            <button class="btn btn-success btn-sm" @click="actualizarEstado(orden.id_ot, 'Entrega')">
                                <i class="bi bi-check-circle me-1"></i> Elaborado
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
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Img</th>
                        <th>Status</th>
                        <th>Acciones</th>
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
                                'bg-info': orden.status.toLowerCase() === 'entrega',
                                'bg-warning': orden.status.toLowerCase() === 'facturacion'
                            }">
                                {{ orden.status }}
                            </span>
                            <br>
                            <small class="text-muted" v-if="orden.estado_pedido">
                                {{ orden.estado_pedido === 'pagado' ? 'Pagado' : 'Pendiente de pago' }}
                            </small>
                        </td>
                        <td>
                            <!-- Estado: Entrega (pendiente de entrega) -->
                            <template v-if="orden.status.toLowerCase() === 'entrega'">
                                <!-- Botón Pagado (solo visible si no está pagado) -->
                                <button v-if="orden.estado_pedido !== 'pagado'"
                                        @click="confirmarPago(orden.pedido_id)" 
                                        class="btn btn-success btn-sm me-1">
                                    <i class="bi bi-cash"></i> Pagado
                                </button>
                                
                                <!-- Botón Entregado (solo visible si está pagado) -->
                                <button v-if="orden.estado_pedido === 'pagado'"
                                        @click="actualizarEstado(orden.id_ot, 'Entregado')" 
                                        class="btn btn-primary btn-sm me-1">
                                    <i class="bi bi-check-circle"></i> Entregado
                                </button>
                            </template>
                            
                            <!-- Estado: Entregado -->
                            <template v-else-if="orden.status.toLowerCase() === 'entregado'">
                                <button @click="actualizarEstado(orden.id_ot, 'Facturacion')" 
                                        class="btn btn-info btn-sm me-1">
                                    <i class="bi bi-receipt"></i> Facturar
                                </button>
                                <button @click="eliminarOrden(orden.id_ot)" 
                                        class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </template>
                            
                            <!-- Estado: Facturación -->
                            <span v-else class="text-muted">En proceso de facturación</span>
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
                <!-- Div oculto para copiar (solo para etiquetas) -->
                <div id="etiquetaOrden" style="position: absolute; left: -9999px; width: 80mm; padding: 10px; background: white; border: 1px solid #000; font-family: Arial, sans-serif;">
                    <h4 style="text-align: center; margin: 5px 0; font-size: 18px;">PEDIDO #{{ ordenSeleccionada.pedido_id }}</h4>
                    <hr style="margin: 5px 0; border-color: #000;">
                    
                    <p style="margin: 4px 0; font-size: 14px;"><strong>Cliente:</strong> {{ ordenSeleccionada.pedido_cliente }}</p>
                    <p style="margin: 4px 0; font-size: 14px;"><strong>Teléfono:</strong> {{ ordenSeleccionada.cliente_telefono || 'N/A' }}</p>
                    <p style="margin: 4px 0; font-size: 14px;"><strong>Modelo:</strong> {{ ordenSeleccionada.modelo || 'N/A' }}</p>
                    
                    <hr style="margin: 8px 0; border-color: #000;">
                    
                    <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                        <p style="margin: 4px 0; font-size: 14px; width: 100%;"><strong>Total:</strong> ${{ ordenSeleccionada.total }}</p>
                        <p style="margin: 4px 0; font-size: 14px;"><strong>Anticipo:</strong> ${{ ordenSeleccionada.anticipo }}</p>
                        <p style="margin: 4px 0; font-size: 14px;"><strong>Saldo:</strong> 
                            <span :style="{color: ordenSeleccionada.saldo > 0 ? 'red' : 'green'}">
                                ${{ ordenSeleccionada.saldo_calculado }}
                            </span>
                        </p>
                    </div>
                    
                    <div style="text-align: center; margin-top: 10px;">
                        <p style="margin: 4px 0; font-size: 12px; color: #555;">
                            {{ new Date().toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" @click="imprimirOrden(ordenSeleccionada.id_ot)">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>
                    <button type="button" class="btn btn-info" @click="copiarEtiqueta" v-if="ordenSeleccionada.id_ot">
                        <i class="bi bi-tag"></i> Copiar Etiqueta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('public/js/admin.js'); ?>"></script>
<?php echo $this->endSection()?>