<?php
session_start();
require_once '../config/database.php';
include('includes/navbar.php');

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$idPasajero = $_SESSION['user_id'];

$sql = "SELECT r.idReserva, r.estadoReserva, r.fechaSolicitud,
               v.nombreViaje, v.lugarSalida, v.destino, v.horaSalida, v.horaLlegada, v.tarifa,
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

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Poppins', sans-serif; background-color: #f9f9f9; }
    .estado-pendiente { color: #ffc107; font-weight: bold; }
    .estado-aceptada { color: #198754; font-weight: bold; }
    .estado-rechazada { color: #dc3545; font-weight: bold; }
    .estado-cancelada { color: #6c757d; font-weight: bold; }
    .table thead th { background-color: #212529; color: white; }
    .btn-sm { padding: 5px 10px; }
  </style>
</head>
<body>

<div class="container my-5">
  <h2 class="text-center mb-4 fw-bold" style="color:#285936;">Mis Viajes</h2>

  <?php if (count($reservas) > 0): ?>
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre del Viaje</th>
                <th>Chofer</th>
                <th>Salida</th>
                <th>Destino</th>
                <th>Tarifa</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reservas as $r): ?>
              <tr id="reserva-<?= $r['idReserva'] ?>">
                <td><?= $r['idReserva'] ?></td>
                <td><strong><?= htmlspecialchars($r['nombreViaje']) ?></strong></td>
                <td><?= htmlspecialchars($r['chofer_nombre']) ?></td>
                <td><?= htmlspecialchars($r['horaSalida']) ?></td>
                <td><?= htmlspecialchars($r['horaLlegada']) ?></td>
                <td><span class="badge bg-success">₡<?= number_format($r['tarifa'], 2) ?></span></td>
                <td>
                  <span class="estado-<?= strtolower($r['estadoReserva']) ?>" id="estado-<?= $r['idReserva'] ?>">
                    <?= ucfirst($r['estadoReserva']) ?>
                  </span>
                </td>
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

// Confirmar cancelación (solo visual)
document.getElementById('confirmarCancelar').addEventListener('click', () => {
  if (!reservaSeleccionada) return;

  const estado = document.getElementById(`estado-${reservaSeleccionada}`);
  estado.textContent = "Cancelada";
  estado.className = "estado-cancelada";

  const fila = document.getElementById(`reserva-${reservaSeleccionada}`);
  const boton = fila.querySelector('.btn-cancelar');
  boton.remove();

  const modal = bootstrap.Modal.getInstance(document.getElementById('cancelarModal'));
  modal.hide();
});
</script>

<footer class="text-center py-3 bg-dark text-white mt-5">
  © 2025 Aventones CR
</footer>

</body>
</html>
