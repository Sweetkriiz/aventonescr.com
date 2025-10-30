<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreUsuario = $_POST['nombreUsuario'];
    $contrasena = $_POST['contrasena'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];   
    $cedula = $_POST['cedula'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];

    // ⚠️ En producción, usar password_hash()
    $sql = "INSERT INTO Usuarios (nombreUsuario, contrasena, nombre, apellidos, cedula, fechaNacimiento, correo, telefono)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombreUsuario, $contrasena, $nombre, $apellidos, $cedula, $fechaNacimiento, $correo, $telefono]);

    echo "<p>Usuario registrado correctamente. <a href='views/login.php'>Iniciar sesión</a></p>";
}?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - Aventones CR</title>
  <link rel="stylesheet" href="css/registrarse.css">
</head>

<body>

  <nav class="navbar">
    <div class="navbar-left">
      <img src="images/logo.png" alt="Logo Aventones" class="navbar-logo">
      <span class="navbar-title">Aventones CR</span>
    </div>
    <div class="navbar-right">
      <button class="navbar-btn">Publicar un viaje</button>
    </div>
  </nav>


  <div class="register-container">
    <div class="image-side">
      <img src="images/login.png" alt="Registro Aventones">
    </div>

    <div class="form-side">
      <h2>Crea tu cuenta</h2>

      <form method="POST" action="">

        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" placeholder="Ingresa tu nombre" required>

        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" placeholder="Ingresa tus apellidos" required>

        <label for="cedula">Cédula</label>
        <input type="text" id="cedula" name="cedula" placeholder="Ingresa tú identificación" required>

        <label for="fechaNacimiento">Fecha de nacimiento</label>
        <input type="date" id="fechaNacimiento" name="fechaNacimiento" required>

        <label for="nombreUsuario">Nombre de usuario</label>
        <input type="text" id="nombreUsuario" name="nombreUsuario" placeholder="Elige un nombre de usuario" required>

        <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" name="correo" placeholder="email@example.com" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>

        <label for="contrasena">Confirmar Contraseña</label>
        <input type="password" id="contrasena" name="contrasena" placeholder="••••••••" required>

        <label for="telefono">Teléfono</label>
        <input type="tel" id="telefono" name="telefono" placeholder="Ej: 8888-8888" required>

        <button type="submit">Registrarme</button>
        
      </form>

      <p class="login-link">¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
    </div>
  </div>

<footer class="footer">
  <div class="footer-container">
    <!-- 🔹 Sección izquierda -->
    <div class="footer-left">
      <h4>Acerca</h4>
      <ul>
        <li><a href="#">Inicio</a></li>
        <li><a href="#">Acerca de nosotros</a></li>
        <li><a href="#">Contáctanos</a></li>
      </ul>
    </div>

    <!-- 🔹 Sección central -->
    <div class="footer-center">
      <img src="images/logo.png" alt="Logo Aventones" class="footer-logo">
    </div>

    <!-- 🔹 Sección derecha -->
    <div class="footer-right">
      <a href="#" class="social-icon facebook">f</a>
      <a href="#" class="social-icon instagram">📸</a>
    </div>
  </div>

  <!-- 🔹 Línea inferior -->
  <div class="footer-bottom">
    <p>©2025, Aventones CR</p>
    <div class="footer-links">
      <a href="#">Términos y Condiciones</a>
      <a href="#">Políticas de Privacidad</a>
    </div>
  </div>
</footer>
</body>
</html>
