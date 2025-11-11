<?php
session_start();
require_once '../../config/funciones_carro.php';
include '../includes/navbar.php'; 

// Obtiene el ID del vehículo desde la URL (GET) y lo convierte a entero
// Y Busca los datos del vehículo en la base de datos
$id = intval($_GET['id'] ?? 0);
$vehiculo = getVehiculoById($id);

// Si no se encuentra el vehículo, muestra error y redirige
if (!$vehiculo) {
  $_SESSION['error'] = 'Vehículo no encontrado.';
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
  
  <!-- Bootstrap, íconos y fuentes -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">


</head>
<body class="bg-light">

<!-- Contenedor principal -->
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
          <!-- Si tiene foto, la muestra -->
          <img src="../../uploads/<?= htmlspecialchars($vehiculo['fotografia']); ?>"
              alt="Fotografía del vehículo"
              class="img-fluid rounded shadow-sm"
              style="max-height: 250px; object-fit: cover;">
          <?php else: ?>
            <!-- Si no tiene foto, muestra un ícono genérico -->
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
            
            <!-- Estado con badge de color -->
            <tr>
              <th>Estado:</th>
              <td>
                <!--Determina el color del badge según el estado del vehículo-->
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
