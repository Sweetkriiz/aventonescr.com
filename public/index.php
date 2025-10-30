<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Aventones - Panel Principal</title>
<link rel="stylesheet" href="../css/index.css">
</head>
<body>

<div class="page-container">

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <img src="../images/logo.png" alt="Aventones CR">
                <h1>Aventones CR</h1>
            </div>
            <div class="nav-buttons">
                <a href="publicar_viaje.php" class="btn">Publicar Viaje</a>
                <a href="login.php" class="btn">Iniciar Sesión</a>
            </div>
        </div>
    </header>

    <!-- HERO CON CARRUSEL -->
    <section class="hero">
        <div class="carousel-container">
            <img src="../images/banner1.jpg" class="carousel-image active" alt="Comparte tu viaje">
            <img src="../images/banner2.jpg" class="carousel-image" alt="Aventones CR">
            <img src="../images/banner3.jpg" class="carousel-image" alt="Viaja seguro">
            <img src="../images/banner4.jpg" class="carousel-image" alt="Comparte tu viaje">
        </div>
   

    <!-- FORMULARIO -->
    <div class="search-box-container">
        <form action="login.php" method="get" class="search-box">
            <input type="text" name="origen" placeholder="Origen" required>
            <input type="text" name="destino" placeholder="Destino" required>
            <input type="date" name="fecha" required>
            <input type="number" name="pasajeros" placeholder="Pasajeros" min="1" required>
            <button type="submit">Buscar</button>
        </form>
    </div>
    </section>

    <!-- SECCIÓN INFORMACION -->
    <section class="three-steps">
        <h2>Reserva tu cupo en 3 pasos</h2>
        <div class="steps-container">
            <div class="step">
                <img src="../imagenes/logo.png" alt="Logo paso 1">
                <h3>Busca tu ruta</h3>
                <p>Encuentra quién te puede llevar. Si no hay viajes disponibles, publica tu búsqueda y aumenta tus posibilidades de viajar.</p>
            </div>
            <div class="step">
                <img src="../imagenes/logo.png" alt="Logo paso 2">
                <h3>Elige tu viaje</h3>
                <p>Revisa las opciones disponibles y escoge según horarios, contribución de gastos, calificación, y puntos de salida y/o llegada.</p>
            </div>
            <div class="step">
                <img src="../imagenes/logo.png" alt="Logo paso 3">
                <h3>Reserva tu cupo</h3>
                <p>Paga en línea para asegurar tu viaje, coordina tu recogida y viaja con total comodidad.</p>
            </div>
        </div>
    </section>

 
</div>

<!-- JAVASCRIPT CARRUSEL -->
<script>
const images = document.querySelectorAll('.carousel-image');
let current = 0;

function nextImage() {
    images[current].classList.remove('active');
    current = (current + 1) % images.length;
    images[current].classList.add('active');
}

// Cambiar cada 3 segundos
setInterval(nextImage, 3000);
</script>

   <footer>
        © 2025 Aventones CR
    </footer>

</body>
</html>
