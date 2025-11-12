<?php
session_start();
require_once '../../config/funciones_ride.php';
require_once '../../config/funciones_carro.php';
include '../includes/navbar.php';

// Obtiene el ID del chofer desde la sesión
$idChofer = $_SESSION['user_id'];

// Obtiene todos los viajes publicados por el chofer actual
$viajes = getViajesByChofer($idChofer);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Mis Rides - Aventones CR</title>

  <!-- Carga Bootstrap, íconos y fuentes externas -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container py-5">

    <!-- Título y botón para crear ride -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold text-success">Mis Rides</h2>
      <a href="ride_create.php" class="btn btn-success">+ Publicar Ride</a>
    </div>

    <!-- Si hay rides publicados -->
    <?php if ($viajes): ?>
      <div class="card shadow">
        <div class="card-header bg-success text-white fw-bold">
          Listado de Viajes
        </div>
        <div class="card-body">
          <table class="table table-bordered align-middle">
            <thead class="table-success">
              <tr>
                <th>Origen</th>
                <th>Destino</th>
                <th>Fecha</th>
                <th>Hora Salida</th>
                <th>Tarifa</th>
                <th>Espacios</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>

              <!-- Recorre todos los viajes del chofer -->
              <?php foreach ($viajes as $v): ?>
                <tr>
                  <td><?= htmlspecialchars($v['origen']) ?></td>
                  <td><?= htmlspecialchars($v['destino']) ?></td>
                  <td><?= htmlspecialchars($v['fecha']) ?></td>
                  <td><?= htmlspecialchars($v['horaSalida']) ?></td>
                  <td>₡<?= number_format($v['tarifa'], 2) ?></td>
                  <td><?= htmlspecialchars($v['espaciosDisponibles']) ?></td>

                  <!-- Muestra el estado del ride con un badge de color -->
                  <td>
                    <?php
                    // match selecciona el color del badge según el estado
                    $badge = match ($v['estado']) {
                      'activo' => 'success',
                      'completado' => 'primary',
                      'cancelado' => 'danger',
                      default => 'secondary'
                    };
                    ?>
                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($v['estado']) ?></span>
                  </td>

                  <!-- BOTONES DE ACCIÓN -->
                  <td>
                    <!-- Botón Ver -->
                    <a href="ride_view.php?id=<?= $v['idViaje'] ?>" class="btn btn-outline-primary btn-sm">
                      Ver
                    </a>

                    <!-- Botón Editar -->
                    <a href="ride_edit.php?id=<?= $v['idViaje'] ?>" class="btn btn-outline-warning btn-sm">
                      Editar
                    </a>

                    <!-- Botón que abre el modal -->
                    <button type="button"
                      class="btn btn-outline-danger btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#confirmarEliminar<?= $v['idViaje'] ?>">
                      Eliminar
                    </button>

                    <!-- MODAL Bootstrap -->
                    <div class="modal fade" id="confirmarEliminar<?= $v['idViaje'] ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg">

                          <!-- Encabezado ROJO -->
                          <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-semibold">
                              Confirmar eliminación
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                          </div>

                          <!-- Cuerpo del modal -->
                          <div class="modal-body text-center">
                            <p class="fw-semibold mb-2">
                              ¿Seguro que querés eliminar el ride
                              <span class="text-danger"><?= htmlspecialchars($v['origen']) ?> → <?= htmlspecialchars($v['destino']) ?></span>?
                            </p>
                            <p class="text-muted small mb-0">
                              Esta acción eliminará el viaje y sus reservas asociadas permanentemente.
                            </p>
                          </div>

                          <!-- Pie del modal -->
                          <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <a href="ride_delete.php?id=<?= $v['idViaje'] ?>" class="btn btn-danger">Sí, eliminar</a>
                          </div>

                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Si no hay rides -->
    <?php else: ?>
      <div class="alert alert-secondary text-center">
        No tenés rides publicados.
      </div>
    <?php endif; ?>

    <!-- Botón para volver al panel -->
    <div class="mt-4 text-center">
      <a href="../dashboard_chofer.php" class="btn btn-secondary">Volver al Panel</a>
    </div>

  </div>
</body>

</html>