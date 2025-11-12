<?php
session_start();
require_once '../config/database.php';

include 'includes/navbar.php';


// Verificar sesión
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

$idChofer = $_SESSION['user_id'];

// Procesar acciones (aceptar / rechazar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'], $_POST['idReserva'])) {
  $accion = $_POST['accion'];
  $idReserva = intval($_POST['idReserva']);
  // Validar acción permitida
  if (in_array($accion, ['aceptar', 'rechazar'])) {
    $nuevoEstado = ($accion === 'aceptar') ? 'aceptada' : 'rechazada';
    // Actualiza el estado de la reserva
    $stmt = $pdo->prepare("UPDATE Reservas SET estadoReserva = ? WHERE idReserva = ?");
    $stmt->execute([$nuevoEstado, $idReserva]);


    echo "<div class='alert alert-success text-center'>Solicitud {$nuevoEstado} correctamente.</div>";
  }
}

//  Obtener todas las reservas relacionadas a viajes del chofer
$query = "
SELECT r.idReserva, r.estadoReserva, r.fechaSolicitud,
       u.nombre AS pasajero, u.correo, u.telefono,
       v.origen, v.destino, v.fecha, v.horaSalida, v.tarifa
FROM Reservas r
JOIN Viajes v ON r.idViaje = v.idViaje
JOIN Usuarios u ON r.idPasajero = u.idUsuario
WHERE v.idChofer = ?
ORDER BY r.fechaSolicitud DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute([$idChofer]);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Solicitudes de Pasajeros - Aventones CR</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

</head>

<body class="bg-light">
  <div class="container py-5">
    <h2 class="fw-bold text-success mb-4">Solicitudes de Pasajeros</h2>

    <!-- Tabla con las solicitudes de los pasajeros -->

    <?php if ($reservas): ?>
      <div class="card shadow">
        <div class="card-header bg-success text-white fw-bold">Solicitudes Recibidas</div>
        <div class="card-body">
          <table class="table table-bordered align-middle">
            <thead class="table-success">
              <tr>
                <th>Pasajero</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Viaje</th>
                <th>Tarifa</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($reservas as $r): ?>
                <tr>
                  <td><?= htmlspecialchars($r['pasajero']) ?></td>
                  <td><?= htmlspecialchars($r['correo']) ?></td>
                  <td><?= htmlspecialchars($r['telefono']) ?></td>
                  <td><?= htmlspecialchars($r['origen']) ?> → <?= htmlspecialchars($r['destino']) ?></td>
                  <td>₡<?= number_format($r['tarifa'], 2) ?></td>
                  <td><?= htmlspecialchars($r['fecha']) ?></td>
                  <td><?= htmlspecialchars($r['horaSalida']) ?></td>
                  <!-- Estado con badge de color -->
                  <td>
                    <?php
                    $badge = match ($r['estadoReserva']) {
                      'aceptada' => 'success',
                      'rechazada' => 'danger',
                      'cancelada' => 'secondary',
                      default => 'warning text-dark'
                    };
                    ?>
                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($r['estadoReserva']) ?></span>
                  </td>
                  <!-- Botones de acción -->
                  <td>
                    <?php if ($r['estadoReserva'] === 'pendiente'): ?>
                      <form method="POST" style="display:inline;">
                        <input type="hidden" name="idReserva" value="<?= $r['idReserva'] ?>">
                        <button type="submit" name="accion" value="aceptar" class="btn btn-success btn-sm">Aceptar</button>
                        <button type="submit" name="accion" value="rechazar" class="btn btn-danger btn-sm">Rechazar</button>
                      </form>
                    <?php else: ?>
                      <small class="text-muted">Sin acciones</small>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Si no hay solicitudes -->
    <?php else: ?>
      <div class="alert alert-secondary text-center">No hay solicitudes de pasajeros.</div>
    <?php endif; ?>
    <!-- Botón de regreso -->
    <div class="mt-4 text-center">
      <a href="dashboard_chofer.php" class="btn btn-secondary">Volver al Panel</a>
    </div>
  </div>
</body>

</html>