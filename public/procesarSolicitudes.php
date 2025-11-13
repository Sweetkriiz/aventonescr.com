<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/funciones_admin.php';
include('includes/navbar.php');

    //Procesar aprobación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $idVehiculo = $_POST['id'] ?? null;
  $accion = $_POST['accion'] ?? null;

  if ($accion === 'aprobar' && $idVehiculo) {
    $stmt = $pdo->prepare("
            UPDATE vehiculos 
            SET estado = 'aprobado', 
                motivoRechazo = NULL,
                leido = 1 -- ya leído, no hay notificación pendiente
            WHERE idVehiculo = ?
        ");
        $stmt->execute([$idVehiculo]);

        // Cambiar rol a chofer 
        $stmtChofer = $pdo->prepare("SELECT idChofer FROM vehiculos WHERE idVehiculo = ?");
        $stmtChofer->execute([$idVehiculo]);
        $idChofer = $stmtChofer->fetchColumn();

    // --- Cambiar rol a chofer si aplica ---
    $stmtChofer = $pdo->prepare("SELECT idChofer FROM vehiculos WHERE idVehiculo = ?");
    $stmtChofer->execute([$idVehiculo]);
    $idChofer = $stmtChofer->fetchColumn();

    if ($idChofer) {
      $stmtRol = $pdo->prepare("SELECT rol FROM usuarios WHERE idUsuario = ?");
      $stmtRol->execute([$idChofer]);
      $rolActual = $stmtRol->fetchColumn();

        $_SESSION['mensaje'] = "Vehículo aprobado correctamente.";
        header("Location: procesarSolicitudes.php");
        exit();
    //Procesar rechazo
    } elseif ($accion === 'rechazar' && $idVehiculo) {
        $motivo = trim($_POST['motivo'] ?? '');
        if (empty($motivo)) {
            $_SESSION['error'] = 'Debe indicar el motivo de rechazo.';
            header("Location: procesarSolicitudes.php");
            exit();
        }

    $stmt = $pdo->prepare("
            UPDATE vehiculos 
            SET estado = 'rechazado', 
                motivoRechazo = ?, 
                leido = 0
            WHERE idVehiculo = ?
        ");
    $stmt->execute([$motivo, $idVehiculo]);
    $_SESSION['mensaje'] = "Vehículo rechazado correctamente.";
    header("Location: procesarSolicitudes.php");
    exit();
  }
}

//Filtro de búsqueda 
$busqueda = $_GET['busqueda'] ?? null;

// Consulta de vehículos pendientes, aprobados o rechazados
if (!empty($busqueda)) {
  $stmt = $pdo->prepare("
        SELECT 
            v.idVehiculo AS id,
            v.marca,
            v.modelo,
            v.anio,
            v.color,
            v.placa,
            v.fotografia,
            v.estado,
            v.motivoRechazo,
            u.nombreUsuario,
            CONCAT(u.nombre, ' ', u.apellidos) AS nombreCompleto
        FROM vehiculos v
        INNER JOIN usuarios u ON v.idChofer = u.idUsuario
        WHERE 
            u.nombreUsuario LIKE :busqueda 
            OR u.nombre LIKE :busqueda 
            OR u.apellidos LIKE :busqueda
        ORDER BY v.idVehiculo DESC
    ");
  $stmt->bindValue(':busqueda', "%$busqueda%");
  $stmt->execute();
  $vehiculosPendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
  $stmt = $pdo->query("
        SELECT 
            v.idVehiculo AS id,
            v.marca,
            v.modelo,
            v.anio,
            v.color,
            v.placa,
            v.fotografia,
            v.estado,
            v.motivoRechazo,
            u.nombreUsuario,
            CONCAT(u.nombre, ' ', u.apellidos) AS nombreCompleto
        FROM vehiculos v
        INNER JOIN usuarios u ON v.idChofer = u.idUsuario
        WHERE v.estado IN ('pendiente','rechazado','aprobado')
        ORDER BY v.idVehiculo DESC
    ");
    // Obtener todos los vehículos pendientes, aprobados o rechazados
    $vehiculosPendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aventones CR | Procesar Solicitudes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/procesarSolicitudes.css">
</head>

<body>

  <!-- Mensajes -->
  <div class="container mt-4">
    <?php if (isset($_SESSION['mensaje'])): ?>
      <div class="alert alert-success alert-dismissible fade show text-center mx-auto" style="max-width:600px;">
        <?= htmlspecialchars($_SESSION['mensaje']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show text-center mx-auto" style="max-width:600px;">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
  </div>

  <div class="container py-5">
    <h2 class="text-center fw-bold mb-4">Solicitudes de Vehículos</h2>

    <!-- Buscador -->
    <div class="container mb-4">
      <form method="GET" class="d-flex justify-content-center">
        <input type="text" name="busqueda" class="form-control me-2"
          placeholder="Buscar por nombre o usuario..."
          style="max-width: 400px;" value="<?= htmlspecialchars($_GET['busqueda'] ?? '') ?>">
        <button type="submit" class="btn btn-success">
          <i class="bi bi-search"></i> Buscar
        </button>
        <a href="procesarSolicitudes.php" class="btn btn-secondary ms-2">
          <i class="bi bi-arrow-clockwise"></i> Ver todos
        </a>
      </form>
    </div>
  <!-- Cards de vehículos-->
  <?php if (empty($vehiculosPendientes)): ?>
    <div class="alert alert-info text-center">No se encontraron vehículos.</div>
  <?php else: ?>
      <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($vehiculosPendientes as $vehiculo): ?>
          <div class="col">
            <div class="card h-100">
              <?php if (!empty($vehiculo['fotografia'])): ?>
                <img src="../uploads/<?= htmlspecialchars($vehiculo['fotografia']) ?>" alt="Foto del vehículo">
              <?php else: ?>
                <img src="https://cdn-icons-png.flaticon.com/512/743/743131.png" alt="Vehículo genérico">
              <?php endif; ?>
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($vehiculo['marca'] . ' ' . $vehiculo['modelo']) ?></h5>
                <p class="card-text mb-1"><strong>Chofer:</strong> <?= htmlspecialchars($vehiculo['nombreCompleto']) ?></p>
                <p class="card-text mb-1"><strong>Usuario:</strong> <?= htmlspecialchars($vehiculo['nombreUsuario']) ?></p>
                <p class="card-text mb-1"><strong>Placa:</strong> <?= htmlspecialchars($vehiculo['placa']) ?></p>
                <p class="card-text mb-1"><strong>Año:</strong> <?= htmlspecialchars($vehiculo['anio']) ?></p>

                <!-- Badge de estado -->
                <p class="card-text mb-2">
                  <strong>Estado:</strong>
                  <span class="badge 
                    <?= $vehiculo['estado'] === 'aprobado' ? 'bg-success' : ($vehiculo['estado'] === 'rechazado' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                    <?= ucfirst($vehiculo['estado']) ?>
                  </span>
                </p>

                <!-- Mostrar motivo de rechazo -->
                <?php if ($vehiculo['estado'] === 'rechazado' && !empty($vehiculo['motivoRechazo'])): ?>
                  <p class="card-text text-muted small mb-3">
                    <strong>Motivo:</strong> <?= htmlspecialchars($vehiculo['motivoRechazo']) ?>
                  </p>
                <?php endif; ?>

                <!-- Formulario de aprobación -->
                <form method="POST" class="d-inline">
                  <input type="hidden" name="id" value="<?= $vehiculo['id'] ?>">
                  <button type="submit" name="accion" value="aprobar" class="btn btn-aprobar btn-sm"
                    <?= $vehiculo['estado'] !== 'pendiente' ? 'disabled' : '' ?>>
                    <i class="bi bi-check-circle"></i> Aprobar
                  </button>
                </form>

                <!-- Botón para modal de rechazo -->
                <button type="button" class="btn btn-rechazar btn-sm" data-bs-toggle="modal"
                  data-bs-target="#modalRechazo" data-id="<?= $vehiculo['id'] ?>"
                  <?= $vehiculo['estado'] !== 'pendiente' ? 'disabled' : '' ?>>
                  <i class="bi bi-x-circle"></i> Rechazar
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Modal de rechazo -->
  <div class="modal fade" id="modalRechazo" tabindex="-1">
    <div class="modal-dialog">
      <form method="POST" class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title"><i class="bi bi-x-circle"></i> Rechazar solicitud</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="vehiculoId">
          <div class="mb-3">
            <label for="motivo" class="form-label">Motivo de rechazo</label>
            <textarea name="motivo" id="motivo" class="form-control" rows="4" placeholder="Escriba el motivo del rechazo..." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="accion" value="rechazar" class="btn btn-danger">Rechazar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="mt-1 d-flex justify-content-center">
    <a href="../dashboard_admin.php" class="btn btn-success">
      <i class="bi bi-arrow-left-circle me-2"></i> Volver al Panel
    </a>
  </div>

  <footer class="text-center py-3 bg-dark text-white mt-5">
    © 2025 Aventones CR | Panel del Administrador
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
   <!-- Js para obtener el id del modal  -->
  <script>
    const modalRechazo = document.getElementById('modalRechazo');
    modalRechazo.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget;
      modalRechazo.querySelector('#vehiculoId').value = button.getAttribute('data-id');
    });
  </script>
</body>

</html>