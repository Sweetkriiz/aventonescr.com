<?php
session_start();
require_once __DIR__ . '/../config/funciones_carro.php';


include('includes/navbar.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel del Chofer - Aventones CR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/chofer.css">
</head>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<body style="font-family: 'Poppins', sans-serif; background-color: #f8f9fa;">

<!-- Banner con imagen -->
<div style="position: relative; text-align: center; margin-bottom: 3rem;">
  <img src="../images/banner3.png" alt="Banner Chofer"
       style="width:100%; height:400px; object-fit:cover; filter:brightness(60%);">
  
  <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); color:#fff;">
    <h1 style="font-weight:600; font-size:2.5rem;">¡Hola, <?= htmlspecialchars($_SESSION['nombreUsuario'] ?? 'Chofer') ?>!</h1>
    <h4 style="font-weight:400;">Gestioná tus vehículos, rides y solicitudes de forma sencilla.</h4>
  </div>
</div>

<!-- Título centrado sin negrita -->
<div class="text-center mb-5">
  <h2 class="text-success" style="font-weight:400; font-size:2rem;">Panel del Chofer</h2>
</div>


  <!-- Tarjetas principales -->
  <div class="row g-4">

    <!-- Tarjeta: Mis Vehículos -->
    <div class="col-md-4">
      <div class="card text-center shadow-sm border-0 h-100 p-3">
        <i class="bi bi-car-front text-success fs-1 mb-3"></i>
        <h5 class="fw-bold">Mis Vehículos</h5>
        <p class="text-muted">Agregá, editá o eliminá tus vehículos registrados.</p>
        <a href="CRUD_vehiculos/listar_vehiculo.php" class="btn btn-success">Ir a Mis Vehículos</a>
      </div>
    </div>

    <!-- Tarjeta: Mis Rides -->
    <div class="col-md-4">
      <div class="card text-center shadow-sm border-0 h-100 p-3">
        <i class="bi bi-map text-primary fs-1 mb-3"></i>
        <h5 class="fw-bold">Mis Rides</h5>
        <p class="text-muted">Publicá y administrá los viajes con tus pasajeros.</p>
        <a href="CRUD_rides/listar_ride.php" class="btn btn-primary">Ver Rides</a>
      </div>
    </div>
    
    <!-- Tarjeta: Solicitudes -->
    <div class="col-md-4">
      <div class="card text-center shadow-sm border-0 h-100 p-3">
        <i class="bi bi-person-check text-warning fs-1 mb-3"></i>
        <h5 class="fw-bold">Solicitudes</h5>
        <p class="text-muted">Aceptá o rechazá las reservas de tus pasajeros.</p>
        <a href="solicitudes_chofer.php" class="btn btn-warning text-dark">Ver Solicitudes</a>
      </div>
    </div>
  </div>

  <!-- Sección informativa -->
  <div class="card border-0 shadow-lg rounded-4 mt-5 consejos">
    <div class="card-body p-4">
      <h4 class="fw-bold text-success mb-3">
        <i class="bi bi-lightbulb-fill text-warning"></i> Consejos para Choferes
      </h4>
      <ul class="list-group list-group-flush">
        <li class="list-group-item"> Mantené la información de tus vehículos al día.</li>
        <li class="list-group-item"> Revisá solicitudes con frecuencia.</li>
        <li class="list-group-item"> Confirmá viajes con tiempo para evitar cancelaciones.</li>
        <li class="list-group-item"> La puntualidad y amabilidad aumentan tus calificaciones.</li>
      </ul>
    </div>
  </div>
</div>

<footer class="text-center py-3 bg-dark text-white mt-5">
  © 2025 Aventones CR | Panel del Chofer
</footer>

</body>
</html>