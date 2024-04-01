<?= $this->extend('template') ?>
<?= $this->section('contenido') ?>
<!-- Sellos autoentintables -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 pt-4" style="min-height: 400px;">
                    <div class="position-relative h-100 wow fadeIn" data-wow-delay="0.1s">
                        <img class="position-absolute img-fluid w-100 h-100" src="<?php echo base_url('public/img/telabanner.png'); ?>" style="object-fit: contain;" alt="">
                        
                    </div>
                </div>
                <div class="col-lg-6">
                    <h6 class="text-primary text-uppercase">// Sellos Para Ropa //</h6>
                    <h1 class="mb-4"><span class="text-primary">Identifica la ropa de tu bebé</span> con sellos de goma personalizados</h1>
                    <p class="mb-4">¿Cansado de buscar las etiquetas de la ropa de tu bebé? ¿Te preocupa que tu pequeño pierda sus prendas en la guardería o el colegio? ¡Los sellos de goma para ropa de bebé son la solución perfecta! Personaliza la ropa de tu bebé con su nombre, apellido o un dibujo divertido. Fáciles de usar, rápidos y duraderos. Resistentes al agua y a la lavadora. Ideales para ropa, baberos, toallas, mochilas y mucho más. ¡Pide tus sellos de goma para ropa de bebé hoy mismo y disfruta de la tranquilidad de saber que sus cosas siempre estarán identificadas!</p>
                    <div class="row g-4 mb-3 pb-3">
                        <div class="col-12 wow fadeIn" data-wow-delay="0.1s">
                            <div class="d-flex">
                                <div class="bg-light d-flex flex-shrink-0 align-items-center justify-content-center mt-1" style="width: 45px; height: 45px;">
                                    <span class="fw-bold text-secondary">01</span>
                                </div>
                                <div class="ps-3">
                                    <h6>Adiós a las etiquetas perdidas</h6>
                                    <span>Olvídate de las etiquetas que se caen o se desgastan. Los sellos de goma son permanentes y resistentes al agua y a la lavadora.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 wow fadeIn" data-wow-delay="0.3s">
                            <div class="d-flex">
                                <div class="bg-light d-flex flex-shrink-0 align-items-center justify-content-center mt-1" style="width: 45px; height: 45px;">
                                    <span class="fw-bold text-secondary">02</span>
                                </div>
                                <div class="ps-3">
                                    <h6>Ahorro de tiempo y dinero</h6>
                                    <span>No necesitas comprar etiquetas nuevas cada vez que tu bebé crece o cambia de ropa. Un sello de goma te durará años.</span>
                                </div>
                            </div>
                        </div>
                         <div class="col-12 wow fadeIn" data-wow-delay="0.3s">
                            <div class="d-flex">
                                <div class="bg-light d-flex flex-shrink-0 align-items-center justify-content-center mt-1" style="width: 45px; height: 45px;">
                                    <span class="fw-bold text-secondary">03</span>
                                </div>
                                <div class="ps-3">
                                    <h6>Personalización única</h6>
                                    <span>Puedes personalizar el sello con el nombre de tu bebé, un dibujo divertido o una frase especial. ¡Haz que la ropa de tu pequeño sea única!</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="<?php echo base_url('catalogo'); ?>" class="btn btn-primary py-3 px-5">Queiro ver el muestrario<i class="fa fa-arrow-right ms-3"></i></a>
                </div>
            </div>
        </div>
    </div>
<!-- Sellos gigantes -->
<?php echo $this->endSection()?>
