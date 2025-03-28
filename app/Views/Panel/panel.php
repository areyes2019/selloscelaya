<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link rel="stylesheet" href="<?php echo base_url('public/panel/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    
    <nav class="sidebar hidden" id="sidebar">
        <div class="sidebar-header">
            <h2>Menú</h2>
        </div>
        <ul>
            <li>
                <i class="fas fa-home"></i>
                <span>Inicio</span>
            </li>
            <li>
                <i class="fas fa-users"></i>
                <span>Usuarios</span>
            </li>
            <li>
                <i class="fas fa-chart-bar"></i>
                <span>Reportes</span>
            </li>
            <li>
                <i class="fas fa-cog"></i>
                <span>Configuración</span>
            </li>
        </ul>
    </nav>
    
    <main class="content" id="content">
        <div class="card">
            <h1>Panel de Control</h1>
            <p>Bienvenido al panel de administración.</p>
        </div>
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
</body>
</html>