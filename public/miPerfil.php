 <?php
session_start();
include('includes/navbar.php');

require_once '../config/database.php';



if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit();
}
$nombreUsuario = $_SESSION['usuario'];

try {
  $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE nombreUsuario = ?");
  $stmt->execute([$nombreUsuario]);
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$usuario) {
    $_SESSION['error'] = 'No se encontró el perfil del usuario.';
    header("Location: index.php");
    exit();
  }
} catch (PDOException $e) {
  die("Error al obtener el perfil: " . $e->getMessage());
}
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
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Poppins', sans-serif;
    }
    .card {
      border: none;
      border-radius: 1rem;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .profile-header {
      background-color: #198754;
      color: white;
      border-radius: 1rem 1rem 0 0;
      padding: 2rem;
      text-align: center;
    }
    .profile-header i {
      font-size: 4rem;
    }
    .profile-header h3 {
      margin-top: 1rem;
      font-weight: 700;
    }
    .profile-body {
      padding: 2rem;
    }
    .info-label {
      font-weight: 600;
      color: #198754;
    }
    .btn-success {
      background-color: #198754;
      border: none;
    }
    .btn-success:hover {
      background-color: #157347;
    }
  </style>
</head>

<body>
  <div class="container py-5">
    <div class="card mx-auto" style="max-width: 850px;">
      
      <div class="profile-header">
        <i class="bi bi-person-circle"></i>
        <h3><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></h3>
        <span class="badge bg-light text-success fw-semibold"><?= ucfirst($usuario['rol']) ?></span>
      </div>

      <div class="profile-body">
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
            <i class="bi bi-arrow-left-circle"></i> Volver al Panel
          </a>
          <a href="CRUD_admin/user_edit.php?id=<?= $usuario['idUsuario'] ?>" class="btn btn-outline-success px-4">
            <i class="bi bi-pencil-square"></i> Editar Perfil
          </a>
        </div>
      </div>

    </div>
  </div>

  <footer class="text-center py-3 text-white mt-5" style="background-color: #198754;">
    © 2025 Aventones CR | Mi Perfil
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>