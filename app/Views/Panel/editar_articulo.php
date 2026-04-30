<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="container mt-3">
    <div class="my-card">
        <h4 class="m-0 p-0"><?php echo esc($nombre) ?></h4>
    </div>
    <div class="my-card mt-3">
        <?php foreach ($articulos as $articulo): ?>
        <form class="row g-3" action="<?= base_url('actualizar_articulo') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="idarticulo" value="<?= $articulo['id_articulo'] ?>">
            <input type="hidden" name="imagen_actual" value="<?= $articulo['img'] ?>">
            
            <div class="col-md-6">
                <label class="form-label">Nombre*</label>
                <input type="text" class="my-input shadow-none form-control p-1" name="nombre" 
                       value="<?= old('nombre', $articulo['nombre']) ?>" required>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Modelo</label>
                <input type="text" class="my-input shadow-none form-control p-1" name="modelo" 
                       value="<?= old('modelo', $articulo['modelo']) ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Precio Proveedor*</label>
                <input type="number" step="0.01" class="my-input shadow-none form-control p-1" name="precio_prov" 
                       value="<?= old('precio_prov', $articulo['precio_prov']) ?>" required onchange="calcularPrecios()">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Precio Público*</label>
                <input type="number" step="0.01" class="my-input shadow-none form-control p-1" name="precio_pub" 
                       value="<?= old('precio_pub', $articulo['precio_pub']) ?>" required onchange="calcularPrecios()">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Precio Distribuidor*</label>
                <input type="number" step="0.01" class="my-input shadow-none form-control p-1" name="precio_dist" 
                       value="<?= old('precio_dist', $articulo['precio_dist']) ?>" required>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Mínimo para reorden</label>
                <input type="number" class="my-input shadow-none form-control p-1" min="0" name="minimo" 
                       value="<?= old('minimo', $articulo['minimo']) ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Clave del producto</label>
                <input type="text" class="my-input shadow-none form-control p-1" name="clave_producto" 
                       value="<?= old('clave_producto', $articulo['clave_producto']) ?>">
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Categoría*</label>
                <select class="form-select my-input shadow-none form-control p-1" name="categoria" required>
                    <option value="">Selecciona una categoría...</option>
                    <?php 
                    $categoriaActual = old('categoria') ?? $articulo['categoria'] ?? null;
                    foreach ($categorias as $categoria): 
                        $selected = ($categoriaActual == $categoria['id_categoria']) ? 'selected' : '';
                    ?>
                        <option value="<?= $categoria['id_categoria'] ?>" <?= $selected ?>>
                            <?= esc($categoria['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Imagen</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input my-input shadow-none form-control p-1" 
                           name="img" id="img" accept="image/jpeg, image/png, image/gif">
                    <label class="custom-file-label" for="img"><?= $articulo['img'] ? 'Cambiar imagen' : 'Seleccionar imagen' ?></label>
                </div>
                <small class="text-muted">Tamaño máximo: 2MB (JPG, PNG, GIF). Se redimensionará automáticamente.</small>
                
                <?php if ($articulo['img']): ?>
                    <div class="mb-2 mt-3">
                        <img src="<?= base_url('public/img/catalogo/'.$articulo['img']) ?>" 
                             class="img-thumbnail" 
                             style="max-width: 150px; max-height: 150px;"
                             alt="Imagen actual del artículo">
                        <div class="mt-1">
                            <small class="text-muted"><?= $articulo['img'] ?></small>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="eliminar_imagen" id="eliminar_imagen">
                            <label class="form-check-label text-danger" for="eliminar_imagen">
                                Eliminar imagen actual
                            </label>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">En Stock*</label>
                <select class="form-select my-input shadow-none form-control p-1" name="stock" required>
                    <option value="1" <?= old('stock', $articulo['stock']) == 1 ? 'selected' : '' ?>>Sí</option>
                    <option value="0" <?= old('stock', $articulo['stock']) == 0 ? 'selected' : '' ?>>No</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Disponible para venta*</label>
                <select class="form-select my-input shadow-none form-control p-1" name="venta" required>
                    <option value="1" <?= old('venta', $articulo['venta']) == 1 ? 'selected' : '' ?>>Sí</option>
                    <option value="0" <?= old('venta', $articulo['venta']) == 0 ? 'selected' : '' ?>>No</option>
                </select>
            </div>
            
            <!-- Nuevo checkbox para visibilidad -->
            <div class="col-md-3">
                <label class="form-label">Visible en catálogo*</label>
                <select class="form-select my-input shadow-none form-control p-1" name="visible" required>
                    <option value="1" <?= old('visible', $articulo['visible'] ?? 1) == 1 ? 'selected' : '' ?>>Sí</option>
                    <option value="0" <?= old('visible', $articulo['visible'] ?? 1) == 0 ? 'selected' : '' ?>>No</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Proveedor*</label>
                <select class="form-select my-input shadow-none form-control p-1" name="proveedor" required>
                    <option value="">Selecciona un proveedor...</option>
                    <?php foreach ($proveedores as $proveedor): ?>
                        <option value="<?= $proveedor['id_proveedor'] ?>" 
                            <?= old('proveedor', $articulo['proveedor']) == $proveedor['id_proveedor'] ? 'selected' : '' ?>>
                            <?= esc($proveedor['empresa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-12">
                <button type="submit" class="my-btn-primary p-3">
                    <span class="bi bi-save"></span> Guardar cambios
                </button>
                <a href="<?= base_url('articulos') ?>" class="btn btn-secondary ms-2 p-3">
                    <span class="bi bi-x-circle"></span> Cancelar
                </a>
            </div>
        </form>
        <?php endforeach; ?>
    </div>
</div>

<script>
    // Mostrar nombre del archivo seleccionado
    document.getElementById('img').addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : "Seleccionar imagen";
        this.nextElementSibling.innerText = fileName;
    });
    
    // Función para calcular precios (debe coincidir con la del formulario de creación)
    function calcularPrecios() {
        // Implementa la misma lógica que en el formulario de creación
    }
</script>

<?php echo $this->endSection()?>