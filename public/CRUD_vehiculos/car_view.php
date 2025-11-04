<?php
session_start();
require_once '../../config/funciones_carro.php';

$id = intval($_GET['id'] ?? 0);
$vehiculo = getVehiculoById($id);

if (!$vehiculo) {
  $_SESSION['error'] = '❌ Vehículo no encontrado.';
  header('Location: listar_vehiculo.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detalles del Vehículo - Aventones CR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="card shadow-lg">
    <div class="card-header bg-success text-white d-flex align-items-center">
      <i class="fas fa-car me-2"></i>
      <h4 class="mb-0">Detalles del Vehículo</h4>
    </div>

    <div class="card-body">
      <div class="row align-items-center mb-4">
        
      <!-- Imagen del vehículo -->
        <div class="col-md-5 text-center">
          <?php if (!empty($vehiculo['fotografia'])): ?>
            <img src="../../uploads/vehiculos/<?= htmlspecialchars($vehiculo['fotografia']); ?>" 
                 alt="Fotografía del vehículo" 
                 class="img-fluid rounded shadow-sm" 
                 style="max-height: 250px; object-fit: cover;">
          <?php else: ?>
            <div class="bg-secondary text-white d-flex justify-content-center align-items-center rounded" 
                 style="height: 250px;">
              <i class="fas fa-car-side fa-3x"></i>
            </div>
            <small class="text-muted d-block mt-2">Sin fotografía</small>
          <?php endif; ?>
        </div>

        <!-- Información general -->
        <div class="col-md-7">
          <table class="table table-borderless mb-0">
            <tr><th>Marca:</th><td><?= htmlspecialchars($vehiculo['marca']); ?></td></tr>
            <tr><th>Modelo:</th><td><?= htmlspecialchars($vehiculo['modelo']); ?></td></tr>
            <tr><th>Año:</th><td><?= htmlspecialchars($vehiculo['anio']); ?></td></tr>
            <tr><th>Color:</th><td><?= htmlspecialchars($vehiculo['color']); ?></td></tr>
            <tr><th>Placa:</th><td><?= htmlspecialchars($vehiculo['placa']); ?></td></tr>
            <tr>
              <th>Estado:</th>
              <td>
                <?php
                $estado = $vehiculo['estado'];
                $badge = match ($estado) {
                  'aprobado' => 'success',
                  'pendiente' => 'warning text-dark',
                  'rechazado' => 'danger',
                  default => 'secondary'
                };
                ?>
                <span class="badge bg-<?= $badge; ?>"><?= ucfirst($estado); ?></span>
              </td>
            </tr>
          </table>
        </div>
      </div>

      <!-- Botón volver -->
      <div class="text-center mt-4">
        <a href="listar_vehiculo.php" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Volver a Mis vehículos
        </a>
      </div>
    </div>
  </div>
</div>

</body>
</html>
