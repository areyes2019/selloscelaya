<?php echo $this->extend('Panel/panel_template')?>
<?= $this->section('contenido') ?>
<div class="container mt-4">
    <h1 class="mb-4">Editar Categoría</h1>
    
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
        <div class="card-header">
            <h5 class="mb-0">Formulario de Edición</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('categorias/update/'.$categoria['id_categoria']) ?>" method="post">
                <?= csrf_field() ?>
                
                <input type="hidden" name="id_categoria" value="<?= $categoria['id_categoria'] ?>">
                
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de la Categoría</label>
                    <input type="text" class="form-control <?= session()->getFlashdata('error_nombre') ? 'is-invalid' : '' ?>" 
                           id="nombre" name="nombre" required 
                           value="<?= old('nombre', $categoria['nombre']) ?>">
                    <?php if(session()->getFlashdata('error_nombre')): ?>
                        <div class="invalid-feedback"><?= session()->getFlashdata('error_nombre') ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('categorias') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al listado
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>