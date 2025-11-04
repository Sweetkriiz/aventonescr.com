<?php
session_start();
require_once '../../config/funciones_ride.php';
require_once '../../config/funciones_carro.php';

$idChofer = $_SESSION['user_id'];
$viajes = getViajesByChofer($idChofer);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Rides - Aventones CR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-success"> Mis Rides</h2>
    <a href="ride_create.php" class="btn btn-success">+ Publicar Ride</a>
  </div>

  <?php if ($viajes): ?>
    <div class="card shadow">
      <div class="card-header bg-success text-white fw-bold">Listado de Viajes</div>
      <div class="card-body">
        <table class="table table-bordered align-middle">
          <thead class="table-success">
            <tr>
              <th>Origen</th><th>Destino</th><th>Fecha</th><th>Hora Salida</th><th>Tarifa</th><th>Espacios</th><th>Estado</th><th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($viajes as $v): ?>
              <tr>
                <td><?= htmlspecialchars($v['origen']) ?></td>
                <td><?= htmlspecialchars($v['destino']) ?></td>
                <td><?= htmlspecialchars($v['fecha']) ?></td>
                <td><?= htmlspecialchars($v['horaSalida']) ?></td>
                <td>₡<?= number_format($v['tarifa'], 2) ?></td>
                <td><?= htmlspecialchars($v['espaciosDisponibles']) ?></td>
                <td>
                  <?php
                    $badge = match($v['estado']) {
                      'activo' => 'success',
                      'completado' => 'primary',
                      'cancelado' => 'danger',
                      default => 'secondary'
                    };
                  ?>
                  <span class="badge bg-<?= $badge ?>"><?= ucfirst($v['estado']) ?></span>
                </td>
                <td>
                  <a href="ride_view.php?id=<?= $v['idViaje'] ?>" class="btn btn-outline-primary btn-sm">Ver</a>
                  <a href="ride_edit.php?id=<?= $v['idViaje'] ?>" class="btn btn-outline-warning btn-sm">Editar</a>
                  <a href="ride_delete.php?id=<?= $v['idViaje'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar este ride?')">Eliminar</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-secondary text-center">No tenés rides publicados.</div>
  <?php endif; ?>

  <div class="mt-4 text-center">
    <a href="../dashboard_chofer.php" class="btn btn-secondary">Volver al Panel</a>
  </div>
</div>
</body>
</html>
