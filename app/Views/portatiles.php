<?= $this->extend('template') ?>
<meta property="og:image" content="<?php echo base_url('public/img/20.png'); ?>" />
<?= $this->section('contenido') ?>
<!-- Sellos autoentintables -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 pt-4" style="min-height: 400px;">
                    <div class="position-relative h-100 wow fadeIn" data-wow-delay="0.1s">
                        <img class="position-absolute img-fluid w-100 h-100" src="<?php echo base_url('public/img/40.png'); ?>" style="object-fit: contain;" alt="">
                        
                    </div>
                </div>
                <div class="col-lg-6">
                    <h6 class="text-primary text-uppercase">// Sellos Fechadores //</h6>
                    <h1 class="mb-4"><span class="text-primary">Control Total /</span> Prácticos y Limpios</h1>
                    <p class="mb-4">Olvídate de la tinta manchada y las limitaciones de espacio con un sello de goma portátil. Su tamaño compacto te permite llevarlo a donde vayas, ya sea en tu bolso, mochila o incluso en el bolsillo.</p>
                    <div class="row g-4 mb-3 pb-3">
                        <div class="col-12 wow fadeIn" data-wow-delay="0.1s">
                            <div class="d-flex">
                                <div class="bg-light d-flex flex-shrink-0 align-items-center justify-content-center mt-1" style="width: 45px; height: 45px;">
                                    <span class="fw-bold text-secondary">01</span>
                                </div>
                                <div class="ps-3">
                                    <h6>Sello portátil de 38 x 14 mm - $200.00</h6>
                                    <span>Ideal para hasta 3 líneas de texto</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 wow fadeIn" data-wow-delay="0.3s">
                            <div class="d-flex">
                                <div class="bg-light d-flex flex-shrink-0 align-items-center justify-content-center mt-1" style="width: 45px; height: 45px;">
                                    <span class="fw-bold text-secondary">02</span>
                                </div>
                                <div class="ps-3">
                                    <h6>Sello portátil de 47 x 18 mm - $230.00</h6>
                                    <span>Lo puedes personalizar con hasta 4 líneas de texto y un logo pequeño</span>
                                </div>
                            </div>
                        </div>
                         <div class="col-12 wow fadeIn" data-wow-delay="0.3s">
                            <div class="d-flex">
                                <div class="bg-light d-flex flex-shrink-0 align-items-center justify-content-center mt-1" style="width: 45px; height: 45px;">
                                    <span class="fw-bold text-secondary">03</span>
                                </div>
                                <div class="ps-3">
                                    <h6>Sello portátil de 59 x 23 mm - $250.00</h6>
                                    <span>Redondo con capacidad de hasta 5 lineas y un logo pequeño</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="" class="btn btn-primary py-3 px-5">Queiro mas información<i class="fa fa-arrow-right ms-3"></i></a>
                </div>
            </div>
        </div>
    </div>
<!-- Sellos gigantes -->
<?= $this->endSection()?>