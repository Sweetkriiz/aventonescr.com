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

    <div class="d-flex align-items-center">
      <a href="verificar_publicacion.php" class="btn btn-success me-3">
        <i class="bi bi-plus-circle"></i> Publicar viaje
      </a>

      <?php if (!isset($_SESSION['user_id'])): ?>
        <!-- Usuario NO logueado -->
        <a href="login.php" class="btn btn-outline-light">Iniciar sesión</a>

      <?php else: ?>
        <!-- Usuario logueado -->
        <?php
          // Rol y nombre formateados
          $rol = ucfirst($_SESSION['rol'] ?? 'Usuario');
          $nombre = htmlspecialchars($_SESSION['usuario'] ?? 'Invitado');
          $textoUsuario = "$rol: $nombre";
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
            <li><a class="dropdown-item" href="perfil.php">Perfil</a></li>
            <li><a class="dropdown-item" href="mis_viajes.php">Mis Viajes</a></li>
            <li><a class="dropdown-item" href="vehiculos.php">Mis carros</a></li>
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
