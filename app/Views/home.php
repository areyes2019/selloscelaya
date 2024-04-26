<?= $this->extend('template') ?>    

<?php echo $this->section('contenido') ?>
    <!-- Carousel Start -->
    <div class="container-fluid p-0 mb-5">
        <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="<?php echo base_url('public/img/baner25.jpg'); ?>" alt="imagen de mi banner principal con grupos de sellos de goma">
                    <div class="carousel-caption d-flex align-items-center">
                        <div class="container">
                            <div class="row align-items-center justify-content-center justify-content-lg-start">
                                <div class="col-10 col-lg-7 text-center text-lg-start">
                                    <h4 class="text-white text-uppercase mb-3 animated slideInDown">// Marca tu mundo con //</h4>
                                    <h1 class="display-3 text-white mb-4 pb-3 animated slideInDown">Sellos Celaya</h1>
                                    <h3 class="display-3 text-white mb-4 pb-3 animated slideInDown">Creando impresiones que duran</h3>
                                    <a href="tel:4613581090" class="btn btn-primary py-3 px-5 animated slideInDown">Quiero información<i class="fa fa-arrow-right ms-3"></i></a>
                                </div>
                                <div class="col-lg-5 d-none d-lg-flex animated zoomIn">
                                    <img class="img-fluid" src="<?php echo base_url('public/img/banner01.png'); ?>" alt="" width="500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#header-carousel"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button> -->
           
        </div>
    </div>
    <!-- Carousel End -->


    <!-- Service Start -->
    <div class="container-xxl py-5" id="whyus">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="d-flex py-5 px-4">
                        <i class="fa fa-certificate fa-3x text-primary flex-shrink-0"></i>
                        <div class="ps-4">
                            <h5 class="mb-3">Calidad Garantizada</h5>
                            <p>Nuestros sellos de goma son fabricados con los más altos estándares de calidad, asegurando impresiones claras y duraderas en cada uso.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="d-flex bg-light py-5 px-4">
                        <i class="fa fa-users-cog fa-3x text-primary flex-shrink-0"></i>
                        <div class="ps-4">
                            <h5 class="mb-3">Servicio a cliente excepcional</h5>
                            <p>Nuestro equipo amable y experimentado está siempre disponible para responder tus preguntas</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="d-flex py-5 px-4">
                        <i class="fa fa-tools fa-3x text-primary flex-shrink-0"></i>
                        <div class="ps-4">
                            <h5 class="mb-3">Personalización a tu medida</h5>
                            <p>Entendemos que cada cliente es único, por lo que ofrecemos opciones de personalización flexibles para adaptarnos a tus necesidades específicas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->


    <!-- About Start -->
    <div class="container-xxl py-5" id="whoweare">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 pt-4" style="min-height: 400px;">
                    <div class="position-relative h-100 wow fadeIn" data-wow-delay="0.1s">
                        <img class="position-absolute img-fluid w-100 h-100" src="<?php echo base_url('public/img/expertis.jpg'); ?>" style="object-fit: cover;" alt="grupos de sellos de goma fechadores">
                        <div class="position-absolute top-0 end-0 mt-n4 me-n4 py-4 px-5" style="background: rgba(0, 0, 0, .08);">
                            <h1 class="display-4 text-white mb-0">10 <span class="fs-4">Años de</span></h1>
                            <h4 class="text-white">Experiencia</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h6 class="text-primary text-uppercase">// Sobre nosotros //</h6>
                    <h1 class="mb-4"><span class="text-primary">Sellos de Goma</span> Personalizados y duraderos</h1>
                    <p class="mb-4">Nuestra dedicación a la calidad, la personalización y el servicio excepcional nos distingue como tu mejor opción en el mercado local.</p>
                    <div class="row g-4 mb-3 pb-3">
                        <div class="col-12 wow fadeIn" data-wow-delay="0.1s">
                            <div class="d-flex">
                                <div class="bg-light d-flex flex-shrink-0 align-items-center justify-content-center mt-1" style="width: 45px; height: 45px;">
                                    <span class="fw-bold text-secondary">01</span>
                                </div>
                                <div class="ps-3">
                                    <h6>Garantía de Calidad</h6>
                                    <span>Sellos de goma duraderos y de alto rendimiento, fabricados con materiales de primera calidad y tecnología avanzada</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 wow fadeIn" data-wow-delay="0.3s">
                            <div class="d-flex">
                                <div class="bg-light d-flex flex-shrink-0 align-items-center justify-content-center mt-1" style="width: 45px; height: 45px;">
                                    <span class="fw-bold text-secondary">02</span>
                                </div>
                                <div class="ps-3">
                                    <h6>Envío Rápido y Seguro</h6>
                                    <span>Opciones de envío rápido y confiable para que recibas tus sellos de goma en perfectas condiciones y en tiempo récord.</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 wow fadeIn" data-wow-delay="0.5s">
                            <div class="d-flex">
                                <div class="bg-light d-flex flex-shrink-0 align-items-center justify-content-center mt-1" style="width: 45px; height: 45px;">
                                    <span class="fw-bold text-secondary">03</span>
                                </div>
                                <div class="ps-3">
                                    <h6>Precios competitivos</h6>
                                    <span>Sellos de goma de alta calidad a precios accesibles, ofreciendo una excelente relación calidad-precio para tu inversión.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p>No importa cuál sea tu necesidad, en Sellos Celaya tenemos el sello de goma perfecto para ti. ¡Contáctanos hoy mismo y descubre cómo podemos ayudarte a plasmar tu creatividad en cada impresión!</p>
                    <a href="https://wa.link/toiw92" class="btn btn-primary py-3 px-5">Queiro mas información<i class="fa fa-arrow-right ms-3"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->


    <!-- Fact Start -->
    <!-- <div class="container-fluid fact bg-dark my-5 py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6 col-lg-3 text-center wow fadeIn" data-wow-delay="0.1s">
                    <i class="fa fa-check fa-2x text-white mb-3"></i>
                    <h2 class="text-white mb-2" data-toggle="counter-up">1234</h2>
                    <p class="text-white mb-0">Years Experience</p>
                </div>
                <div class="col-md-6 col-lg-3 text-center wow fadeIn" data-wow-delay="0.3s">
                    <i class="fa fa-users-cog fa-2x text-white mb-3"></i>
                    <h2 class="text-white mb-2" data-toggle="counter-up">1234</h2>
                    <p class="text-white mb-0">Expert Technicians</p>
                </div>
                <div class="col-md-6 col-lg-3 text-center wow fadeIn" data-wow-delay="0.5s">
                    <i class="fa fa-users fa-2x text-white mb-3"></i>
                    <h2 class="text-white mb-2" data-toggle="counter-up">1234</h2>
                    <p class="text-white mb-0">Satisfied Clients</p>
                </div>
                <div class="col-md-6 col-lg-3 text-center wow fadeIn" data-wow-delay="0.7s">
                    <i class="fa fa-car fa-2x text-white mb-3"></i>
                    <h2 class="text-white mb-2" data-toggle="counter-up">1234</h2>
                    <p class="text-white mb-0">Compleate Projects</p>
                </div>
            </div>
        </div>
    </div>-->
    
    <!-- Fact End -->


    <!-- Service Start -->
    <div class="container-xxl service py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="text-primary text-uppercase">// Nuestro Trabajo //</h6>
                <h1 class="mb-5">Explora tus Opciones</h1>
            </div>
            <div class="row g-4 wow fadeInUp" data-wow-delay="0.3s">
                <div class="col-lg-4">
                    <div class="nav w-100 nav-pills me-4">
                        <button class="nav-link w-100 d-flex align-items-center text-start p-4 mb-4 active" data-bs-toggle="pill" data-bs-target="#tab-pane-1" type="button">
                            <i class="fa fa-tint fa-2x me-3"></i>
                            <h4 class="m-0">Autoentitables</h4>
                        </button>
                        <button class="nav-link w-100 d-flex align-items-center text-start p-4 mb-4" data-bs-toggle="pill" data-bs-target="#tab-pane-2" type="button">
                            <i class="fa fa-tree fa-2x me-3"></i>
                            <h4 class="m-0">En Madera</h4>
                        </button>
                        <button class="nav-link w-100 d-flex align-items-center text-start p-4 mb-4" data-bs-toggle="pill" data-bs-target="#tab-pane-3" type="button">
                            <i class="fa fa-clock fa-2x me-3"></i>
                            <h4 class="m-0">Fechadores</h4>
                        </button>
                        <button class="nav-link w-100 d-flex align-items-center text-start p-4 mb-0" data-bs-toggle="pill" data-bs-target="#tab-pane-4" type="button">
                            <i class="fa fa-shower fa-2x me-3"></i>
                            <h4 class="m-0">Para Tela</h4>
                        </button>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="tab-content w-100">
                        <div class="tab-pane fade show active" id="tab-pane-1">
                            <div class="row g-4">
                                <div class="col-md-6" style="min-height: 350px;">
                                    <div class="position-relative h-100">
                                        <img class="position-absolute img-fluid w-100 h-100" src="<?php echo base_url('public/img/autoentintable.jpg'); ?>"
                                            style="object-fit: cover;" alt="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- autoentintables -->
                                    <h3 class="mb-3">Eficiencia y Comodidad</h3>
                                    <p class="mb-4">Nuestros sellos autoentintables ofrecen una solución conveniente y eficiente, ya que eliminan la necesidad de utilizar almohadillas de tinta externas. Con un diseño integrado que recarga automáticamente la tinta después de cada impresión, nuestros sellos autoentintables son ideales para un uso rápido y sin complicaciones, lo que ahorra tiempo y esfuerzo en cada tarea de estampado.</p>
                                    <p><i class="fa fa-check text-success me-3"></i>Rapidez y Precisión</p>
                                    <p><i class="fa fa-check text-success me-3"></i>Limpieza y Orden</p>
                                    <p><i class="fa fa-check text-success me-3"></i>Ergonomía de Diseño</p>
                                    <a href="https://wa.link/gcydqc" target="_blank" class="btn btn-primary py-3 px-5 mt-3">Quiero uno<i class="fa fa-arrow-right ms-3"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-pane-2">
                            <div class="row g-4">
                                <div class="col-md-6" style="min-height: 350px;">
                                    <div class="position-relative h-100">
                                        <img class="position-absolute img-fluid w-100 h-100" src="<?php echo base_url('public/img/madera.jpg'); ?>"
                                            style="object-fit: cover;" alt="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- madera -->
                                    <h3 class="mb-3">Los Clásicos de siempre</h3>
                                    <p class="mb-4"> La madera de calidad proporciona una base resistente para el sello, lo que garantiza una impresión clara y nítida en cada uso. Además, los sellos de madera tienden a ser más duraderos que otros tipos de sellos.La madera es un material natural y renovable, lo que hace que los sellos de madera sean una opción ecológica y respetuosa con el medio ambiente. </p>
                                    <p><i class="fa fa-check text-success me-3"></i>Durables</p>
                                    <p><i class="fa fa-check text-success me-3"></i>Gran calidad de estampado</p>
                                    <p><i class="fa fa-check text-success me-3"></i>100% personalizados</p>
                                    <a href="https://wa.link/6uydwx" target="_blank" class="btn btn-primary py-3 px-5 mt-3">Quiero uno<i class="fa fa-arrow-right ms-3"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-pane-3">
                            <div class="row g-4">
                                <div class="col-md-6" style="min-height: 350px;">
                                    <div class="position-relative h-100">
                                        <img class="img-fluid w-100 h-100" src="<?php echo base_url('public/img/fechadores.jpg'); ?>"
                                            style="object-fit: cover;" alt="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- fechadores -->
                                    <h3 class="mb-3">Organización y Registro</h3>
                                    <p class="mb-4"> Los sellos de goma fechadores ofrecen una manera rápida y eficiente de marcar documentos, correspondencia o registros con fechas específicas, lo que facilita el seguimiento de la temporalidad de eventos, transacciones o procesos en una organización o empresa.</p>
                                    <p><i class="fa fa-check text-success me-3"></i>Registro preciso</p>
                                    <p><i class="fa fa-check text-success me-3"></i>Eficiencia en Documentación</p>
                                    <p><i class="fa fa-check text-success me-3"></i>Organizacion y elegancia</p>
                                    <a href="https://wa.link/1l85w2" target="_blank" class="btn btn-primary py-3 px-5 mt-3">Quiero uno<i class="fa fa-arrow-right ms-3"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-pane-4">
                            <div class="row g-4">
                                <div class="col-md-6" style="min-height: 350px;">
                                    <div class="position-relative h-100">
                                        <img class="position-absolute img-fluid w-100 h-100" src="<?php echo base_url('public/img/tela.jpg'); ?>"
                                            style="object-fit: cover;" alt="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- tela-->
                                    <h3 class="mb-3">No más ropa perdida</h3>
                                    <p class="mb-4">"¡Haz que la vida con tu bebé sea más fácil y divertida! Con nuestros adorables sellos para ropa del bebé, marcar sus prendas nunca ha sido tan sencillo. Olvídate de las etiquetas cosidas que se desprenden y las planchas que queman. Con nuestros sellos, simplemente estampa y listo. ¡Personaliza su ropita con estilo y deja tu marca en cada aventura!"</p>
                                    <p><i class="fa fa-check text-success me-3"></i>Identificación Personalizada</p>
                                    <p><i class="fa fa-check text-success me-3"></i>Durabilidad y Seguridad</p>
                                    <p><i class="fa fa-check text-success me-3"></i>Hasta 800 estampaciones</p>
                                    <a href="https://wa.link/9wprzd" target="_blank" class="btn btn-primary py-3 px-5 mt-3">Quiero uno<i class="fa fa-arrow-right ms-3"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->
    <!-- Sellos gigantes -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 pt-4" style="min-height: 400px;">
                    <div class="position-relative h-100 wow fadeIn" data-wow-delay="0.1s">
                        <img class="position-absolute img-fluid w-100 h-100" src="<?php echo base_url('public/img/gigante.jpg'); ?>" style="object-fit: cover;" alt="">
                        <div class="position-absolute top-0 end-0 mt-n4 me-n4 py-4 px-5" style="background: rgba(0, 0, 0, .08);">
                            <h1 class="display-4 text-white mb-0">Por que.. <span class="fs-4">Grande es</span></h1>
                            <h4 class="text-white">MEJOR</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h6 class="text-primary text-uppercase">// Sellos Muy Grandes //</h6>
                    <h1 class="mb-4"><span class="text-primary">Sellos de Goma</span> De Gran Tamaño</h1>
                    <p class="mb-4">"¿Quieres hacer una impresión tan grande como tu creatividad? ¡Entonces nuestros sellos de goma de gran formato son tu mejor opción! Desde cajas de piza audaces hasta carteles gigantes, nuestros sellos te permiten dejar tu marca en grande y con estilo. ¡Haz que tus ideas cobren vida en proporciones épicas con nuestros sellos de goma de gran formato! Es hora de ¡pensar a lo grande y estampar aún más grande!"</p>
                    <div class="row g-4 mb-3 pb-3">
                        <div class="col-12 wow fadeIn" data-wow-delay="0.1s">
                            <div class="d-flex">
                                <div class="bg-light d-flex flex-shrink-0 align-items-center justify-content-center mt-1" style="width: 45px; height: 45px;">
                                    <span class="fw-bold text-secondary">01</span>
                                </div>
                                <div class="ps-3">
                                    <h6>Sello de 15 x 10 cm</h6>
                                    <span>Rectangular ideal para cajas de empaque</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 wow fadeIn" data-wow-delay="0.3s">
                            <div class="d-flex">
                                <div class="bg-light d-flex flex-shrink-0 align-items-center justify-content-center mt-1" style="width: 45px; height: 45px;">
                                    <span class="fw-bold text-secondary">02</span>
                                </div>
                                <div class="ps-3">
                                    <h6>Sello de 15 x 15 cm</h6>
                                    <span>Cuadrado ideal para cajas de pizza</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 wow fadeIn" data-wow-delay="0.5s">
                            <div class="d-flex">
                                <div class="bg-light d-flex flex-shrink-0 align-items-center justify-content-center mt-1" style="width: 45px; height: 45px;">
                                    <span class="fw-bold text-secondary">03</span>
                                </div>
                                <div class="ps-3">
                                    <h6>Sello de 20 x 20 cm</h6>
                                    <span>Ideal tambien para cajas de pizza, cajas de regalo y playeras</span>
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

    <!-- Booking Start -->
    <div class="container-fluid bg-secondary booking my-5 wow fadeInUp" data-wow-delay="0.1s" id="contact">
        <div class="container">
            <div class="row gx-5">
                <div class="col-lg-6 py-5">
                    <div class="py-5">
                        <h1 class="text-white mb-4">En Sellos Celaya, nos enorgullece ofrecer una experiencia incomparable al adquirir tus sellos de goma y productos de marcado en Celaya, Gto.</h1>
                        <p class="text-white mb-0">En Sellos Celaya, te ofrecemos la posibilidad de crear sellos únicos que reflejen tu estilo y personalidad. Desde sellos para tu negocio hasta sellos para bodas o eventos especiales, tenemos la solución perfecta para que dejes una huella imborrable en todo lo que hagas.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="bg-primary h-100 d-flex flex-column justify-content-center text-center p-5 wow zoomIn" data-wow-delay="0.6s">
                        <h1 class="text-white mb-4">Contáctanos para mas información</h1>
                        <form method="post" action="<?php echo base_url('contacto');?>">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="form-control border-0" placeholder="Nombre" style="height: 55px;" name="nombre">
                                </div>
                                <div class="col-12 col-sm-6">
                                    <input type="email" class="form-control border-0" placeholder="Email" style="height: 55px;" name="correo">
                                </div>
                                <div class="col-12">
                                    <textarea class="form-control border-0" placeholder="Escribe aqui tu consulta" name="texto"></textarea>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-secondary w-100 py-3" type="submit">Enviar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Booking End -->


    <!-- Team Start  <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="text-primary text-uppercase">// Our Technicians //</h6>
                <h1 class="mb-5">Our Expert Technicians</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="team-item">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid" src="img/team-1.jpg" alt="">
                            <div class="team-overlay position-absolute start-0 top-0 w-100 h-100">
                                <a class="btn btn-square mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square mx-1" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square mx-1" href=""><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                        <div class="bg-light text-center p-4">
                            <h5 class="fw-bold mb-0">Full Name</h5>
                            <small>Designation</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="team-item">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid" src="img/team-2.jpg" alt="">
                            <div class="team-overlay position-absolute start-0 top-0 w-100 h-100">
                                <a class="btn btn-square mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square mx-1" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square mx-1" href=""><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                        <div class="bg-light text-center p-4">
                            <h5 class="fw-bold mb-0">Full Name</h5>
                            <small>Designation</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="team-item">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid" src="img/team-3.jpg" alt="">
                            <div class="team-overlay position-absolute start-0 top-0 w-100 h-100">
                                <a class="btn btn-square mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square mx-1" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square mx-1" href=""><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                        <div class="bg-light text-center p-4">
                            <h5 class="fw-bold mb-0">Full Name</h5>
                            <small>Designation</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="team-item">
                        <div class="position-relative overflow-hidden">
                            <img class="img-fluid" src="img/team-4.jpg" alt="">
                            <div class="team-overlay position-absolute start-0 top-0 w-100 h-100">
                                <a class="btn btn-square mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-square mx-1" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-square mx-1" href=""><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                        <div class="bg-light text-center p-4">
                            <h5 class="fw-bold mb-0">Full Name</h5>
                            <small>Designation</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>-->
   
    <!-- Team End -->

    <!-- Testimonial Start -->
    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <div class="text-center">
                <h6 class="text-primary text-uppercase">// Recomendados //</h6>
                <h1 class="mb-5">Nuestros Clientes Opinan!!</h1>
            </div>
            <div class="owl-carousel testimonial-carousel position-relative">
                <div class="testimonial-item text-center">
                    <img class="bg-light rounded-circle p-2 mx-auto mb-3" src="" style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Leopoldo</h5>
                    <p></p>
                    <div class="testimonial-text bg-light text-center p-4">
                    <p class="mb-0">Excelente servicio, me mandaron varios diseños sobre mi prototipo el tiempo de entrega fue de un día te mantienen informado sobre tu pedido</p>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="bg-light rounded-circle p-2 mx-auto mb-3" src="" style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Karen</h5>
                    <p></p>
                    <div class="testimonial-text bg-light text-center p-4">
                    <p class="mb-0">Trabajo muy bien hecho, la atención es muy buena. Recomendado al 100%.Gracias por mis sellos. Me encantaron.</p>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="bg-light rounded-circle p-2 mx-auto mb-3" src="" style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Judith</h5>
                    <p></p>
                    <div class="testimonial-text bg-light text-center p-4">
                    <p class="mb-0">Muy contenta con su excelente atención, trabajo impecable y rapidez en la entrega. Lo recomiendo al 100%</p>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="bg-light rounded-circle p-2 mx-auto mb-3" src="" style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Sego</h5>
                    <p></p>
                    <div class="testimonial-text bg-light text-center p-4">
                    <p class="mb-0">Excelente servicio... solicite la información a las 3:00 pm y a las 7:00 pm ya me lo entregaron... y súper bonito el diseño.. gracias !súper recomendable!!</p>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="bg-light rounded-circle p-2 mx-auto mb-3" src="" style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Alejandra</h5>
                    <p></p>
                    <div class="testimonial-text bg-light text-center p-4">
                    <p class="mb-0">Excelente servicio, desde la atención hasta la entrega del mismo lo pedí el mismo día por la mañana y ya lo tenía en mis manos por la tarde, excelente trabajo y servicio, totalmente recomendable y lo mejor de todo tienen entrega a domicilio.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->
<?php echo $this->endSection() ?>
    

