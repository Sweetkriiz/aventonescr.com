<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="../index.php">
      <img src="../images/logo.png" alt="Logo Aventones" height="70" class="me-2">
      <span class="fw-bold fs-4" style="color: #198754;">Aventones CR</span>
    </a>

 <div class="d-flex">
      <?php if (!isset($_SESSION['usuario_id'])): ?>
        <!-- Usuario no logueado -->
        <a href="../login.php" class="btn btn-success me-2">Publicar viaje</a>
        <a href="../login.php" class="btn btn-outline-light">Iniciar sesión</a>

      <?php else: ?>
        <!-- Usuario logueado -->
        <a href="../verificar_publicacion.php" class="btn btn-success me-2">Publicar viaje</a>
        <a href="../logout.php" class="btn btn-outline-light">Cerrar sesión</a>
      <?php endif; ?>
    </div>
  </div>
</nav>