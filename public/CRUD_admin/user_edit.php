<?php
session_start();

require_once '../../config/database.php';
require_once __DIR__ . '/../../config/funciones_admin.php';
include("../includes/navbar.php");

// Verificar acceso solo para administrador
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== 'administrador') {
  header("Location: ../login.php");
  exit();
}

// Obtener ID
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
  $_SESSION['error'] = 'ID de usuario inválido.';
  header('Location: listar_usuarios.php');
  exit();
}

$errores = [];

// Obtener datos del usuario
try {
  $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE idUsuario = ?");
  $stmt->execute([$id]);
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$usuario) {
    $_SESSION['error'] = 'Usuario no encontrado.';
    header('Location: listar_usuarios.php');
    exit();
  }
} catch (PDOException $e) {
  die("Error al obtener usuario: " . $e->getMessage());
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre']);
  $apellidos = trim($_POST['apellidos']);
  $cedula = trim($_POST['cedula']);
  $fechaNacimiento = $_POST['fechaNacimiento'];
  $nombreUsuario = trim($_POST['nombreUsuario']);
  $correo = trim($_POST['correo']);
  $telefono = trim($_POST['telefono']);
  $rol = $_POST['rol'] ?? 'pasajero';

  // Validaciones básicas
  if (
    empty($nombre) || empty($apellidos) || empty($cedula) || empty($fechaNacimiento) ||
    empty($nombreUsuario) || empty($correo) || empty($telefono)
  ) {
    $errores[] = "Todos los campos son obligatorios.";
  }

  if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El correo electrónico no es válido.";
  }

  // Si no hay errores, actualizar
  if (empty($errores)) {
    try {
      $sql = "UPDATE Usuarios
              SET nombre = ?, apellidos = ?, cedula = ?, fechaNacimiento = ?, 
                  nombreUsuario = ?, correo = ?, telefono = ?, rol = ?
              WHERE idUsuario = ?";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$nombre, $apellidos, $cedula, $fechaNacimiento, $nombreUsuario, $correo, $telefono, $rol, $id]);

      // Redirigir con mensaje de éxito
      $_SESSION['success'] = 'Usuario actualizado correctamente.';
      header('Location: listar_usuarios.php');
      exit();

    } catch (PDOException $e) {
      $errores[] = "Error al actualizar usuario: " . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Usuario - Aventones CR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Poppins', sans-serif;
    }
    .card {
      border-radius: .75rem;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      border: none;
    }
    label {
      font-weight: 600;
    }
    .btn-secondary {
      background-color: #6c757d;
      border: none;
    }
    .btn-secondary:hover {
      background-color: #5a6268;
    }
    .btn-success {
      background-color: #198754;
      border: none;
    }
    .btn-success:hover {
      background-color: #157347;
    }
    .info-box {
      background-color: #eafaf1;
      border-left: 5px solid #198754;
      padding: 15px;
      border-radius: .5rem;
      font-size: .9rem;
    }
  </style>
</head>

<body>
  <div class="container py-5">
    <div class="card mx-auto p-4" style="max-width: 850px;">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="fw-bold text-success mb-0">
            <i class="bi bi-pencil-square me-2"></i> Editar Usuario
          </h4>
          <a href="listar_usuarios.php" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left-circle"></i> Volver a Lista
          </a>
        </div>
         <!-- Mensaje de error -->
        <?php if (!empty($errores)): ?>
          <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errores as $error): ?><li><?= htmlspecialchars($error) ?></li><?php endforeach; ?></ul>
          </div>
        <?php endif; ?>

        <form method="POST" class="mt-3">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label"><i class="bi bi-person"></i> Nombre</label>
              <input type="text" name="nombre" class="form-control" maxlength="50" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label"><i class="bi bi-person-badge"></i> Apellidos</label>
              <input type="text" name="apellidos" class="form-control" maxlength="50" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label"><i class="bi bi-credit-card-2-front"></i> Cédula</label>
              <input type="text" name="cedula" class="form-control" maxlength="20" value="<?= htmlspecialchars($usuario['cedula']) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label"><i class="bi bi-calendar-date"></i> Fecha de nacimiento</label>
              <input type="date" name="fechaNacimiento" class="form-control" value="<?= htmlspecialchars($usuario['fechaNacimiento']) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label"><i class="bi bi-envelope"></i> Correo electrónico</label>
              <input type="email" name="correo" class="form-control" maxlength="100" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label"><i class="bi bi-telephone"></i> Teléfono</label>
              <input type="text" name="telefono" class="form-control" maxlength="15" value="<?= htmlspecialchars($usuario['telefono']) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label"><i class="bi bi-person-circle"></i> Nombre de usuario</label>
              <input type="text" name="nombreUsuario" class="form-control" maxlength="30" value="<?= htmlspecialchars($usuario['nombreUsuario']) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label"><i class="bi bi-shield-lock"></i> Contraseña</label>
              <input type="password" class="form-control" value="********" disabled readonly>
              <small class="text-muted">La contraseña no puede modificarse desde este formulario.</small>
            </div>

            <div class="col-md-6">
              <label class="form-label"><i class="bi bi-person-gear"></i> Rol</label>
              <select name="rol" class="form-select" required>
                <option value="pasajero" <?= $usuario['rol'] === 'pasajero' ? 'selected' : '' ?>>Pasajero</option>
                <option value="chofer" <?= $usuario['rol'] === 'chofer' ? 'selected' : '' ?>>Chofer</option>
                <option value="administrador" <?= $usuario['rol'] === 'administrador' ? 'selected' : '' ?>>Administrador</option>
              </select>
            </div>
          </div>

          <!-- Bloque informativo -->
          <div class="info-box mt-4">
            <i class="bi bi-info-circle-fill text-success"></i>
            <strong> Información del usuario:</strong><br>
            ID: <?= htmlspecialchars($usuario['idUsuario']) ?> |
            Creado: <?= htmlspecialchars($usuario['fechaRegistro'] ?? 'N/D') ?>
          </div>

          <div class="text-center mt-4">
            <a href="listar_usuarios.php" class="btn btn-secondary px-4 me-2">
              <i class="bi bi-x-circle"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-success px-4">
              <i class="bi bi-check-circle"></i> Actualizar Usuario
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
?> 