<?php echo $this->extend('Panel/panel_template') ?>
<?php echo $this->section('contenido')?>
<div class="container-fluid p-4" id="app">
    <div class="row">
        <!-- Columna principal ahora a ancho completo -->
        <div class="col-lg-12">
            <h2 class="mb-3">Flujo de Trabajo</h2>
            <div v-if="error" class="alert alert-danger">{{ error }}</div>

            <!-- Toast de notificaciones -->
            <div id="liveToast" class="toast position-fixed bottom-0 end-0 mb-4 me-4" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <span id="toastMessage">Mensaje de notificación</span>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
            
            <!-- Pestañas -->
            <ul class="nav nav-tabs" id="ordenesTab">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dibujo">
                        Dibujo <span class="badge bg-primary ms-1">{{ ordenes.dibujo.length }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#elaboracion">
                        Elaboración <span class="badge bg-warning ms-1">{{ ordenes.elaboracion.length }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#entrega">
                        Entrega <span class="badge bg-success ms-1">{{ ordenes.entrega.length }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#facturacion">
                        Para Facturar <span class="badge bg-info ms-1">{{ ordenes.facturacion.length }}</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content card rounded-0 shadow-sm border-top-0">
                
                <!-- Dibujo -->
                <div class="tab-pane fade show active" id="dibujo">
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
                                    <a :href="'/ventas/ticket/' + orden.pedido_id" target="_blank" v-if="orden.pedido_id">
                                        {{ orden.pedido_id }}
                                    </a>
                                    <span v-else>N/A</span>
                                </td>
                                <td>{{ orden.cliente_nombre }}</td>
                                <td>{{ orden.cliente_telefono }}</td>
                                <td>
                                    <img v-if="orden.imagen_path" :src="'/writable/uploads/ordenes/' + orden.imagen_path"
                                        class="img-thumbnail" style="max-width: 80px;">
                                    <span v-else class="badge bg-secondary">Sin imagen</span>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm"
                                        @click="actualizarEstado(orden.id_ot, 'Elaboracion')">
                                        A Elaboración
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Elaboración -->
                <div class="tab-pane fade" id="elaboracion">
                    <div class="mb-3" v-if="ordenes.elaboracion.length > 0">
                        <a href="<?= site_url('ordenes/descargar_ordenes') ?>" target="_blank" class="btn btn-danger me-2">
                            Descargar Órdenes (PDF)
                        </a>
                        <a href="<?= site_url('ordenes/pedidos-pendientes') ?>" class="btn btn-warning" target="_blank">
                            Generar Etiquetas
                        </a>
                    </div>

                    <table class="table table-bordered">
                        <tbody>
                            <tr v-for="orden in ordenes.elaboracion" :key="orden.id_ot">
                                <td>{{ orden.id_ot }}</td>
                                <td>{{ orden.cliente_nombre }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm"
                                        @click="actualizarEstado(orden.id_ot, 'Entrega')">
                                        A Entrega
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Entrega -->
                <div class="tab-pane fade" id="entrega">
                    <table class="table table-bordered">
                        <tbody>
                            <tr v-for="orden in ordenes.entrega" :key="orden.id_ot">
                                <td>{{ orden.id_ot }}</td>
                                <td>
                                    <button class="btn btn-success btn-sm"
                                        @click="actualizarEstado(orden.id_ot, 'Entregado')">
                                        Entregado
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Facturación -->
                <div class="tab-pane fade" id="facturacion">
                    <table class="table table-bordered">
                        <tbody>
                            <tr v-for="orden in ordenes.facturacion" :key="orden.id_ot">
                                <td>{{ orden.id_ot }}</td>
                                <td>
                                    <button class="btn btn-success btn-sm"
                                        @click="marcarComoFacturado(orden.id_ot)">
                                        Facturado
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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