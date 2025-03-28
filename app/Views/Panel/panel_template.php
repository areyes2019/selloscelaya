<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link rel="stylesheet" href="<?php echo base_url('public/panel/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.3/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.3/datatables.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
    <link href="<?php echo base_url('public/css/toaster.css'); ?>" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="<?php echo base_url('public/js/html2Canvas.js'); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.0/FileSaver.min.js"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="<?php echo base_url('public/js/toaster.js'); ?>"></script>
</head>
<body>
    <header class="header">
        <div class="header-title">Panel de Control</div>
        <div class="menu-toggle-container">
            <button class="menu-toggle" onclick="toggleMenu()" aria-label="Toggle menu">
                <span id="menu-icon">☰</span>
            </button>
        </div>
    </header>
    
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>Sello Pronto</h2>
        </div>
        <ul>
            <li>
                <a href="<?php echo base_url('admin')?>"><i class="fas fa-home"></i>
                <span>Inicio</span>
                </a>
            </li>
            <li>
                <a href="<?php echo base_url('clientes'); ?>">
                <i class="fas fa-users"></i>
                <span>Clientes</span>
                </a>
            </li>
            <li>
                <a href="<?php echo base_url('proveedores'); ?>">
                <i class="fas fa-users"></i>
                <span>Proveedores</span>
                </a>
            </li>
            <li>
                <a href="<?php echo base_url('articulos'); ?>">
                <i class="fas fa-box"></i>
                <span>Articulos</span>
                </a>
            </li>
            <li>
                <a href="<?php echo base_url('cotizaciones'); ?>">
                <i class="fas fa-file"></i>
                <span>Cotizaciones</span>
                </a>
            </li>
            <li>
                <a href="<?php echo base_url('compras'); ?>">
                <i class="fas fa-file"></i>
                <span>Ordenes de Compra</span>
                </a>
            </li>
            <li>
                <a href="<?php echo base_url('existencias'); ?>">
                <i class="fas fa-database"></i>
                <span>Existencias</span>
                </a>
            </li>
            
        </ul>
    </nav>
    
    <main class="content" id="content">
        <?php echo $this->renderSection('contenido') ?>
    </main>

    <script>
        function toggleMenu() {
            const sidebar = document.getElementById("sidebar");
            const menuIcon = document.getElementById("menu-icon");
            
            sidebar.classList.toggle("hidden");
            sidebar.classList.toggle("visible");
            
            // Cambiar ícono según estado
            if (sidebar.classList.contains("hidden")) {
                menuIcon.textContent = '☰';
            } else {
                menuIcon.textContent = '×';
            }
        }
        
        // Manejar cambios de tamaño de pantalla
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById("sidebar");
            if (window.innerWidth > 768) {
                sidebar.classList.remove("hidden");
                sidebar.classList.remove("visible");
            } else {
                sidebar.classList.add("hidden");
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>