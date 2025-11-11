<?php
session_start();
require_once '../config/database.php';
include('includes/navbar.php');

// Si el usuario no ha iniciado sesión, lo redirige al login
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// Obtiene el ID del pasajero actual
$idPasajero = $_SESSION['user_id'];

/* CONSULTA PRINCIPAL: Reservas del pasajero actual  */
$sql = "SELECT r.idReserva, r.estadoReserva, r.fechaSolicitud,
               v.nombreViaje, v.origen, v.destino, v.horaSalida, v.horaLlegada, v.tarifa, v.fecha,
               u.nombre AS chofer_nombre
        FROM Reservas r
        INNER JOIN Viajes v ON r.idViaje = v.idViaje
        INNER JOIN Usuarios u ON v.idChofer = u.idUsuario
        WHERE r.idPasajero = :idPasajero
        ORDER BY r.fechaSolicitud DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':idPasajero' => $idPasajero]);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis Viajes - Aventones CR</title>
  
  <!-- Bootstrap, íconos y fuentes -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  
  <!-- Estilos internos -->
  <style>
    body { font-family: 'Poppins', sans-serif; background-color: #f9f9f9; }
    .estado-pendiente { color: #ffc107; font-weight: bold; }
    .estado-aceptada { color: #198754; font-weight: bold; }
    .estado-rechazada { color: #ec710cff; font-weight: bold; }
    .estado-cancelada { color: #eb0606ff; font-weight: bold; }
    .table thead th { background-color: #212529; color: white; }
    .btn-sm { padding: 5px 10px; }
  </style>
</head>
<body>

<div class="container my-5">
    <h2 class="text-center mb-4 fw-bold" style="color:#285936;">Mis Viajes</h2>
  
    <!-- Si hay reservas, muestra la tabla -->
  <?php if (count($reservas) > 0): ?>
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Nombre del Viaje</th>
                <th>Chofer</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Fecha</th>
                <th>Tarifa</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reservas as $r): ?>
              <tr id="reserva-<?= $r['idReserva'] ?>">
                <td><strong><?= htmlspecialchars($r['nombreViaje']) ?></strong></td>
                <td><?= htmlspecialchars($r['chofer_nombre']) ?></td>
                <td><?= htmlspecialchars($r['origen']) ?></td>
                <td><?= htmlspecialchars($r['destino']) ?></td>
                <td><?= htmlspecialchars($r['fecha']) ?></td>
                <td><span class="badge bg-success">₡<?= number_format($r['tarifa'], 2) ?></span></td>
                
                <!-- Estado visual -->
                <td>
                  <span class="estado-<?= strtolower($r['estadoReserva']) ?>" id="estado-<?= $r['idReserva'] ?>">
                    <?= ucfirst($r['estadoReserva']) ?>
                  </span>
                </td>

                <!-- Botón cancelar (solo si está pendiente) -->
                <td>
                  <?php if ($r['estadoReserva'] === 'pendiente'): ?>
                    <button class="btn btn-outline-danger btn-sm btn-cancelar" data-bs-toggle="modal" data-bs-target="#cancelarModal" data-id="<?= $r['idReserva'] ?>">
                      <i class="bi bi-x-circle"></i>
                    </button>
                  <?php else: ?>
                    <button class="btn btn-outline-secondary btn-sm" disabled>
                      <i class="bi bi-dash-circle"></i>
                    </button>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- Si no hay reservas -->
  <?php else: ?>
    <div class="alert alert-info text-center mt-5 shadow-sm">
      No tienes viajes reservados aún.
    </div>
  <?php endif; ?>
</div>

<!-- Modal Bootstrap -->
<div class="modal fade" id="cancelarModal" tabindex="-1" aria-labelledby="cancelarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Cancelar reserva</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Seguro que deseas cancelar esta reserva?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button type="button" class="btn btn-danger" id="confirmarCancelar">Sí, cancelar</button>
      </div>
    </div>
  </div>
</div>

<script>
let reservaSeleccionada = null;

// Detectar qué reserva se quiere cancelar
document.querySelectorAll('.btn-cancelar').forEach(boton => {
  boton.addEventListener('click', () => {
    reservaSeleccionada = boton.getAttribute('data-id');
  });
});

// Simula cancelación visual al confirmar (sin recargar)
document.getElementById('confirmarCancelar').addEventListener('click', () => {
  if (!reservaSeleccionada) return;
  
  // Cambia el estado en pantalla
  const estado = document.getElementById(`estado-${reservaSeleccionada}`);
  estado.textContent = "Cancelada";
  estado.className = "estado-cancelada";
  
  // Desactiva el botón de cancelar
  const fila = document.getElementById(`reserva-${reservaSeleccionada}`);
  const boton = fila.querySelector('.btn-cancelar');
  boton.remove();
  
  // Cierra el modal
  const modal = bootstrap.Modal.getInstance(document.getElementById('cancelarModal'));
  modal.hide();
});
</script>

<footer class="text-center py-3 bg-dark text-white mt-5">
  © 2025 Aventones CR
</footer>

</body>
</html>
