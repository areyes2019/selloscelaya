<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container-fluid p-4" id="app">
    <h2 class="mb-3">Flujo de Trabajo</h2>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- Pestañas y contenido (similar a tu HTML original pero con directivas Vue) -->
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
    </ul>

    <div class="tab-content card rounded-0 shadow-sm border-top-0" id="ordenesTabContent">
        <!-- Pestaña Dibujo -->
        <div class="tab-pane fade show active" id="dibujo" role="tabpanel">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Img</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="orden in ordenes.dibujo" :key="orden.id_ot">
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
                                <option value="Entrega">Entrega</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pestaña Elaboración -->
        <div class="tab-pane fade" id="elaboracion" role="tabpanel">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Img</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="orden in ordenes.elaboracion" :key="orden.id_ot">
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
                                <option value="Elaboracion" selected>Elaboración</option>
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
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Img</th>
                        <th>Status</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="orden in ordenes.entrega" :key="orden.id_ot">
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
                                <option value="Entrega" selected>Entrega</option>
                                <option value="Entregado">Marcar como Entregado</option>
                            </select>
                            <button v-else class="btn btn-danger btn-sm rounded-0" @click="eliminarOrden(orden.id_ot)">
                                <i class="bi bi-trash3"></i>
                            </button>
                            <div v-if="orden.estado_pedido !== 'Pagado'">
                              <button @click="confirmarPago(orden.pedido_id)" class="btn btn-success btn-sm rounded-0">
                                <i class="bi bi-cash"></i>
                              </button>
                            </div>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="<?php echo base_url('public/js/admin.js'); ?>"></script>
<?php echo $this->endSection()?>