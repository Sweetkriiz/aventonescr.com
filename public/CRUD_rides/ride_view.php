<?php
session_start();
require_once '../../config/funciones_ride.php';
require_once '../../config/funciones_carro.php';
include '../includes/navbar.php'; 

// Verificar que haya ID
$idViaje = intval($_GET['id'] ?? 0);
$viaje = getViajeById($idViaje);

// Si el viaje no existe, redirige al listado con mensaje de error
if (!$viaje) {
    $_SESSION['error'] = 'Ride no encontrado.';
    header('Location: listar_ride.php');
    exit();
}

// verifica que el viaje pertenezca al chofer logueado
if ($viaje['idChofer'] !== $_SESSION['user_id']) {
    die('<div class="alert alert-danger text-center mt-5">
            No tenés permiso para ver este ride.
         </div>');
}

// Obtiene la información del vehículo asociado al viaje
$vehiculo = getVehiculoById($viaje['idVehiculo']);
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalles del Ride - Aventones CR</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

</head>
<body class="bg-light">
<div class="container py-5">
  <div class="card shadow-lg">
    <div class="card-header bg-success text-white">
      <h4 class="mb-0"><i class="fas fa-route"></i> Detalles del Ride</h4>
    </div>
    <div class="card-body">

      <!-- Tabla con la información principal del viaje -->
      <table class="table table-borderless mb-4">
        <tr><th>Nombre del viaje:</th><td><?= htmlspecialchars($viaje['nombreViaje']); ?></td></tr>
        <tr><th>Origen:</th><td><?= htmlspecialchars($viaje['origen']); ?></td></tr>
        <tr><th>Destino:</th><td><?= htmlspecialchars($viaje['destino']); ?></td></tr>
        <tr><th>Fecha:</th><td><?= htmlspecialchars($viaje['fecha']); ?></td></tr>
        <tr><th>Hora de salida:</th><td><?= htmlspecialchars($viaje['horaSalida']); ?></td></tr>
        <tr><th>Hora de llegada:</th><td><?= htmlspecialchars($viaje['horaLlegada']); ?></td></tr>
        <tr><th>Tarifa:</th><td>₡<?= number_format($viaje['tarifa'], 2); ?></td></tr>
        <tr><th>Espacios disponibles:</th><td><?= htmlspecialchars($viaje['espaciosDisponibles']); ?></td></tr>
        
        <!-- Estado del viaje con badge de color dinámico -->
        <tr><th>Estado:</th>
            <td>

            <!-- Determina el color del badge según el estado del viaje -->
              <?php            
              $estado = $viaje['estado'];
              $badge = match ($estado) {
                'activo' => 'success',
                'completado' => 'secondary',
                'cancelado' => 'danger',
                default => 'dark'
              };
              ?>

              <span class="badge bg-<?= $badge; ?>"><?= ucfirst($estado); ?></span>
            </td>
        </tr>
      </table>

      <h5 class="text-success mb-3">Vehículo asociado</h5>
      <?php if ($vehiculo): ?>
        <div class="card border-success mb-4">
          <div class="card-body">
            <p><strong><?= htmlspecialchars($vehiculo['marca']) . ' ' . htmlspecialchars($vehiculo['modelo']); ?></strong></p>
            <p><i class="fas fa-palette"></i> Color: <?= htmlspecialchars($vehiculo['color']); ?></p>
            <p><i class="fas fa-car-side"></i> Placa: <?= htmlspecialchars($vehiculo['placa']); ?></p>
          </div>
        </div>
      <?php else: ?>
        <!-- Si el vehículo no existe o fue eliminado -->
        <div class="alert alert-warning">Vehículo no disponible o eliminado.</div>
      <?php endif; ?>

      <!--  Botón volver al final -->
      <div class="text-center mt-4">
        <a href="listar_ride.php" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Volver a Mis rides
        </a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
