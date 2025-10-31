<?php
session_start();

// Verificación de sesión
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "chofer") {
  header("Location: ../login.php");
  exit();
}

include('../includes/navbar.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel del Chofer - Aventones CR</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Custom -->
  <link rel="stylesheet" href="../css/index.css">
</head>

<body style="font-family: 'Poppins', sans-serif; background-color: #f8f9fa;">

<div class="container py-5">

  <!-- Título -->
  <div class="text-center mb-5">
    <h1 class="fw-bold text-success">🚘 Panel del Chofer</h1>
    <p class="text-muted">Bienvenido, <?php echo htmlspecialchars($_SESSION["usuario"] ?? "Chofer"); ?>.  
    Aquí puedes administrar tus vehículos, viajes y solicitudes.</p>
  </div>

  <!-- Estadísticas rápidas -->
  <div class="row g-4 mb-5">
    <div class="col-md-4">
      <div class="card shadow-sm border-0 text-center">
        <div class="card-body">
          <h5 class="card-title text-success fw-bold">Mis Vehículos</h5>
          <p class="card-text text-muted">Administra tus autos registrados.</p>
          <a href="vehiculos.php" class="btn btn-success">Ver vehículos</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm border-0 text-center">
        <div class="card-body">
          <h5 class="card-title text-success fw-bold">Mis Viajes</h5>
          <p class="card-text text-muted">Publica, edita o elimina tus viajes.</p>
          <a href="viajes.php" class="btn btn-success">Gestionar viajes</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow-sm border-0 text-center">
        <div class="card-body">
          <h5 class="card-title text-success fw-bold">Solicitudes</h5>
          <p class="card-text text-muted">Revisa las solicitudes de los pasajeros.</p>
          <a href="solicitudes.php" class="btn btn-success">Ver solicitudes</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Sección informativa -->
  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <h4 class="fw-bold text-success mb-3">📋 Consejos para choferes</h4>
      <ul>
        <li>Mantené actualizada la información de tus vehículos y tus rutas.</li>
        <li>Podés eliminar viajes vencidos desde la opción “Mis viajes”.</li>
        <li>Revisá frecuentemente las solicitudes pendientes para no perder pasajeros.</li>
        <li>Recordá: la amabilidad y puntualidad aumentan tu calificación ⭐.</li>
      </ul>
    </div>
  </div>

</div>

<!-- Footer -->
<footer class="text-center py-3 bg-dark text-white mt-5">
  © 2025 Aventones CR | Panel del Chofer
</footer>

</body>
</html>
