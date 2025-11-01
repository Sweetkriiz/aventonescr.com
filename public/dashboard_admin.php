<?php

require_once __DIR__ . '/../config/start_app.php';
require_once __DIR__ . '/../config/database.php';

//  Verificar si hay sesión activa y si el usuario es administrador
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== 'administrador') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cerrar sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

  <a href="logout.php" class="btn btn-danger btn-lg">
    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
  </a>

  <!-- Iconos de Bootstrap (opcional) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


