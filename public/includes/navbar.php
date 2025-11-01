<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="images/logo.png" alt="Logo Aventones" height="70" class="me-2">
      <span class="fw-bold fs-4" style="color: #198754;">Aventones CR</span>
    </a>

  

      <?php if (!isset($_SESSION['user_id'])): ?>
        <!-- Usuario NO logueado -->
        <a href="login.php" class="btn btn-outline-light">Iniciar sesión</a>

      <?php else: ?>
        <!-- Usuario LOGUEADO -->
        <?php
          $rol = strtolower($_SESSION['rol'] ?? 'usuario');
          $nombre = htmlspecialchars($_SESSION['usuario'] ?? 'Invitado');
          $textoUsuario = ucfirst($rol) . ": " . $nombre;
        ?>

        <div class="dropdown">
          <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center"
                  type="button" id="dropdownMenuButton"
                  data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle fs-5 me-2"></i>
            <?= $textoUsuario ?>
          </button>

          <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMenuButton">
            <li><h6 class="dropdown-header">Mi cuenta</h6></li>

            <!-- Opciones comunes -->
            <li><a class="dropdown-item" href="perfil.php"><i class="bi bi-person"></i> Mi perfil</a></li>
            <li><a class="dropdown-item" href="mis_viajes.php"><i class="bi bi-calendar-check"></i> Mis viajes</a></li>

            <?php if ($rol === 'pasajero'): ?>
              <!-- Opciones solo para pasajeros -->
              <li><a class="dropdown-item" href="vehiculos.php"><i class="bi bi-car-front"></i> Quiero ser chofer</a></li>
            <?php elseif ($rol === 'chofer'): ?>
              <!-- Opciones solo para chofer -->
              <li><a class="dropdown-item" href="dashboard_chofer.php"><i class="bi bi-speedometer2"></i> Panel de chofer</a></li>
            <?php endif; ?>

            <li><a class="dropdown-item" href="index.php"><i class="bi bi-house-door"></i> Inicio</a></li>
            <li><hr class="dropdown-divider"></li>

            <li>
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
