<?= $this->extend('Panel/panel_template') ?>
<?= $this->section('contenido') ?>
<div class="container mt-3">
    <h1>Nuevo Artículo</h1>
    
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?= session('error') ?></div>
    <?php endif ?>
    
    <?php if(session('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach(session('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>
    
    <form action="<?= base_url('nuevo_articulo') ?>" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nombre">Nombre*</label>
                    <input type="text" name="nombre" class="form-control rounded-0" 
                           value="<?= old('nombre') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="modelo">Modelo</label>
                    <input type="text" name="modelo" class="form-control rounded-0" 
                           value="<?= old('modelo') ?>">
                </div>
                
                <div class="form-group">
                    <label for="precio_prov">Precio Proveedor*</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" step="0.01" name="precio_prov" id="precio_prov" 
                               class="form-control rounded-0" value="<?= old('precio_prov') ?>" 
                               required onchange="calcularPrecios()">
                    </div>
                </div>
                <div class="form-group">
                    <label for="precio_prov">Precio Público*</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" step="0.01" name="precio_pub" id="precio_prov" 
                               class="form-control rounded-0" value="<?= old('precio_prov') ?>" 
                               required onchange="calcularPrecios()">
                    </div>
                </div>
                <div class="form-group">
                    <label for="img">Imagen del Artículo</label>
                    <div class="custom-file">
                        <input type="file" name="img" id="img" class="custom-file-input">
                        <label class="custom-file-label rounded-0" for="img">Seleccionar imagen</label>
                    </div>
                    <small class="text-muted">Tamaño máximo: 2MB (JPG, PNG, GIF)</small>
                </div>
                
                <div class="form-group form-check">
                    <input type="checkbox" name="venta" id="venta" class="form-check-input" 
                           <?= old('venta', 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="venta">Disponible para venta</label>
                </div>
            </div>  
        </div>        
        <div class="form-group">
            <button type="submit" class="btn btn-danger rounded-0 mt-3">
                <i class="fas fa-save"></i> Guardar
            </button>
            <a href="<?= base_url('articulos') ?>" class="btn btn-secondary rounded-0 mt-3">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<script>

</script>
<?= $this->endSection() ?>
