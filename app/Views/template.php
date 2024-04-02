
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sellos Celaya | Personaliza tu sello </title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description" content="Encuentra sellos de goma personalizados para tus necesidades comerciales o personales. Amplia variedad de diseños y tamaños disponibles. ¡Haz tu pedido ahora!">
    <meta name="keywords" content="sellos de goma, sellos personalizados, sellos para empresas, sellos para oficinas, sellos automáticos">
    <meta property="og:image" content="<?php echo base_url('public/img/20.png'); ?>" />
    <meta name="author" content="sellos celaya">
    <meta name="language" content="es">

    <!-- Favicon -->
    <link rel="icon" href="<?php echo base_url('/logo.ico')?>" type="image/x-icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@600;700&family=Ubuntu:wght@400;500&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="<?php echo base_url('public/lib/animate/animate.min.css'); ?> " rel="stylesheet">
    <link href="<?php echo base_url('public/lib/owlcarousel/assets/owl.carousel.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('public/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css'); ?>" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="<?php echo base_url('public/css/bootstrap.min.css'); ?>" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="<?php echo base_url('public/css/style.css'); ?>" rel="stylesheet">
</head>

<body>
<!-- Topbar Start -->
    <div class="container-fluid bg-light p-0">
        <div class="row gx-0 d-none d-lg-flex">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                    <small class="fa fa-map-marker-alt text-primary me-2"></small>
                    <small>Real del Seminario 122, Valle del Real</small>
                </div>
                <div class="h-100 d-inline-flex align-items-center py-3">
                    <small class="far fa-clock text-primary me-2"></small>
                    <small>Lun - Vie : 09.00 AM - 06.00 PM</small>
                </div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                    <small class="fa fa-phone-alt text-primary me-2"></small>
                    <small>461 358 1090</small>
                </div>
                <div class="h-100 d-inline-flex align-items-center">
                    <a class="btn btn-sm-square bg-white text-primary me-1" href="https://www.facebook.com/selloscelaya"><i class="fab fa-facebook-f"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->
     <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="<?php echo base_url('/'); ?>" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <img class="mr-3" src="<?php echo base_url('public/img/LOGOciruclos3.png'); ?>" alt="Sello Pronto"  width="50">
            <h2 class="m-0 text-primary">Sellos Celaya</h2>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="<?php echo base_url('/'); ?>" class="nav-item nav-link active">Inicio</a>
                <a href="<?php echo base_url('#whoweare'); ?>" class="nav-item nav-link">Nosotros</a>
                <a href="<?php echo base_url('#whyus'); ?>" class="nav-item nav-link">Por que elegirnos</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Nuestros Sellos de Goma</a>
                    <div class="dropdown-menu fade-up m-0">
                        <a href="<?php echo base_url('autoentitables'); ?>" class="dropdown-item">Autoentitables</a>
                        <a href="<?php echo base_url('madera'); ?>" class="dropdown-item">En Madera</a>
                        <a href="<?php echo base_url('fechadores'); ?>" class="dropdown-item">Fechadores</a>
                        <a href="<?php echo base_url('portatiles'); ?>" class="dropdown-item">Portátiles</a>
                        <a href="<?php echo base_url('textiles'); ?>" class="dropdown-item">Para ropa</a>
                        <a href="<?php echo base_url('gigantes'); ?>" class="dropdown-item">Gigantes</a>
                    </div>
                </div>
                <a href="<?php echo base_url('#contact'); ?>" class="nav-item nav-link">Contacto</a>
            </div>
            <a href="tel:4613581090" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Llamanos 461 358 1090<i class="fa fa-arrow-right ms-3"></i></a>
        </div>
    </nav>
    <!-- Navbar End -->
    <?= $this->renderSection('contenido') ?>
     <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Ubicación</h4>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Real del Seminario 122, Valle del Real. Celaya Mx</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>461 358 1090</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>ventas@sellopronto.com.mx</p>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3736.4827724092047!2d-100.8460488042762!3d20.527418891557925!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x842cbbc6aa2b0ba5%3A0x2f236980b32d188d!2sSellos%20Celaya!5e0!3m2!1ses-419!2smx!4v1711676634258!5m2!1ses-419!2smx" style="border:0;" allowfullscreen="no" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Horario</h4>
                    <h6 class="text-light">Monday - Friday:</h6>
                    <p class="mb-4">09.00 AM - 06.00 PM</p>
                    <h6 class="text-light">Saturday - Sunday:</h6>
                    <p class="mb-0">Descansamos</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="text-light mb-4">Sellos</h4>
                    <a class="btn btn-link" href="">Autoentitables</a>
                    <a class="btn btn-link" href="">Fechadores</a>
                    <a class="btn btn-link" href="">Montados en madera</a>
                    <a class="btn btn-link" href="">De Gran Formato</a>
                    <a class="btn btn-link" href="">Para Ropa</a>
                    <a class="btn btn-link" href="">Portáiles</a>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">Sellos Celaya</a>, Derechos reservados.

                        <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                        Diseño por <a class="border-bottom" href="https://selloscelaya">Sellos Celaya</a>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="footer-menu">
                            <a href="">Home</a>
                            <a href="">Cookies</a>
                            <a href="">Help</a>
                            <a href="">FQAs</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url('public/lib/wow/wow.min.js'); ?>"></script>
    <script src="<?php echo base_url('public/lib/easing/easing.min.js'); ?> "></script>
    <script src="<?php echo base_url('public/lib/waypoints/waypoints.min.js'); ?> "></script>
    <script src="<?php echo base_url('public/lib/counterup/counterup.min.js'); ?> "></script>
    <script src="<?php echo base_url('public/lib/owlcarousel/owl.carousel.min.js'); ?> "></script>
    <script src="<?php echo base_url('public/lib/tempusdominus/js/moment.min.js'); ?> "></script>
    <script src="<?php echo base_url('public/lib/tempusdominus/js/moment-timezone.min.js'); ?> "></script>
    <script src="<?php echo base_url('public/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js'); ?> "></script>

    <!-- Template Javascript -->
    <script src="<?php echo base_url('public/js/main.js'); ?> "></script>
</body>

</html>
