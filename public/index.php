<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aventones - Panel Principal</title>
    <link rel="stylesheet" href="assets/style.css">
    
</head>
<body>

<header>
    <h1> Aventones CR</h1>
    <nav class="nav-bar">
        <span>Bienvenido a nuestra plataforma </span>
        <div class="nav-links">
        <a href="login.php">Iniciar Sesión</a>
        <a href="registrarse.php">Registrarse</a>
    </nav>
</header>

<div class="container text-center">
    <h2>Panel principal</h2>
    <p>Bienvenido al sistema de carpooling <strong>Aventones CR</strong>. Selecciona una opción para continuar:</p>
    
</div>
<div class="banner">
    ¡Comparte tu viaje y ahorra con Aventones!
  </div>

  <div class="search-box">
    <form action="login.php" method="get">
      <input type="text" name="origen" placeholder="Origen" required>
      <input type="text" name="destino" placeholder="Destino" required>
      <input type="date" name="fecha" required>
      <button type="submit">Buscar</button>
    </form>
  </div>



<footer>
    © 2025 Aventones CR
</footer>

</body>

</html>
