<?php
session_start();
require_once '../../config/funciones_carro.php';
include '../includes/navbar.php';

$dashboardURL = ($_SESSION['rol'] === 'chofer')
  ? '../dashboard_chofer.php'
  : '../dashboard_pasajero.php';

// Obtiene el ID del chofer logueado
$idChofer = $_SESSION['user_id'] ?? null;

if (!$idChofer) {
  die('<div class="alert alert-danger text-center mt-5">No se detectó usuario logueado.</div>');
}

// Obtiene los vehículos del chofer
$vehiculos = getVehiculosByChofer($idChofer);
$pendientes = getVehiculosPendientes($idChofer);
$rechazados = getVehiculosRechazados($idChofer);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Vehículos - Aventones CR</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-light">

  <!-- Mensajes de éxito/error -->
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show text-center mx-auto mt-3 shadow-sm" style="max-width:700px;">
      <i class="bi bi-check-circle-fill me-2"></i>
      <?= htmlspecialchars($_SESSION['success']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show text-center mx-auto mt-3 shadow-sm" style="max-width:700px;">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <?= htmlspecialchars($_SESSION['error']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold text-success">Mis Vehículos</h2>
      <a href="car_create.php" class="btn btn-success">+ Agregar Vehículo</a>
    </div>

    <!-- Vehículos Aprobados -->
    <?php if ($vehiculos): ?>
      <div class="mb-4">
        <h4 class="fw-bold text-success mb-3">Vehículos Aprobados</h4>
        <div class="row g-4">
          <?php foreach ($vehiculos as $v): ?>
            <div class="col-md-6 col-lg-4">
              <div class="card shadow-sm border-0 h-100">
                <?php if (!empty($v['fotografia'])): ?>
                  <img src="../uploads/<?= htmlspecialchars($v['fotografia']); ?>"
                    class="card-img-top" alt="Foto del vehículo"
                    style="height:200px; object-fit:cover;">
                <?php else: ?>
                  <img src="https://cdn-icons-png.flaticon.com/512/743/743131.png"
                    class="card-img-top" alt="Vehículo genérico"
                    style="height:200px; object-fit:cover;">
                <?php endif; ?>

                <div class="card-body">
                  <h5 class="fw-bold"><?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?></h5>
                  <p class="mb-1"><strong>Placa:</strong> <?= htmlspecialchars($v['placa']) ?></p>
                  <p class="mb-1"><strong>Estado:</strong>
                    <span class="badge bg-success">Aprobado</span>
                  </p>
                </div>

                <div class="card-footer bg-white text-center">
                  <a href="car_view.php?id=<?= $v['idVehiculo'] ?>" class="btn btn-outline-primary btn-sm me-1">
                    <i class="bi bi-eye"></i> Ver
                  </a>
                  <a href="car_edit.php?id=<?= $v['idVehiculo'] ?>" class="btn btn-outline-warning btn-sm me-1">
                    <i class="bi bi-pencil"></i> Editar
                  </a>
                  <!-- Botón que activa el modal -->
                  <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmarEliminar<?= $v['idVehiculo'] ?>">
                    <i class="bi bi-trash"></i> Eliminar
                  </button>
                </div>
              </div>
            </div>

            <!-- Modal de Confirmación -->
            <div class="modal fade" id="confirmarEliminar<?= $v['idVehiculo'] ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                      <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmar eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body text-center">
                    <p>¿Estás segura de que querés eliminar <strong><?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?></strong>?</p>
                    <p class="text-muted mb-0">Esta acción <b>eliminará permanentemente</b> el vehículo y su fotografía.</p>
                  </div>
                  <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="car_delete.php?id=<?= $v['idVehiculo'] ?>" class="btn btn-danger">
                      <i class="bi bi-trash"></i> Sí, eliminar
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-secondary text-center">No tenés vehículos aprobados.</div>
    <?php endif; ?>

    <!-- Vehículos Pendientes -->
    <?php if ($pendientes): ?>
      <div class="card mt-4">
        <div class="card-header bg-warning fw-bold">Vehículos Pendientes de Aprobación</div>
        <div class="card-body">
          <table class="table table-bordered align-middle">
            <thead class="table-warning">
              <tr>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Color</th>
                <th>Placa</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($pendientes as $v): ?>
                <tr>
                  <td><?= htmlspecialchars($v['marca']) ?></td>
                  <td><?= htmlspecialchars($v['modelo']) ?></td>
                  <td><?= htmlspecialchars($v['anio']) ?></td>
                  <td><?= htmlspecialchars($v['color']) ?></td>
                  <td><?= htmlspecialchars($v['placa']) ?></td>
                  <td><span class="badge bg-warning text-dark"><?= ucfirst($v['estado']) ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>

    <!-- Vehículos Rechazados -->
    <?php if ($rechazados): ?>
      <div class="card mt-4">
        <div class="card-header bg-danger text-white fw-bold">Vehículos Rechazados</div>
        <div class="card-body">
          <table class="table table-bordered align-middle">
            <thead class="table-danger">
              <tr>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Placa</th>
                <th>Motivo del Rechazo</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rechazados as $v): ?>
                <tr>
                  <td><?= htmlspecialchars($v['marca']) ?></td>
                  <td><?= htmlspecialchars($v['modelo']) ?></td>
                  <td><?= htmlspecialchars($v['placa']) ?></td>
                  <td><?= htmlspecialchars($v['motivoRechazo'] ?? 'Sin especificar') ?></td>
                  <td><span class="badge bg-danger">Rechazado</span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>

    <div class="mt-4 text-center">
      <a href="<?= $dashboardURL ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver al Panel
      </a>
    </div>
  </div>

</body>

</html>