<?= $this->extend('template') ?>
<?= $this->section('contenido') ?>
<!-- Sellos autoentintables -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <img class="img-fluid h-100" src="<?php echo base_url('public/img/telabanner.png'); ?>" alt="sellostextiles">
                </div>
                <div class="col-6">
                    <h1 class="text-primary text-uppercase mt-0 pt-0">Sellos Para Ropa</h1>
                    <h3 class="mb-4"><span class="text-primary">Identifica la ropa de tu bebé</span> con sellos de goma personalizados</h3>
                    <p class="mb-4">¿Cansado de buscar las etiquetas de la ropa de tu bebé? ¿Te preocupa que tu pequeño pierda sus prendas en la guardería o el colegio? ¡Los sellos de goma para ropa de bebé son la solución perfecta! Personaliza la ropa de tu bebé con su nombre, apellido o un dibujo divertido. Fáciles de usar, rápidos y duraderos. Resistentes al agua y a la lavadora. ¡Pide tus sellos de goma para ropa de bebé hoy mismo!</p>
                    <h4 class="text-primary">$340.00</h4>
                    <img width="250px" class="mb-4" src="<?php echo base_url('public/img/oxxopagos.png'); ?>" alt="oxxopagos">
                    <div class="row g-4 mb-3 pb-3">
                        <div class="col-12 wow fadeIn" data-wow-delay="0.1s">
                            <div class="d-flex">
                                <div class="ps-3">
                                    <h6>¿Cuantas lavadas resiste?</h6>
                                    <span>Tu sello resiste hasta 20 lavadas con agua fría. Despues puedes volver a marcar tus prendas</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 wow fadeIn" data-wow-delay="0.3s">
                            <div class="d-flex">
                                <div class="ps-3">
                                    <h6>¿Como lo pago?</h6>
                                    <span>Puedes pagar en Oxxo, por trasferencia o directamente en nuestra ubucación.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 wow fadeIn" data-wow-delay="0.3s">
                            <div class="d-flex">
                                <div class="ps-3">
                                    <h6>¿Se puede personalizar?</h6>
                                    <span>Sí. Puedes personalizar el sello con el nombre de tu bebé, un dibujo divertido o una frase especial. ¡Haz que la ropa de tu pequeño sea única!</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 wow fadeIn" data-wow-delay="0.3s">
                            <div class="d-flex">
                                <div class="ps-3">
                                    <h6>¿En cuanto tiempo lo entrego?</h6>
                                    <span>El trabajo se tiene listo 48 horas después de que se recibe el comprobante de pago. Cuando el trabajo está listo puedes elegir recogerlo en nuestra ubicación o recibirlo a domicilio.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 wow fadeIn" data-wow-delay="0.3s">
                            <div class="d-flex">
                                <div class="ps-3">
                                    <h6>¿Como lo recibo?</h6>
                                    <span>Contamos con servicio a domicilio en toda las zona metropolitana de Celaya y sus alrededores.</span>
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
