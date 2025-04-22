<?php echo $this->extend('Panel/panel_template')?>
<?= $this->section('contenido') ?>
<div class="container mt-4" id="app">
    <h1 class="mb-4">Gestión de Categorías</h1>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Lista de Categorías</h5>
            <button class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Categoría
            </button>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="tabla-categorias">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Datos ficticios -->
                        <tr id="categoria-1" v-for="data in categorias">
                            <td>{{data.id_categoria}}</td>
                            <td>{{data.nombre}}</td>
                            <td>
                                <button class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" @click = "eliminarCategoria(data.id_categoria)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar -->
<div class="modal fade" id="modal-categoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-categoria">
                    <input type="hidden" id="id_categoria" name="id_categoria">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                        <div class="invalid-feedback" id="nombre-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar">Guardar</button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('public/js/categorias.js'); ?>"></script>
<script src="<?php echo base_url('public/js/notify.js'); ?>"></script>
<?= $this->endSection() ?>