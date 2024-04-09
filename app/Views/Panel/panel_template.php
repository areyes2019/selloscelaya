<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo base_url('public/css/menu.css');?>">
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.3/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.3/datatables.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <title>Panel de control</title>
  </head>
  <body>
    
    <nav class="navbar navbar-expand-lg navbar-light my-nav">
      <div class="container-fluid">
        <img src="<?php echo base_url('public/img/LOGOciruclos3.png'); ?>" width="50" alt="">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0 my-ul">
            <li class="nav-item">
              <a class="nav-link" aria-current="page" href="<?php echo base_url('nueva_venta'); ?>">Nueva Venta</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Clientes
              </a>
              <ul class="dropdown-menu rounded-0" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="<?php echo base_url('clientes')?>">Lista</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Articulos
              </a>
              <ul class="dropdown-menu rounded-0" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="<?php echo base_url('articulos')?>">Lista</a></li>
              </ul>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Cotizaciones
              </a>
              <ul class="dropdown-menu rounded-0" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="<?php echo base_url('cotizaciones')?>">Lista</a></li>
                <li><a class="dropdown-item" href="<?php echo base_url('nueva_cotización'); ?>">Nueva Cotización</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container-fluid mt-3">
      <div id="app">
      <?php echo $this->renderSection('contenido') ?>  
      </div>
    </div>
    <!-- Optional JavaScript; choose one of the two! -->
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
      new DataTable('#example');
    </script>
    <script>
      jQuery(document).ready(function($){
          $(document).ready(function() {
              $('#articulo').select2();
          });
      });

      $('ul li').on('click', function() {
          $('li').removeClass('active');
          $(this).addClass('active');
      });
    </script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>