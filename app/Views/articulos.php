<?= $this->extend('template') ?>
<?= $this->section('contenido') ?>

<!-- Modal para vista detallada -->
<div class="modal fade" id="productoModal" tabindex="-1" aria-labelledby="productoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- Columna de la imagen -->
                    <div class="col-md-6">
                        <div class="image-container bg-light rounded" style="height: 400px;">
                            <img id="modalImagen" class="img-fluid w-100 h-100 p-3" 
                                 src="" 
                                 alt="Imagen del producto" 
                                 style="object-fit: contain; object-position: center;">
                        </div>
                    </div>
                    <!-- Columna de la información -->
                    <div class="col-md-6">
                        <h3 id="modalTitulo" class="mb-3"></h3>
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <span id="modalPrecio" class="h4 text-primary"></span>
                                <span id="modalPrecioAnterior" class="text-muted ms-2"><del></del></span>
                            </div>
                            <div id="modalEstrellas"></div>
                            <small id="modalOpiniones" class="ms-2"></small>
                        </div>
                        
                        <p id="modalDescripcion" class="mb-4"></p>
                        
                        <div class="d-flex gap-2 mb-4">
                            <a id="modalCarrito" href="#" class="btn btn-outline-dark btn-lg px-4">
                                <i class="fa fa-shopping-cart me-2"></i>Añadir al carrito
                            </a>
                            <a id="modalFavoritos" href="#" class="btn btn-outline-dark btn-lg px-4">
                                <i class="far fa-heart me-2"></i>Favoritos
                            </a>
                        </div>
                        
                        <div class="border-top pt-3">
                            <a id="modalWhatsapp" href="#" target="_blank" class="btn btn-primary btn-lg w-100 py-3">
                                <i class="fab fa-whatsapp me-2"></i> Quiero más información
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Título de la Página -->
<div class="container-fluid page-header mb-5 p-0" style="background-image: url(<?= base_url('public/img/carousel-bg-1.jpg') ?>);">
    <div class="container-fluid page-header-inner py-5">
        <div class="container text-center">
            <h1 class="display-3 text-white mb-3 animated slideInDown"><?= esc($categoria_actual['nombre']) ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center text-uppercase">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Inicio</a></li>
                    <li class="breadcrumb-item text-white active" aria-current="page"><?= esc($categoria_actual['nombre']) ?></li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="row g-5">
        <!-- Columna de Productos -->
        <div class="col-lg-8">
            <div class="row g-4">
                <?php if (!empty($articulos)): ?>
                    <?php foreach ($articulos as $articulo): ?>
                        <div class="col-lg-4 col-md-6 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                            <div class="product-item bg-white rounded h-100 d-flex flex-column border">
                                <!-- Contenedor de imagen optimizado -->
                                <div class="image-container position-relative" style="height: 220px; background-color: #f8f9fa;">
                                    <img class="img-fluid w-100 h-100 p-2"
                                        src="<?= base_url('public/img/catalogo/'.$articulo['img']);?>"
                                        alt="<?= esc($articulo['nombre'] ?? 'Imagen del producto'); ?>"
                                        style="object-fit: contain; object-position: center;">
                                    <div class="product-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                        <div class="product-action bg-white rounded-pill p-2 shadow">
                                            <a class="btn btn-sm btn-outline-dark rounded-circle mx-1" href="<?= base_url('carrito/agregar/' . esc($articulo['id_articulo'])); ?>">
                                                <i class="fa fa-shopping-cart"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-dark rounded-circle mx-1" href="<?= base_url('favoritos/agregar/' . esc($articulo['id_articulo'])); ?>">
                                                <i class="far fa-heart"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-dark rounded-circle mx-1 ver-detalle" 
                                               href="#" 
                                               data-bs-toggle="modal" 
                                               data-bs-target="#productoModal"
                                               data-imagen="<?= base_url('public/img/catalogo/'.$articulo['img']) ?>"
                                               data-titulo="<?= esc($articulo['nombre']) ?>"
                                               data-precio="$<?= number_format(esc($articulo['precio_pub']), 2) ?>"
                                               data-precio-anterior="<?= !empty($articulo['precio_anterior']) && $articulo['precio_anterior'] > $articulo['precio_pub'] ? '$'.number_format(esc($articulo['precio_anterior']), 2) : '' ?>"
                                               data-estrellas="<?= round(esc($articulo['estrellas_calificacion'] ?? 0)) ?>"
                                               data-opiniones="<?= esc($articulo['numero_opiniones'] ?? 0) ?>"
                                               data-descripcion="<?= esc($articulo['descripcion_larga_articulo'] ?? 'Descripción detallada no disponible.') ?>"
                                               data-carrito="<?= base_url('carrito/agregar/' . esc($articulo['id_articulo'])) ?>"
                                               data-favoritos="<?= base_url('favoritos/agregar/' . esc($articulo['id_articulo'])) ?>"
                                               data-whatsapp="https://api.whatsapp.com/send?phone=524613581090&text=Hola,%20Me%20interesa%20más%20información%20sobre%20el%20producto%20<?= rawurlencode($articulo['nombre']) ?>">
                                                <i class="fa fa-search"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center p-3 flex-grow-1 d-flex flex-column">
                                    <a class="h5 text-decoration-none text-truncate d-block mb-2" 
                                       href="<?= base_url('articulo/' . esc($articulo['slug_articulo'] ?? $articulo['id_articulo'])); ?>">
                                        <?= esc($articulo['nombre']); ?>
                                    </a>
                                    <div class="mt-auto">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <h5 class="text-primary mb-0">$<?= number_format(esc($articulo['precio_pub']), 2); ?></h5>
                                            <?php if (!empty($articulo['precio_anterior']) && $articulo['precio_anterior'] > $articulo['precio_pub']): ?>
                                                <h6 class="text-muted ms-2 mb-0"><small><del>$<?= number_format(esc($articulo['precio_anterior']), 2); ?></del></small></h6>
                                            <?php endif; ?>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center mt-2">
                                            <?php
                                            $estrellas = round(esc($articulo['estrellas_calificacion'] ?? 0));
                                            for ($i = 1; $i <= 5; $i++):
                                                if ($i <= $estrellas): ?>
                                                    <small class="fa fa-star text-warning"></small>
                                                <?php elseif ($i - 0.5 <= esc($articulo['estrellas_calificacion'] ?? 0)): ?>
                                                    <small class="fa fa-star-half-alt text-warning"></small>
                                                <?php else: ?>
                                                    <small class="far fa-star text-warning"></small>
                                                <?php endif;
                                            endfor; ?>
                                            <small class="ms-1">(<?= esc($articulo['numero_opiniones'] ?? 0); ?>)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center py-4">
                            <i class="fas fa-box-open fa-2x mb-3"></i>
                            <h4>No hay artículos disponibles</h4>
                            <p class="mb-0">Por el momento no tenemos productos en esta categoría.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar Mejorada -->
        <div class="col-lg-4">
            
            <div class="sticky-top" style="top: 20px;">
                <div class="bg-light p-4 rounded shadow-sm mb-4">
                    <h6 class="text-primary text-uppercase mb-3">// <?= esc($categoria_actual['nombre']) ?> //</h6>
                    <h2 class="mb-3"><span class="text-primary"><?= esc($categoria_actual['nombre']) ?></span></h2>
                    <p class="lead mb-4">
                        <?= esc($categoria_actual['subtitulo_categoria'] ?? 'Prácticos y Limpios') ?>
                    </p>
                    <p class="mb-4">
                        <?= esc($categoria_actual['descripcion_categoria'] ?? 'Nuestros sellos de esta categoría ofrecen una solución conveniente y eficiente...') ?>
                    </p>
                    
                    <div class="border-top pt-3">
                        <a href="https://api.whatsapp.com/send?phone=524613581090&text=Hola,%20Me%20interesa%20más%20información%20sobre%20los%20sellos%20de%20<?= rawurlencode($categoria_actual['nombre']) ?>" 
                           target="_blank" 
                           class="btn btn-primary btn-lg w-100 py-3">
                           <i class="fab fa-whatsapp me-2"></i> Quiero más información
                        </a>
                    </div>
                </div>

                <?php if (!empty($articulos)): ?>
                <div class="bg-light p-4 rounded shadow-sm">
                    <h5 class="mb-4">Productos destacados</h5>
                    <div class="row g-3">
                        <?php $itemNumber = 0; ?>
                        <?php foreach ($articulos as $idx => $articulo_sidebar): ?>
                            <?php $itemNumber++; ?>
                            <?php if ($itemNumber > 7) break; ?>
                            <div class="col-12 wow fadeIn" data-wow-delay="<?= ($idx % 3 + 1) * 0.2 ?>s">
                                <div class="d-flex align-items-center bg-white p-3 rounded shadow-sm-hover">
                                    <div class="flex-shrink-0 me-3">
                                        <img src="<?= base_url('public/img/catalogo/'.$articulo_sidebar['img']);?>" 
                                             alt="<?= esc($articulo_sidebar['nombre']); ?>" 
                                             class="img-fluid rounded" 
                                             style="width: 60px; height: 60px; object-fit: contain;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= esc($articulo_sidebar['nombre']); ?></h6>
                                        <small class="text-muted d-block"><?= esc($articulo_sidebar['descripcion_corta_articulo'] ?? 'Descripción breve del sello.'); ?></small>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <strong class="text-primary">$<?= number_format(esc($articulo_sidebar['precio_pub']), 2); ?></strong>
                                            <a href="<?= base_url('articulo/' . esc($articulo_sidebar['slug_articulo'] ?? $articulo_sidebar['id_articulo'])); ?>" class="btn btn-sm btn-outline-primary">Ver</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para el Modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('productoModal'));
    const verDetalleButtons = document.querySelectorAll('.ver-detalle');
    
    verDetalleButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Obtener los datos del producto
            const imagen = this.getAttribute('data-imagen');
            const titulo = this.getAttribute('data-titulo');
            const precio = this.getAttribute('data-precio');
            const precioAnterior = this.getAttribute('data-precio-anterior');
            const estrellas = parseInt(this.getAttribute('data-estrellas'));
            const opiniones = this.getAttribute('data-opiniones');
            const descripcion = this.getAttribute('data-descripcion');
            const carrito = this.getAttribute('data-carrito');
            const favoritos = this.getAttribute('data-favoritos');
            const whatsapp = this.getAttribute('data-whatsapp');
            
            // Actualizar el modal con los datos
            document.getElementById('modalImagen').src = imagen;
            document.getElementById('modalImagen').alt = titulo;
            document.getElementById('modalTitulo').textContent = titulo;
            document.getElementById('modalPrecio').textContent = precio;
            
            const precioAnteriorElement = document.getElementById('modalPrecioAnterior');
            if (precioAnterior) {
                precioAnteriorElement.innerHTML = '<del>' + precioAnterior + '</del>';
                precioAnteriorElement.style.display = 'inline';
            } else {
                precioAnteriorElement.style.display = 'none';
            }
            
            // Generar estrellas
            const estrellasContainer = document.getElementById('modalEstrellas');
            estrellasContainer.innerHTML = '';
            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('small');
                if (i <= estrellas) {
                    star.className = 'fa fa-star text-warning';
                } else if (i - 0.5 <= estrellas) {
                    star.className = 'fa fa-star-half-alt text-warning';
                } else {
                    star.className = 'far fa-star text-warning';
                }
                estrellasContainer.appendChild(star);
            }
            
            document.getElementById('modalOpiniones').textContent = '(' + opiniones + ')';
            document.getElementById('modalDescripcion').textContent = descripcion;
            document.getElementById('modalCarrito').href = carrito;
            document.getElementById('modalFavoritos').href = favoritos;
            document.getElementById('modalWhatsapp').href = whatsapp;
            
            // Mostrar el modal
            modal.show();
        });
    });
});
</script>

<?= $this->endSection() ?>