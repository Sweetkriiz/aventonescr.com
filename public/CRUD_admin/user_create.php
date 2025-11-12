<?php
session_start();
require_once '../../config/database.php';
require_once __DIR__ . '/../../config/funciones_admin.php';
include("../includes/navbar.php");


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
  $confirmar = $_POST['confirmar'];
  $telefono = trim($_POST['telefono']);
  $rol = $_POST['rol'] ?? 'pasajero';

  // Validaciones
  if (
    empty($nombre) || empty($apellidos) || empty($cedula) || empty($fechaNacimiento) ||
    empty($nombreUsuario) || empty($correo) || empty($password) || empty($confirmar) || empty($telefono)
  ) {
    $errores[] = "Todos los campos son obligatorios.";
  }

  if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El correo electrónico no es válido.";
  }

  if ($password !== $confirmar) {
    $errores[] = "Las contraseñas no coinciden.";
  }

  if (strlen($password) < 8) {
    $errores[] = "La contraseña debe tener al menos 8 caracteres.";
  }

  // Verificar duplicados
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

  // Insertar usuario
  if (empty($errores)) {
    try {
      // Usar SHA-256 en lugar de password_hash()
      $hashedPassword = hash('sha256', $password);

      $sql = "INSERT INTO Usuarios 
              (nombre, apellidos, cedula, fechaNacimiento, nombreUsuario, correo, contrasena, telefono, rol)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        $nombre, $apellidos, $cedula, $fechaNacimiento,
        $nombreUsuario, $correo, $hashedPassword, $telefono, $rol
      ]);

      $mensaje = "Usuario creado correctamente.";
    } catch (PDOException $e) {
      $errores[] = "Error al registrar el usuario: " . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Usuarios - Aventones CR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
   
      <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Poppins', sans-serif;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    .btn-success {
      background-color: #28a745;
      border: none;
    }
    .btn-success:hover {
      background-color: #218838;
    }
    .form-label {
      font-weight: 600;
      color: #343a40;
    }
  </style>
</head>
<body>
    <div class="container py-5">
    <div class="card mx-auto p-4" style="max-width: 850px;">
      <div class="card-body">
        <h3 class="text-center fw-bold mb-4 text-success">
          <i class="bi bi-person-plus-fill"></i> Crear nuevo usuario
        </h3>

        <?php if (!empty($errores)): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errores as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php elseif (!empty($mensaje)): ?>
          <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Apellidos</label>
              <input type="text" name="apellidos" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Cédula</label>
              <input type="text" name="cedula" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de nacimiento</label>
              <input type="date" name="fechaNacimiento" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo electrónico</label>
              <input type="email" name="correo" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Teléfono</label>
              <input type="tel" name="telefono" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nombre de usuario</label>
              <input type="text" name="nombreUsuario" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Rol</label>
              <select name="rol" class="form-select" required>
                <option value="pasajero">Pasajero</option>
                <option value="chofer">Chofer</option>
                <option value="administrador">Administrador</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contraseña</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirmar contraseña</label>
              <input type="password" name="confirmar" class="form-control" required>
            </div>
            <div class="col-12">
              <label class="form-label">Fotografía (opcional)</label>
              <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
          </div>

          <div class="text-center mt-4">
            <button type="submit" class="btn btn-success px-4">
              <i class="bi bi-check-circle"></i> Crear usuario
            </button>
            <a href="listar_usuarios.php" class="btn btn-secondary px-4 ms-2">
              <i class="bi bi-arrow-left-circle"></i> Volver
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

<footer class="text-center py-3 text-white mt-5">
© 2025 Aventones CR | Registro de Usuarios
</footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>