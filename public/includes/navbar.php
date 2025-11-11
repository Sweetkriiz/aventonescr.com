<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<link rel="stylesheet" href="/css/navbar.css?v=7">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<nav class="navbar navbar-expand-lg shadow-sm navbar-unificado">
  <div class="container d-flex justify-content-between align-items-center">

    <!-- LOGO -->
   
    <a class="navbar-brand d-flex align-items-center" href="/index.php">
      <img src="/images/logo.png" alt="Logo Aventones" class="me-2 logo-navbar">
      <span class="fw-bold fs-4">Aventones CR</span>
    </a>

    <ul class="navbar-nav flex-row align-items-center ms-auto gap-3">

      <!-- BOTÓN DE INICIO -->
      <li class="nav-item">
        <a href="/index.php" class="btn btn-outline-light fw-semibold px-3">
          <i class="bi bi-house-door me-1"></i> Inicio
        </a>
      </li>

      <?php if (isset($_SESSION['user_id'])): ?>
        <?php
          $rol = ucfirst(strtolower($_SESSION['rol'] ?? 'Usuario'));
          $nombre = htmlspecialchars($_SESSION['usuario'] ?? 'Invitado');
        ?>
        
        <!-- MENÚ DESPLEGABLE -->
        
        <li class="nav-item dropdown">
          <button class="btn btn-outline-light dropdown-toggle fw-semibold px-3" type="button" id="dropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-list"></i> Menú
          </button>

          <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenu">

            <!-- OPCIONES COMUNES -->
            <li><h6 class="dropdown-header">Mi cuenta</h6></li>
            <li><a class="dropdown-item" href="/miPerfil.php"><i class="bi bi-person me-1"></i> Mi Perfil</a></li>
            <li><a class="dropdown-item" href="/mis_viajes.php"><i class="bi bi-calendar-check me-1"></i> Mis Viajes</a></li>
            <li><a class="dropdown-item" href="/index.php"><i class="bi bi-house-door me-1"></i> Inicio</a></li>

            <!-- SOLO CHOFER -->
            <?php if ($rol === 'Chofer'): ?>
              <li><hr class="dropdown-divider"></li>
              <li><h6 class="dropdown-header">Panel de Chofer</h6></li>
              <li><a class="dropdown-item" href="/dashboard_chofer.php"><i class="bi bi-speedometer2 me-1"></i> Panel de Chofer</a></li>
              <li><a class="dropdown-item" href="/dashboard_pasajero.php"><i class="bi bi-car-front me-1"></i> Panel de Pasajero</a></li>
            <?php endif; ?>

            <!-- SOLO PASAJERO -->
            <?php if ($rol === 'Pasajero'): ?>
              <li><hr class="dropdown-divider"></li>
              <li><h6 class="dropdown-header">Panel de Pasajero</h6></li>
              <li><a class="dropdown-item" href="/dashboard_pasajero.php"><i class="bi bi-car-front me-1"></i> Panel de Pasajero</a></li>
            <?php endif; ?>

            <!-- SOLO ADMIN -->
            <?php if ($rol === 'Administrador'): ?>
              <li><hr class="dropdown-divider"></li>
              <li><h6 class="dropdown-header">Gestión Interna </h6></li>
              
              <!-- Gestión interna -->
              <li><a class="dropdown-item" href="/dashboard_admin.php"><i class="bi bi-tools me-1"></i> Gestión de Vehículos</a></li>
              <li><a class="dropdown-item" href="../CRUD_admin/listar_usuarios.php"><i class="bi bi-people me-1"></i> Gestión de Usuarios</a></li>

              <li><hr class="dropdown-divider"></li>
              <li><h6 class="dropdown-header">Paneles de Vista</h6></li>
              <li><a class="dropdown-item" href="/dashboard_chofer.php"><i class="bi bi-speedometer2 me-1"></i> Panel de Chofer</a></li>
              <li><a class="dropdown-item" href="/dashboard_pasajero.php"><i class="bi bi-car-front me-1"></i> Panel de Pasajero</a></li>
            <?php endif; ?>


            <!-- SALIR -->
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="/logout.php"><i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión</a></li>
          </ul>
        </li>

      
        <!-- BLOQUE CONECTADO COMO -->
        
        <li class="nav-item d-flex flex-column text-end">
          <small class="text-light fw-light">Conectado como:</small>
          <span class="fw-semibold text-white"><?= $nombre ?></span>
          <small class="text-success"><?= $rol ?></small>
        </li>

      <?php else: ?>
        
        <!-- USUARIO NO LOGUEADO -->
                
        <li class="nav-item">
          <a href="/login.php" class="btn btn-outline-light fw-semibold px-3">
            <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>
