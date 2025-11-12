<?php
require_once '../config/database.php';
include("includes/navbar.php");

$mensaje = "";
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $cedula = trim($_POST['cedula']);
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $nombreUsuario = trim($_POST['nombreUsuario']);
    $correo = trim($_POST['correo']);
    $password = $_POST['password'];
    $confirmar = $_POST['contrasena'];
    $telefono = trim($_POST['telefono']);
    $rol = 'pasajero'; // Valor por defecto

    // Validaciones 
    if (empty($nombre) || empty($apellidos) || empty($cedula) || empty($fechaNacimiento) ||
        empty($nombreUsuario) || empty($correo) || empty($password) || empty($confirmar) || empty($telefono)) {
        $errores[] = "Todos los campos son obligatorios.";
    }
      //Válida el formato del correo electrónico
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }

    if ($password !== $confirmar) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    // Validación de duplicados
    if (empty($errores)) {
        try {
            $sqlCheck = "SELECT COUNT(*) FROM Usuarios 
                         WHERE correo = ? OR cedula = ? OR telefono = ? OR nombreUsuario = ?";
      $stmtCheck = $pdo->prepare($sqlCheck);
      $stmtCheck->execute([$correo, $cedula, $telefono, $nombreUsuario]);
      $existe = $stmtCheck->fetchColumn();

      if ($existe > 0) {
        $errores[] = "El correo, cédula, teléfono o nombre de usuario ya están registrados.";
      }
    } catch (PDOException $e) {
      $errores[] = "Error al verificar duplicados: " . $e->getMessage();
    }
  }


  if (empty($errores)) {
    try {
      // Hashear con SHA-256 
      $hashedPassword = hash('sha256', $password);

      $sql = "INSERT INTO Usuarios 
                    (nombre, apellidos, cedula, fechaNacimiento, nombreUsuario, correo, contrasena, telefono, rol)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$nombre, $apellidos, $cedula, $fechaNacimiento, $nombreUsuario, $correo, $hashedPassword, $telefono, $rol]);

      $mensaje = true;
    } catch (PDOException $e) {
      $errores[] = "Error al registrar el usuario: " . $e->getMessage();
    }
  }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Registro - Aventones CR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/registrarse.css">
</head>

<body>

  <?php if (!empty($errores)): ?>
    <div class="alert alert-danger mt-3">
      <ul class="mb-0">
        <?php foreach ($errores as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="register-container d-flex flex-wrap justify-content-center align-items-stretch my-5 shadow rounded overflow-hidden">
    <div class="col-md-6 p-0 image-side">
      <img src="images/login.png" alt="Registro Aventones" class="w-100 h-100" style="object-fit: cover;">
    </div>

    <div class="col-md-6 form-side p-5 bg-white">
      <h2 class="text-center mb-4">Crea tu cuenta</h2>

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

  <!-- Modal de éxito -->
  <div class="modal fade" id="registroExitoso" tabindex="-1" aria-labelledby="registroExitosoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="position-relative bg-gradient"
          style="background: linear-gradient(135deg, #16a34a, #22c55e); height: 160px;">
          <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
            <i class="bi bi-check-circle-fill display-3 mb-2"></i>
            <h4 class="fw-bold mb-0">¡Registro exitoso!</h4>
          </div>
        </div>
        <div class="modal-body text-center p-5">
          <p class="fs-5 text-muted mb-4">
            Tu cuenta ha sido creada correctamente.
            Ya puedes iniciar sesión y comenzar a usar Aventones CR.
          </p>
          <a href="login.php" class="btn btn-success px-4 py-2 rounded-pill shadow-sm">
            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión
          </a>
        </div>
        <div class="text-center pb-4">
          <small class="text-muted">© 2025 Aventones CR</small>
        </div>
      </div>
    </div>
  </div>
</div>
 
<?php if ($mensaje): ?>
  <!-- Js del modal-->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const modal = new bootstrap.Modal(document.getElementById('registroExitoso'));
      modal.show();
      const form = document.querySelector("form");
      if (form) form.reset();
      setTimeout(() => {
        window.location.href = "login.php";
      }, 4000);
    });
  </script>
  
<?php endif; ?>

  <footer class="footer bg-light border-top mt-5 py-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
      <div>
        <h5 class="fw-bold mb-2">Acerca</h5>
        <ul class="list-unstyled mb-0">
          <li><a href="#" class="text-decoration-none text-muted">Inicio</a></li>
          <li><a href="#" class="text-decoration-none text-muted">Acerca de nosotros</a></li>
          <li><a href="mailto:contacto@aventonescr.com" class="text-decoration-none text-muted">contacto@aventonescr.com</a></li>
          <li><a href="tel:+50688888888" class="text-decoration-none text-muted">+506 8888-8888</a></li>
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