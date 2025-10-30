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
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - Aventones CR</title>
  <link rel="stylesheet" href="css/registrarse.css">
</head>
<body>
  <div class="register-container">
    <div class="content">
      <img src="images/logo.png" alt="Logo Aventones" class="logo">
      <h2>Crear cuenta Aventones</h2>

      <form action="register.php" method="POST">
        <div class="form-row">
          <div class="form-group">
            <label>Nombre de usuario</label>
            <input type="text" name="nombreUsuario" required>
          </div>

          <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="contrasena" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Nombre</label>
            <input type="text" name="nombre" required>
          </div>

          <div class="form-group">
            <label>Apellidos</label>
            <input type="text" name="apellidos" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Cédula</label>
            <input type="text" name="cedula" required>
          </div>

          <div class="form-group">
            <label>Fecha de nacimiento</label>
            <input type="date" name="fechaNacimiento" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Correo electrónico</label>
            <input type="email" name="correo" required>
          </div>

          <div class="form-group">
            <label>Teléfono</label>
            <input type="tel" name="telefono" required>
          </div>
        </div>

        <button type="submit">Registrarse</button>
        <p class="switch">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
      </form>
    </div>
  </div>
</body>
</html>
