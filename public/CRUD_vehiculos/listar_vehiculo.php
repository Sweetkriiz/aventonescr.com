<?php
session_start();
require_once '../../config/funciones_carro.php';

$idChofer = $_SESSION['user_id'] ?? null;

if (!$idChofer) {
  die('<div class="alert alert-danger text-center mt-5">No se detectó usuario logueado.</div>');
}

$vehiculos = getVehiculosByChofer($idChofer);
$pendientes = getVehiculosPendientes($idChofer);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Vehículos - Aventones CR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
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
                  <a href="car_view.php?id=<?= $v['idVehiculo'] ?>" class="btn btn-outline-primary btn-sm">Ver</a>
                  <a href="car_edit.php?id=<?= $v['idVehiculo'] ?>" class="btn btn-outline-warning btn-sm">Editar</a>
                  <a href="car_delete.php?id=<?= $v['idVehiculo'] ?>" class="btn btn-outline-danger btn-sm">Eliminar</a>
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
    <a href="../dashboard_chofer.php" class="btn btn-secondary">Volver al Panel</a>
  </div>
</div>
</body>
</html>
