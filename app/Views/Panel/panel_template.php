<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>


    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" 
          crossorigin="anonymous">
    <script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
    crossorigin="anonymous"></script>
    <!--  Data Tables -->
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <script src="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.js" integrity="sha384-2Ul6oqy3mEjM7dBJzKOck1Qb/mzlO+k/0BQv3D3C7u+Ri9+7OBINGa24AeOv5rgu" crossorigin="anonymous"></script>

    <!-- Vue DataTablesGrid -->
    <script src="https://unpkg.com/vue-datagrid@latest/dist/vue-datagrid.umd.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/vue-datagrid@latest/dist/vue-datagrid.css">

    <!-- Herramientas adicionales -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.0/FileSaver.min.js"></script>

    <!-- Vue y Axios -->
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <!--  Vue select -->
    <script src="https://unpkg.com/vue@latest"></script>

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?= base_url('public/panel/css/style.css'); ?>">

    <!--  selec2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
    <header class="header">
        <div class="header-title">Panel de Control</div>
                <div class="user-dropdown">
            <button class="user-btn">
                <span class="username"><?= session()->get('username') ?? 'Usuario' ?></span>
                <i class="bi bi-person-circle"></i>
            </button>
            <div class="dropdown-content">
                <a href="<?= base_url('perfil') ?>"><i class="bi bi-person"></i> Mi perfil</a>
                <a href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
            </div>
        </div>
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
        <!-- Contenedor con scroll -->
        <div class="sidebar-menu-container">
            <ul class="sidebar-menu">
                <!-- Tus elementos del menú actual aquí -->
                <li>
                    <a href="<?php echo base_url('admin')?>"><i class="bi bi-house"></i>
                    <span>Inicio</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('ventas/pos')?>"><i class="bi bi-cash-coin"></i>
                    <span>POS</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('clientes'); ?>">
                    <i class="bi bi-person"></i>
                    <span>Clientes</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('proveedores'); ?>">
                    <i class="bi bi-people"></i>
                    <span>Proveedores</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('articulos'); ?>">
                    <i class="bi bi-box2"></i>
                    <span>Articulos</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('cotizaciones'); ?>">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <span>Cotizaciones</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('compras'); ?>">
                    <i class="bi bi-file-earmark-plus"></i>
                    <span>Ordenes de Compra</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('administracion'); ?>">
                    <i class="bi bi-file-earmark-plus"></i>
                    <span>Panel de Trabajo</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('ordenes'); ?>">
                    <i class="bi bi-gear"></i>
                    <span>Ordenes de Trabajo</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('existencias/existencias_admin'); ?>">
                    <i class="bi bi-bar-chart"></i>
                    <span>Existencias</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('reportes/reporte'); ?>">
                    <i class="bi bi-coin"></i>
                    <span>Finansas</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('categorias/'); ?>">
                    <i class="bi bi-shop"></i>
                    <span>Catálogo</span>
                    </a>
                </li>                    
            </ul>
        </div>
        <div class="user-panel">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="user-details">
                    <span class="user-name"><?= session()->get('username') ?? 'Usuario' ?></span>
                    <span class="user-email"><?= session()->get('email') ?></span>
                </div>
            </div>
            <a href="<?= base_url('logout') ?>" class="logout-btn" title="Cerrar sesión">
                <i class="bi bi-box-arrow-right"></i>
                <span class="logout-text">Salir</span>
            </a>
        </div>
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
    <script type="text/javascript"src="<?php echo base_url('/public/js/notify.js'); ?>"></script>
</body>
</html>