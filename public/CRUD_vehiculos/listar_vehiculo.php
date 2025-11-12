<?php
session_start();
require_once '../../config/funciones_carro.php';
include '../includes/navbar.php'; 

$dashboardURL = ($_SESSION['rol'] === 'chofer')
  ? '../dashboard_chofer.php'
  : '../dashboard_pasajero.php';




// Obtiene el ID del chofer logueado
$idChofer = $_SESSION['user_id'] ?? null;

// Si no hay sesión activa, se muestra un error y se detiene la ejecución
if (!$idChofer) {
  die('<div class="alert alert-danger text-center mt-5">No se detectó usuario logueado.</div>');
}

// Obtiene los vehículos del chofer (aprobados y pendientes)
$vehiculos = getVehiculosByChofer($idChofer);
$pendientes = getVehiculosPendientes($idChofer);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Vehículos - Aventones CR</title>
  
  <!-- Bootstrap, íconos y fuentes -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

</head>
<body class="bg-light">
<!-- Título y botón de agregar -->
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-success">Mis Vehículos</h2>
    <a href="car_create.php" class="btn btn-success">+ Agregar Vehículo</a>
  </div>

  <!-- Vehículos aprobados -->
  <?php if ($vehiculos): ?>
    <div class="card mb-4">
      <div class="card-header bg-success text-white fw-bold">Vehículos Aprobados</div>
      <div class="card-body">
        <table class="table table-bordered align-middle">
          <thead class="table-success">
            <tr><th>Marca</th><th>Modelo</th><th>Año</th><th>Color</th><th>Placa</th><th>Acciones</th></tr>
          </thead>
          <tbody>
            <?php foreach ($vehiculos as $v): ?>
              <tr>
                <td><?= htmlspecialchars($v['marca']) ?></td>
                <td><?= htmlspecialchars($v['modelo']) ?></td>
                <td><?= htmlspecialchars($v['anio']) ?></td>
                <td><?= htmlspecialchars($v['color']) ?></td>
                <td><?= htmlspecialchars($v['placa']) ?></td>
                <td>
                  <!-- Botón Ver -->
                  <a href="car_view.php?id=<?= $v['idVehiculo'] ?>" class="btn btn-outline-primary btn-sm">Ver</a>
                  <!-- Botón Editar -->
                  <a href="car_edit.php?id=<?= $v['idVehiculo'] ?>" class="btn btn-outline-warning btn-sm">Editar</a>
                  <!-- Botón Eliminar (abre modal) -->
                  <button type="button"
                          class="btn btn-danger btn-sm"
                          data-bs-toggle="modal"
                          data-bs-target="#confirmarEliminar<?= $v['idVehiculo'] ?>">
                    <i class="fas fa-trash-alt"></i> Eliminar
                  </button>

                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-secondary text-center">No tenés vehículos aprobados.</div>
  <?php endif; ?>

  <!-- Vehículos pendientes -->
  <?php if ($pendientes): ?>
    <div class="card">
      <div class="card-header bg-warning fw-bold">Vehículos Pendientes de Aprobación</div>
      <div class="card-body">
        <table class="table table-bordered align-middle">
          <thead class="table-warning">
            <tr><th>Marca</th><th>Modelo</th><th>Año</th><th>Color</th><th>Placa</th><th>Estado</th></tr>
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

  <div class="mt-4 text-center">
    <a href="<?= $dashboardURL ?>" class="btn btn-secondary">Volver al Panel</a>
  </div>


    <!-- Modal de confirmación -->
  <div class="modal fade" id="confirmarEliminar<?= $v['idVehiculo'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirmar eliminación</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body text-center">
          <p>¿Estás seguro de que querés eliminar <strong><?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?></strong>?</p>
          <p class="text-muted mb-0">Esta acción eliminará el vehículo y su fotografía permanentemente.</p>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <a href="car_delete.php?id=<?= $v['idVehiculo'] ?>" class="btn btn-danger">
            <i class="fas fa-trash-alt"></i> Sí, eliminar
          </a>
        </div>
      </div>
    </div>
  </div>

</div>
</body>
</html>
