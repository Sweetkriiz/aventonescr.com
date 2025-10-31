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

    echo "<p class='text-center mt-4'>Usuario registrado correctamente. <a href='login.php'>Iniciar sesión</a></p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - Aventones CR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <link rel="stylesheet" href="css/registrarse.css">
</head>

<body>
  <!-- 🔹 Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container-fluid px-4">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="images/logo.jpeg" alt="Logo Aventones" class="navbar-logo me-2" style="height: 45px; border-radius: 50%;">
        <span class="fw-bold text-success">Aventones CR</span>
      </a>
      <button class="btn btn-success ms-auto">Publicar un viaje</button>
    </div>
  </nav>

  <!-- 🔹 Contenedor principal -->
  <div class="register-container d-flex flex-wrap justify-content-center align-items-stretch my-5 shadow rounded overflow-hidden">
    <div class="col-md-6 p-0 image-side">
      <img src="images/login.png" alt="Registro Aventones" class="w-100 h-100" style="object-fit: cover;">
    </div>

    <div class="col-md-6 form-side p-5 bg-white">
      <h2 class="text-center mb-4 ">Crea tu cuenta</h2>

      <form method="POST" action="" class="needs-validation" novalidate>
        <div class="row g-3">
          <div class="col-md-6">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ingresa tu nombre" required>
          </div>

          <div class="col-md-6">
            <label for="apellidos" class="form-label">Apellidos</label>
            <input type="text" id="apellidos" name="apellidos" class="form-control" placeholder="Ingresa tus apellidos" required>
          </div>

          <div class="col-md-6">
            <label for="cedula" class="form-label">Cédula</label>
            <input type="text" id="cedula" name="cedula" class="form-control" placeholder="Ingresa tu identificación" required>
          </div>

          <div class="col-md-6">
            <label for="fechaNacimiento" class="form-label">Fecha de nacimiento</label>
            <input type="date" id="fechaNacimiento" name="fechaNacimiento" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label for="nombreUsuario" class="form-label">Nombre de usuario</label>
            <input type="text" id="nombreUsuario" name="nombreUsuario" class="form-control" placeholder="Elige un nombre de usuario" required>
          </div>

          <div class="col-md-6">
            <label for="correo" class="form-label">Correo electrónico</label>
            <input type="email" id="correo" name="correo" class="form-control" placeholder="email@example.com" required>
          </div>

          <div class="col-md-6">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
          </div>

          <div class="col-md-6">
            <label for="contrasena" class="form-label">Confirmar Contraseña</label>
            <input type="password" id="contrasena" name="contrasena" class="form-control" placeholder="••••••••" required>
          </div>

          <div class="col-12">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="tel" id="telefono" name="telefono" class="form-control" placeholder="Ej: 8888-8888" required>
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-success w-100 mt-3 py-2">Registrarme</button>
          </div>
        </div>
      </form>

      <p class="text-center mt-3 mb-0">¿Ya tienes una cuenta? 
        <a href="login.php" class="text-decoration-none text-success fw-semibold">Iniciar sesión</a>
      </p>
    </div>
  </div>

  <!-- 🔹 Footer -->
  <footer class="footer bg-light border-top mt-5 py-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <h5 class="fw-bold mb-2">Acerca</h5>
        <ul class="list-unstyled mb-0">
          <li><a href="#" class="text-decoration-none text-muted">Inicio</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Acerca de nosotros</a></li>
          <li><a href="mailto:contacto@aventonescr.com" class="text-decoration-none text-muted">📧 contacto@aventonescr.com</a></li>
          <li><a href="tel:+50688888888" class="text-decoration-none text-muted">📞 +506 8888-8888</a></li>
        </ul>

      </div>
      <div class="d-flex gap-3 fs-4">
        <a href="#" class="text-success"><i class="bi bi-facebook"></i></a>
        <a href="#" class="text-success"><i class="bi bi-instagram"></i></a>
      </div>
    </div>
    <hr>
    <div class="text-center small text-muted">
      ©2025, Aventones CR — <a href="#" class="text-decoration-none text-muted">Términos</a> · <a href="#" class="text-decoration-none text-muted">Privacidad</a>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
