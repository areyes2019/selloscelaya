<?php echo $this->extend('Panel/panel_template')?>
<?php echo $this->section('contenido')?>
<div class="midde_cont" id="app">
    <div class="container-fluid">
        <div class="row column_title card rounded-0 shadow-sm">
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
                            <a class="btn btn-danger rounded-0 btn-sm mb-5" href="<?php echo base_url('nuevo_art_vista'); ?>">Agregar Articulo</a>
                            <a href="<?php echo base_url('categorias'); ?>" class="btn btn-danger btn-sm rounded-0 mb-5">Categorías</a>
                        </div>
                    </div>
                    <div class="card rounded-0 shadow-sm table_section padding_infor_info">
                        <div class="table-responsive-sm">
                            <table id="example" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Img</th>
                                        <th>Nombre</th>
                                        <th>Modelo</th>
                                        <th>Proveedor</th>
                                        <th>Costo</th>
                                        <th>Precio Dist.</th>
                                        <th>Precio Público</th>
                                        <th>Beneficio</th>
                                        <th>Stock</th>
                                        <th>Visible</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($articulos as $articulo): ?>
                                    <tr>
                                        <td><?= $articulo['id_articulo']; ?></td>
                                        <td>
                                            <?php if(!empty($articulo['img'])): ?>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#imagenModal" 
                                               data-imagen="<?= esc(base_url('public/img/catalogo/'.$articulo['img'])) ?>"
                                               data-nombre="<?= esc($articulo['nombre']) ?>">
                                               <img src="<?= esc(base_url('public/img/catalogo/'.$articulo['img'])) ?>" 
                                                    alt="<?= esc($articulo['nombre']) ?>" 
                                                    style="width: 50px; height: 50px; object-fit: contain;">
                                            </a>
                                            <?php else: ?>
                                                <span class="text-muted">Sin imagen</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($articulo['nombre']); ?></td>
                                        <td><?= esc($articulo['modelo']); ?></td>
                                        <td><?= esc($articulo['nombre_proveedor'] ?? 'No especificado'); ?></td>
                                        <td>$<?= number_format($articulo['precio_prov'], 2); ?></td>
                                        <td><strong class="text-primary">$<?= number_format($articulo['precio_dist'], 2); ?></strong></td>
                                        <td><strong>$<?= number_format($articulo['precio_pub'], 2); ?></strong></td>
                                        <td>$<?= number_format($articulo['precio_pub'] - $articulo['precio_prov'], 2); ?></td>
                                        <td class="<?= $articulo['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= $articulo['stock'] ?>
                                        </td>
                                        <td>
                                            <?php if($articulo['visible'] == 1): ?>
                                                <span class="badge bg-success">Visible</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Oculto</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="d-flex gap-1">
                                            <!-- Eliminar -->
                                            <a href="<?= base_url('eliminar_articulo/'.$articulo['id_articulo']) ?>" 
                                               onclick="return confirm('¿Seguro que quieres eliminar este registro?')" 
                                               class="btn btn-sm btn-danger rounded-0"
                                               title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            
                                            <!-- Editar --> 
                                            <a href="<?= base_url('editar_articulo/'.$articulo['id_articulo']) ?>" 
                                               class="btn btn-sm btn-success rounded-0"
                                               title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <!-- Edición rápida -->
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary rounded-0" 
                                                    @click="cambio_rapido(<?= $articulo['id_articulo'] ?>)"
                                                    title="Edición rápida">
                                                <i class="bi bi-lightning-charge"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
<!-- Modal de Edición Rápida -->
<div class="modal fade" id="quickEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edición rápida</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del artículo</label>
                        <input type="text" class="form-control" id="nombre" v-model="articulo.nombre">
                    </div>
                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" v-model="articulo.modelo">
                    </div>
                    <div class="mb-3">
                        <div class="row g-2">
                            <div class="col">
                                <label class="form-label">Precio Público</label>
                                <input type="number" step="0.01" class="form-control" placeholder="Público" v-model="articulo.precio_pub">
                            </div>
                            <div class="col">
                                <label class="form-label">Precio Dist.</label>
                                <input type="number" step="0.01" class="form-control" placeholder="Distribuidor" v-model="articulo.precio_dist">
                            </div>
                            <div class="col">
                                <label class="form-label">Precio Prov</label>
                                <input type="number" step="0.01" class="form-control" placeholder="Proveedor" v-model="articulo.precio_prov">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row g-2">
                            <div class="col">
                                <label for="" class="form-label">Categoria</label>
                                <select name="" id="" class="form-control" v-model="articulo.categoria">
                                    <option value="">Seleccione una categoría...</option>
                                    <option value="0">Sin categoria</option>
                                    <option value="1">Autoentintable</option>
                                    <option value="1">Madera</option>
                                    <option value="1">Fechadores</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button @click.prevent="guardarEdicionRapida(articulo.id_articulo)" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script>
$(document).ready(function() {
    new DataTable('#example');
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
<script src="<?php echo base_url('public/js/articulos.js'); ?>"></script>
<?php echo $this->endSection()?>