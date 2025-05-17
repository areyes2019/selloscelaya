<?php echo $this->extend('Panel/panel_template')?>
<?= $this->section('contenido') ?>
<div class="container mt-4">
    <h1 class="mb-4">Gestión de Categorías</h1>
    
    <!-- Mostrar mensajes de éxito/error -->
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if(session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <?= implode('<br>', session()->getFlashdata('errors')) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Lista de Categorías</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-categoria">
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
                        <?php foreach($categorias as $categoria): ?>
                        <tr id="categoria-<?= $categoria['id_categoria'] ?>">
                            <td><?= $categoria['id_categoria'] ?></td>
                            <td><?= esc($categoria['nombre']) ?></td>
                            <td>
                                <a href="<?= base_url('categorias/edit/'.$categoria['id_categoria']) ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <a href="<?= base_url('categorias/delete/'.$categoria['id_categoria']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
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
                <h5 class="modal-title" id="modal-title">
                    <?= isset($categoria_editar) ? 'Editar Categoría' : 'Nueva Categoría' ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= isset($categoria_editar) ? base_url('categorias/update/'.$categoria_editar['id_categoria']) : base_url('categorias/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="id_categoria" value="<?= isset($categoria_editar) ? $categoria_editar['id_categoria'] : '' ?>">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control <?= session()->getFlashdata('error_nombre') ? 'is-invalid' : '' ?>" 
                               id="nombre" name="nombre" required 
                               value="<?= isset($categoria_editar) ? esc($categoria_editar['nombre']) : old('nombre') ?>">
                        <?php if(session()->getFlashdata('error_nombre')): ?>
                            <div class="invalid-feedback"><?= session()->getFlashdata('error_nombre') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para abrir el modal automáticamente si hay errores o en edición -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(session()->getFlashdata('errors') || isset($categoria_editar)): ?>
        var modal = new bootstrap.Modal(document.getElementById('modal-categoria'));
        modal.show();
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>