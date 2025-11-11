<?php
session_start();

include('includes/navbar.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> Panel de Administrador - Aventones CR </title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/admin.css">

</head>
<body>

  <!-- Banner con imagen -->
<div style="position: relative; text-align: center; margin-bottom: 3rem;">
  <img src="../images/banner4.png" alt="Banner Administrador"
       style="width:100%; height:400px; object-fit:cover; filter:brightness(60%);">
  
  <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); color:#fff;">
    <h1 style="font-weight:600; font-size:2.5rem;">Bienvenido, <?= htmlspecialchars($_SESSION["usuario"] ?? "administrador")?>!</h1>
    <h4 style="font-weight:400;">Gestiona usuarios, solicitudes de choferes y viajes registrados en el sistema.</h4>
  </div>
</div>
    <div class="row justify-content-center g-4">
      <!-- Usuarios -->
      <div class="col-md-3">
        <div class="card p-4">
          <div class="text-success mb-3">
            <i class="bi bi-people-fill fs-1"></i>
          </div>
          <h5 class="fw-bold">Usuarios</h5>
          <p>Gestioná, edita o elimina usuarios del sistema.</p>
          <a href="CRUD_admin/listar_usuarios.php" class="btn btn-success btn-custom w-100">Ver Usuarios</a>
        </div>
      </div>

      <!-- Solicitudes de Choferes -->
      <div class="col-md-3">
        <div class="card p-4">
          <div class="text-warning mb-3">
            <i class="bi bi-person-badge-fill fs-1"></i>
          </div>
          <h5 class="fw-bold">Solicitudes de Choferes</h5>
          <p>Revisá y aprobá los vehículos registrados por choferes.</p>
          <a href="procesarSolicitudes.php" class="btn btn-warning btn-custom w-100 text-white">Ver Solicitudes</a>
        </div>
      </div>

      <!-- Viajes -->
      <div class="col-md-3">
        <div class="card p-4">
          <div class="text-primary mb-3">
            <i class="bi bi-map-fill fs-1"></i>
          </div>
          <h5 class="fw-bold">Viajes</h5>
          <p>Consulta todos los viajes publicados por los choferes.</p>
          <a href="listar_ride.php" class="btn btn-primary btn-custom w-100">Ver Viajes</a>
        </div>
      </div>
    </div>
  </div>

  <footer class="text-center py-3 bg-dark text-white mt-5">
  © 2025 Aventones CR | Panel del Administración
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
