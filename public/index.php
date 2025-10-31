<?php
session_start();
include('includes/navbar.php');


?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aventones CR</title>
  <!-- css -->
  <link rel="stylesheet" href="../css/custom.css">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Poppins', sans-serif; }
    .form-glass {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(6px);
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      padding: 30px;
    }
  </style>
</head>
<body class="bg-light">

  <!-- Carrusel -->
  <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="../images/banner1.jpg" class="d-block w-100" alt="...">
      </div>
      <div class="carousel-item">
        <img src="../images/banner2.jpg" class="d-block w-100" alt="...">
      </div>
      <div class="carousel-item">
        <img src="../images/banner3.jpg" class="d-block w-100" alt="...">
      </div>
       <div class="carousel-item">
        <img src="../images/banner4.jpg" class="d-block w-100" alt="...">
      </div>
    </div>
  </div>

<!-- FRASE EN LA ESQUINA DERECHA -->
<div class="position-absolute text-end" 
     style="top: 120px; right: 40px; z-index: 20;">
  <h2 class="fw-bold text-white text-shadow display-5 mb-2">
    ¿VAMOS EN LA MISMA DIRECCIÓN?
  </h2>
  <p class="text-white fs-5 text-shadow mb-0">
    Comparte tu viaje y únete a la comunidad Aventones 
  </p>
</div>



  <!-- Formulario flotante -->
  <div class="position-absolute start-50 translate-middle-x bottom-0 mb-5 w-75 form-glass">
    <form class="row g-3 justify-content-center">
      <div class="col-md-3">
        <label for="origen" class="form-label">Origen</label>
        <input type="text" class="form-control" id="origen" placeholder="Ej. San José">
      </div>
      <div class="col-md-3">
        <label for="destino" class="form-label">Destino</label>
        <input type="text" class="form-control" id="destino" placeholder="Ej. Cartago">
      </div>
      <div class="col-md-2">
        <label for="fecha" class="form-label">Fecha</label>
        <input type="date" class="form-control" id="fecha">
      </div>
      <div class="col-md-2">
        <label for="pasajeros" class="form-label">Pasajeros</label>
        <input type="number" class="form-control" id="pasajeros" min="1">
      </div>
      <div class="col-12 text-center">
        <button type="submit" class="btn btn-success mt-3 px-4">Buscar viaje</button>
      </div>
    </form>
  </div>

  <!-- Sección 3 pasos -->
  <section class="container text-center my-5">
    <h2 class="fw-bold mb-4">Reserva tu cupo en 3 pasos</h2>
    <div class="row gy-4">
      <div class="col-md-4">
        <img src="../images/paso1.svg" width="100" alt="">
        <h4>Busca tu ruta</h4>
        <p>Encuentra quién te puede llevar. Si no hay viajes disponibles, publica tu búsqueda y aumenta tus posibilidades.</p>
      </div>
      <div class="col-md-4">
        <img src="../images/paso2.svg" width="100" alt="">
        <h4>Elige tu viaje</h4>
        <p>Revisa las opciones disponibles y escoge según horarios, contribución de gastos y calificación.</p>
      </div>
      <div class="col-md-4">
        <img src="../images/paso3.svg" width="100" alt="">
        <h4>Reserva tu cupo</h4>
        <p>Paga en línea, coordina tu recogida y viaja con total comodidad.</p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="text-center py-3 bg-dark text-white">
    © 2025 Aventones CR
  </footer>

</body>
</html>
