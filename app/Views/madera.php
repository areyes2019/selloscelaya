<?= $this->extend('template') ?>
<?= $this->section('contenido') ?>

<!-- Catálogo de Productos -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h1 class="text-primary text-uppercase">Nuestros Productos</h1>
            <h3 class="mb-5">Catálogo de <span class="text-primary">Sellos Personalizados</span></h3>
        </div>
        
        <div class="row g-4">
            <?php foreach($productos as $producto): ?>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="product-item">
                    <div class="overflow-hidden position-relative">
                        <img class="img-fluid" src="<?= base_url('public/img/' . $producto['imagen']) ?>" alt="<?= $producto['nombre'] ?>">
                        <div class="product-overlay">
                            <a class="btn btn-square btn-primary" href="<?= base_url('productos/detalle/' . $producto['id']) ?>">
                                <i class="fa fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    <div class="text-center p-4">
                        <h5 class="mb-3"><?= $producto['nombre'] ?></h5>
                        <span class="text-primary"><?= $producto['precio'] ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>