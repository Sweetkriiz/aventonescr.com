<?php
session_start();
include('includes/navbar.php');
require_once '../config/database.php';

// Obtener datos del usuario
$idUsuario = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE idUsuario = ?");
$stmt->execute([$idUsuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $fechaNacimiento = $_POST['fechaNacimiento'] ?? '';
    $passwordActual = $_POST['passwordActual'] ?? '';
    $passwordNueva = $_POST['passwordNueva'] ?? '';
    $passwordConfirmar = $_POST['passwordConfirmar'] ?? '';

    // Valida si se desea cambiar la contraseña
    if (!empty($passwordActual) || !empty($passwordNueva) || !empty($passwordConfirmar)) {
        // Obtener contraseña actual del usuario
        $stmtPass = $pdo->prepare("SELECT contrasena FROM usuarios WHERE idUsuario = ?");
        $stmtPass->execute([$idUsuario]);
        $actual = $stmtPass->fetchColumn();

        // Validaciones de contraseña
        if (hash('sha256', $passwordActual) !== $actual) {
            $error = "La contraseña actual no es correcta.";
        } elseif ($passwordNueva !== $passwordConfirmar) {
            $error = "Las contraseñas nuevas no coinciden.";
        } elseif (strlen($passwordNueva) < 8) {
            $error = "La nueva contraseña debe tener al menos 8 caracteres.";
        } elseif (hash('sha256', $passwordNueva) === $actual) {
            $error = "La nueva contraseña no puede ser igual a la actual.";
        } else {
            // Actualizar contraseña con hash SHA-256
            $hashed = hash('sha256', $passwordNueva);
            $stmt = $pdo->prepare("UPDATE usuarios SET contrasena = ? WHERE idUsuario = ?");
            $stmt->execute([$hashed, $idUsuario]);
            $success = "Contraseña actualizada correctamente.";
        }
    }

    // Actualizar otros datos
    $stmt = $pdo->prepare("UPDATE usuarios 
                           SET nombre = ?, apellidos = ?, correo = ?, telefono = ?, fechaNacimiento = ? 
                           WHERE idUsuario = ?");
    $stmt->execute([$nombre, $apellidos, $correo, $telefono, $fechaNacimiento, $idUsuario]);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Perfil - Aventones CR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/miPerfil.css">
</head>

<body>
  <div class="container py-5">
    <div class="card mx-auto" style="max-width: 850px;">
      <div class="card-header bg-success text-white text-center">
        <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Editar Perfil</h4>
      </div>

      <!-- Cuerpo del formulario -->
      <div class="px-3 pb-4 mt-3">
        <?php if (isset($error)): ?>
          <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold text-success"><i class="bi bi-person"></i> Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-success"><i class="bi bi-person-lines-fill"></i> Apellidos</label>
            <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-success"><i class="bi bi-envelope"></i> Correo electrónico</label>
            <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($usuario['correo']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-success"><i class="bi bi-telephone"></i> Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($usuario['telefono']) ?>" required>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold text-success"><i class="bi bi-calendar-date"></i> Fecha de nacimiento</label>
            <input type="date" name="fechaNacimiento" class="form-control" value="<?= htmlspecialchars($usuario['fechaNacimiento']) ?>">
          </div>

          <hr class="mt-4 mb-2">

          <h5 class="fw-bold text-success mb-2"><i class="bi bi-shield-lock"></i> Cambiar Contraseña</h5>

          <div class="col-md-4">
            <label class="form-label text-success">Contraseña actual</label>
            <input type="password" name="passwordActual" class="form-control" placeholder="Actual...">
          </div>

          <div class="col-md-4">
            <label class="form-label text-success">Nueva contraseña</label>
            <input type="password" name="passwordNueva" class="form-control" placeholder="Nueva...">
          </div>

          <div class="col-md-4">
            <label class="form-label text-success">Confirmar nueva</label>
            <input type="password" name="passwordConfirmar" class="form-control" placeholder="Confirmar...">
          </div>

          <div class="col-12 text-center mt-4">
            <a href="miPerfil.php" class="btn btn-outline-secondary px-4 me-2">
              <i class="bi bi-arrow-left-circle"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-success px-4">
              <i class="bi bi-check-circle"></i> Guardar cambios
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal de confirmación -->
  <div class="modal fade" id="modalSuccess" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg rounded-4">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title"><i class="bi bi-check-circle-fill me-2"></i> Contraseña actualizada</h5>
        </div>
        <div class="modal-body text-center">
          <p class="fs-5 text-muted mb-3">Tu contraseña se ha actualizado correctamente.</p>
          <button type="button" id="btnIrPerfil" class="btn btn-success px-4 py-2 rounded-pill">Ir a mi perfil</button>
        </div>
      </div>
    </div>
  </div>

  <footer class="text-center py-3 bg-dark text-white mt-5">
    © 2025 Aventones CR | Panel del Administrador
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <?php if (isset($success)): ?>
    
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modal = new bootstrap.Modal(document.getElementById('modalSuccess'));
      modal.show();

      // Redirigir automáticamente después de 5s
      setTimeout(() => {
        window.location.href = "miPerfil.php";
      }, 5000);

      // O de inmediato si presiona el botón
      document.getElementById('btnIrPerfil').addEventListener('click', function() {
        window.location.href = "miPerfil.php";
      });
    });
  </script>
  <?php endif; ?>
</body>
</html>
