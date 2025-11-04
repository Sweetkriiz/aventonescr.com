<?php
session_start();
require_once '../config/database.php';

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

    if (in_array($accion, ['aceptar', 'rechazar'])) {
        $nuevoEstado = ($accion === 'aceptar') ? 'aceptada' : 'rechazada';

        $stmt = $pdo->prepare("UPDATE Reservas SET estadoReserva = ? WHERE idReserva = ?");
        $stmt->execute([$nuevoEstado, $idReserva]);

        $_SESSION['success'] = " Solicitud {$nuevoEstado} correctamente.";
        header("Location: solicitudes_chofer.php");
        exit();
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
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="fw-bold text-success mb-4">Solicitudes de Pasajeros</h2>

  <?php if (!empty($_SESSION['success'])): ?>
  <div class="alert alert-success">
    <?= $_SESSION['success']; ?>
  </div>
  <?php unset($_SESSION['success']); ?>  <!-- limpia el mensaje después de mostrar -->
<?php endif; ?>


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
  <?php else: ?>
    <div class="alert alert-secondary text-center">No hay solicitudes de pasajeros.</div>
  <?php endif; ?>

  <div class="mt-4 text-center">
    <a href="dashboard_chofer.php" class="btn btn-secondary">Volver al Panel</a>
  </div>
</div>
</body>
</html>
