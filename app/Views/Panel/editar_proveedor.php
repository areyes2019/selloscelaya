<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>

<div class="container mt-4">
    
    <!-- Título -->
    <div class="my-card mb-3 p-3">
        <h4 class="mb-0">
            <span class="bi bi-building"></span>
            <?php echo $empresa?>
        </h4>
        <small class="text-muted">Editar información del proveedor</small>
    </div>

    <!-- Formulario -->
    <div class="my-card p-4">
        <?php foreach ($proveedores as $proveedor): ?>
        
        <form class="row g-3" action="<?php echo base_url('actualizar_proveedor'); ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" value="<?php echo $proveedor['id_proveedor']?>" name="id_proveedor">

            <!-- Empresa -->
            <div class="col-md-6">
                <label class="form-label">Empresa</label>
                <input type="text" class="form-control my-input" 
                       name="empresa" 
                       value="<?php echo $proveedor['empresa'] ?>">
            </div>

            <!-- Contacto -->
            <div class="col-md-6">
                <label class="form-label">Contacto</label>
                <input type="text" class="form-control my-input" 
                       name="contacto" 
                       value="<?php echo $proveedor['contacto'] ?>">
            </div>

            <!-- Teléfono -->
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="text" class="form-control my-input" 
                       name="telefono" 
                       value="<?php echo $proveedor['telefono'] ?>">
            </div>

            <!-- Correo -->
            <div class="col-md-6">
                <label class="form-label">Correo</label>
                <input type="email" class="form-control my-input" 
                       name="correo" 
                       value="<?php echo $proveedor['correo'] ?>">
            </div>

            <!-- 🔥 NUEVOS CAMPOS -->

            <!-- Descuento -->
            <div class="col-md-6">
                <label class="form-label">
                    Descuento del proveedor (%)
                </label>
                <input type="number" step="0.01" min="0" 
                       class="form-control my-input" 
                       name="descuento" 
                       value="<?php echo $proveedor['descuento'] ?>">
                <small class="text-muted">Ej: 10 = 10% de descuento</small>
            </div>

            <!-- IVA -->
            <div class="col-md-6">
                <label class="form-label">
                    Tipo de precio
                </label>
                <select class="form-control my-input" name="incluye_iva">
                    <option value="0" <?php echo ($proveedor['incluye_iva'] == 0) ? 'selected' : '' ?>>
                        Precio + IVA
                    </option>
                    <option value="1" <?php echo ($proveedor['incluye_iva'] == 1) ? 'selected' : '' ?>>
                        Precio ya incluye IVA
                    </option>
                </select>
            </div>

            <!-- Botón -->
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary btn-icon-split">
                    <span class="icon text-white-50">
                        <i class="bi bi-save"></i>
                    </span>
                    <span class="text">Actualizar proveedor</span>
                </button>
            </div>

        </form>

        <?php endforeach; ?>
    </div>

</div>

<?php echo $this->endSection()?>