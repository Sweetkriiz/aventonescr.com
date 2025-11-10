<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}


?>


<link rel="stylesheet" href="/css/index.css?v=5">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<nav class="navbar navbar-expand-lg shadow-sm navbar-unificado">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="/index.php">
      <img src="/images/logo.png" alt="Logo Aventones" class="me-2 logo-navbar">
      <span class="fw-bold fs-4">Aventones CR</span>
    </a>

      <?php if (!isset($_SESSION['user_id'])): ?>
        <!-- Usuario NO logueado -->
        <a href="/login.php" class="btn btn-outline-light">Iniciar sesión</a>

      <?php else: ?>
        <!-- Usuario LOGUEADO -->
        <?php
          $rol = strtolower($_SESSION['rol'] ?? 'usuario');
          $nombre = htmlspecialchars($_SESSION['usuario'] ?? 'Invitado');
          $textoUsuario = ucfirst($rol) . ": " . $nombre;
        ?>

         <div class="dropdown">
        <button class="btn btn-principal dropdown-toggle d-flex align-items-center"
                type="button" id="dropdownMenuButton"
                data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-person-circle fs-5 me-2"></i>
          <?= $textoUsuario ?>
        </button>

            <!-- Opciones comunes -->
          <ul class="dropdown-menu dropdown-menu-end shadow">
          <li><h6 class="dropdown-header">Mi cuenta</h6></li>
          <li><a class="dropdown-item" href="/miPerfil.php"><i class="bi bi-person"></i> Mi perfil</a></li>
          <li><a class="dropdown-item" href="mis_viajes.php"><i class="bi bi-calendar-check"></i> Mis viajes</a></li>
            <!-- Opciones solo para pasajeros -->
            <?php if ($rol === 'pasajero'): ?>         
              <li><a class="dropdown-item" href="dashboard_pasajero.php"><i class="bi bi-car-front"></i> Panel de pasajeros</a></li>
              <li><hr class="dropdown-divider"></li>
            <!-- Opciones solo para chofer -->
            <?php elseif ($rol === 'chofer'): ?>
              <li><a class="dropdown-item" href="dashboard_chofer.php"><i class="bi bi-speedometer2"></i> Panel de chofer</a></li>
              <li><a class="dropdown-item" href="dashboard_pasajero.php"><i class="bi bi-car-front"></i> Panel de pasajeros</a></li>
              <!-- Opciones solo para admin -->
            <?php elseif ($rol === 'administrador'): ?>   
             <li><a class="dropdown-item" href="dashboard_admin.php"><i class="bi bi-speedometer2"></i> Panel de Admin</a></li>
            <?php endif; ?>
            <li><a class="dropdown-item" href="index.php"><i class="bi bi-house-door"></i> Inicio</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
            <!-- Salir -->
              <a class="dropdown-item text-danger" href="logout.php">
                <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
              </a>
            </li>
          </ul>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>
