<?php
session_start();
include('includes/navbar.php');
require_once '../config/database.php';

// Obtener datos del usuario
$idUsuario = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE idUsuario = ?");
$stmt->execute([$idUsuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Perfil - Aventones CR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/miPerfil.css">

</head>

<body>
  <div class="container py-5">
    <div class="card mx-auto shadow-sm" style="max-width: 850px; border-radius: 1rem;">
      
      <div class="profile-header">
        <div class="profile-icon">
          <i class="bi bi-person-circle"></i>
        </div>

        <h3 class="fw-bold"><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></h3>
        <span class="badge bg-light text-success fw-semibold"><?= ucfirst($usuario['rol']) ?></span>
      </div>

      <div class="profile-body px-4 pb-4">
        <div class="row mb-3">
          <div class="col-md-6">
            <p class="info-label"><i class="bi bi-person-badge"></i> Nombre de usuario:</p>
            <p><?= htmlspecialchars($usuario['nombreUsuario']) ?></p>
          </div>
          <div class="col-md-6">
            <p class="info-label"><i class="bi bi-envelope"></i> Correo electrónico:</p>
            <p><?= htmlspecialchars($usuario['correo']) ?></p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <p class="info-label"><i class="bi bi-credit-card-2-front"></i> Cédula:</p>
            <p><?= htmlspecialchars($usuario['cedula']) ?></p>
          </div>
          <div class="col-md-6">
            <p class="info-label"><i class="bi bi-telephone"></i> Teléfono:</p>
            <p><?= htmlspecialchars($usuario['telefono']) ?></p>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <p class="info-label"><i class="bi bi-calendar-date"></i> Fecha de nacimiento:</p>
            <p><?= htmlspecialchars($usuario['fechaNacimiento']) ?></p>
          </div>
          <div class="col-md-6">
            <p class="info-label"><i class="bi bi-clock-history"></i> Fecha de registro:</p>
            <p><?= htmlspecialchars($usuario['fechaRegistro'] ?? 'N/D') ?></p>
          </div>
        </div>

        <div class="mt-4 text-center">
          <a href="dashboard_<?= strtolower($usuario['rol']) ?>.php" class="btn btn-success px-4 me-2">
            <i class="bi bi-arrow-left-circle"></i> Volver a inicio
          </a>
          <a href="edit_miperfil.php?id=<?= $usuario['idUsuario'] ?>" class="btn btn-outline-success px-4">
            <i class="bi bi-pencil-square"></i> Editar Perfil
          </a>
        </div>
      </div>
    </div>
  </div>

  <footer class="text-center py-3 bg-dark text-white mt-5">
    © 2025 Aventones CR | Panel del Administrador
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
