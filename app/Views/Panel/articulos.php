<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="midde_cont">
    <div class="container-fluid">
        <div class="row column_title">
            <div class="col-md-12">
                <div class="page_title">
                    <h2>Lista de precios</h2>
                </div>
                <!-- Mensajes Flash -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- row -->
        <div class="row">
            <!-- table section -->
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0">
                            <a class="btn btn-danger rounded-0" href="<?php echo base_url('nuevo_art_vista'); ?>">Agregar Articulo</a>
                        </div>
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

                            <button type="submit" class="btn btn-primary mt-3">Importar</button>
                        </form>
                    </div>
                    <div class="table_section padding_infor_info">
                        <div class="table-responsive-sm">
                            <table id="example" class="table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Img</th>
                                        <th>Nombre</th>
                                        <th>Modelo</th>
                                        <th>Costo</th>
                                        <th>Precio Distribuidor</th>
                                        <th>Precio Público</th>
                                        <th>Beneficio</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($articulos as $articulo): ?>
                                    <tr>
                                        <td><?= $articulo['id_articulo'];?></td>
                                        <td>
                                            <?php if(!empty($articulo['img'])): ?>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#imagenModal" 
                                               data-imagen="<?= base_url('ver_imagen/'.$articulo['img']) ?>"
                                               data-nombre="<?= $articulo['nombre'] ?>">
                                                <img src="<?= base_url('/ver_imagen/'.$articulo['img']) ?>" alt="Imagen" width="30" style="max-height: 30px; object-fit: contain;">
                                            </a>
                                            <?php else: ?>
                                                <span class="text-muted">Sin imagen</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $articulo['nombre'];?></td>
                                        <td><?= $articulo['modelo'];?></td>
                                        <td>$<?= $articulo['precio_prov'];?></td>
                                        <td><strong class="text-primary">$<?= $articulo['precio_dist'];?></strong></td>
                                        <td><strong>$<?= $articulo['precio_pub'];?></strong></td>
                                        <td>$<?= $articulo['precio_pub'] - $articulo['precio_prov'];?></td>
                                        <td>
                                            <a href="eliminar_articulo/<?php echo $articulo['id_articulo'] ?>" onclick="return confirm('¿Seguro que quieres eliminar este registro?')" class="btn btn-sm rounded-0 my-btn-danger">Eliminar</a>
                                            <a href="editar_articulo/<?php echo $articulo['id_articulo'] ?>" class="btn btn-sm rounded-0 my-btn-success">Editar</a>
                                        </td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Modelo</th>
                                        <th>Precio Proveedor</th>
                                        <th>Precio Público</th>
                                        <th>Beneficio</th>
                                        <th>Acción</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar imagen -->
<div class="modal fade" id="imagenModal" tabindex="-1" aria-labelledby="imagenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagenModalLabel">Imagen del producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImagen" src="" class="img-fluid" alt="Imagen ampliada" style="max-height: 70vh;">
                <h5 id="modalNombre" class="mt-3"></h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="copiarImagenBtn">
                    <i class="fab fa-whatsapp"></i> Copiar para WhatsApp
                </button>
                <a href="#" id="descargarImagenBtn" class="btn btn-primary">
                    <i class="fas fa-download"></i> Descargar imagen
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Incluir las librerías necesarias -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    new DataTable('#example', {
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-MX.json'
        }
    });
    
    // Configurar el modal cuando se muestra
    $('#imagenModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var imagenUrl = button.data('imagen');
        var nombreProducto = button.data('nombre');
        
        var modal = $(this);
        modal.find('#modalImagen').attr('src', imagenUrl);
        modal.find('#modalNombre').text(nombreProducto);
        
        // Configurar el enlace de descarga
        $('#descargarImagenBtn').attr('href', imagenUrl)
                               .attr('download', nombreProducto.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.jpg');
    });
    
    // Función para copiar la imagen
    $('#copiarImagenBtn').click(async function() {
        var img = document.getElementById('modalImagen');
        var imagenUrl = img.src;
        
        try {
            // Primero intentamos con la API moderna de Clipboard
            if (navigator.clipboard && window.ClipboardItem) {
                const response = await fetch(imagenUrl);
                const blob = await response.blob();
                await navigator.clipboard.write([
                    new ClipboardItem({ [blob.type]: blob })
                ]);
                showAlert('success', 'Imagen copiada al portapapeles. Ahora puedes pegarla en WhatsApp.');
            } 
            // Si falla, ofrecemos alternativa
            else {
                // Creamos un elemento temporal para copiar el enlace
                const tempInput = document.createElement('input');
                document.body.appendChild(tempInput);
                tempInput.value = imagenUrl;
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                
                showAlert('info', 'Enlace de la imagen copiado. En WhatsApp, pega este enlace para compartir la imagen.');
            }
        } catch (error) {
            console.error('Error al copiar:', error);
            showAlert('danger', 'No se pudo copiar la imagen. Intenta descargarla y compartirla manualmente.');
        }
    });
    
    // Función para mostrar alertas
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.style.position = 'fixed';
        alertDiv.style.bottom = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Eliminar la alerta después de 5 segundos
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
});
</script>

<!-- Script para el drag and drop -->
<script type="text/javascript" src="<?php echo base_url('public/js/drag.js'); ?>"></script>

<?php echo $this->endSection()?>