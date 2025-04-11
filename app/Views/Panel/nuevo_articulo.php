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
    <div class="row">
        <div class="col-md-6">
            <form action="<?= base_url('nuevo_articulo') ?>" method="post" enctype="multipart/form-data">
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
        <div class="col-md-6">   
            <h4 class="mt-3">Importacion maisva</h4>
            <form action="<?= base_url('import_masivo') ?>" method="post" enctype="multipart/form-data">
                <div class="drop-zone mb-3" id="dropZone">
                    <p id="dropZoneText">Arrastra y suelta tu archivo aquí o haz clic para seleccionarlo</p>
                    <input type="file" name="archivo_excel" class="d-none" id="fileInput" accept=".xlsx,.xls" required>
                </div>

                <div id="filePreview" class="text-success fw-bold"></div>

                <small class="form-text text-muted d-block mb-3">
                    Debe contener exactamente 8 columnas en este orden:<br>
                    Nombre, Modelo, Precio Proveedor, Mínimo, Stock, Clave, Nombre Imagen, Disponible
                </small>

                <button type="submit" class="btn btn-danger rounded-0 mt-3"><i class="bi bi-download"></i> Importar</button>
            </form>
        </div>
    </div>        
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const dropZoneText = document.getElementById('dropZoneText');
    const filePreview = document.getElementById('filePreview');
    
    // Evento cuando se arrastra sobre la zona
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('drop-zone--over');
    });
    
    // Eventos cuando se deja de arrastrar
    ['dragleave', 'dragend'].forEach(type => {
        dropZone.addEventListener(type, () => {
            dropZone.classList.remove('drop-zone--over');
        });
    });
    
    // Evento cuando se suelta el archivo
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('drop-zone--over');
        
        if (e.dataTransfer.files.length && isValidExcelFile(e.dataTransfer.files[0].name)) {
            fileInput.files = e.dataTransfer.files;
            showFileName();
        }
    });
    
    // Click en la zona
    dropZone.addEventListener('click', () => fileInput.click());
    
    // Cambio en el input file
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length && isValidExcelFile(fileInput.files[0].name)) {
            showFileName();
        }
    });
    
    // Mostrar nombre del archivo
    function showFileName() {
        filePreview.textContent = `Archivo listo: ${fileInput.files[0].name}`;
        dropZoneText.textContent = 'Suelta otro archivo Excel o haz clic para cambiar';
    }
    
    // Validar que sea Excel
    function isValidExcelFile(filename) {
        const allowedExtensions = /(\.xlsx|\.xls)$/i;
        if (!allowedExtensions.exec(filename)) {
            alert('Por favor sube solo archivos Excel (.xlsx o .xls)');
            return false;
        }
        return true;
    }
});
</script>
<?= $this->endSection() ?>
