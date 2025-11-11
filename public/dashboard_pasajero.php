<?php
session_start();
require_once '../config/database.php';
include('includes/navbar.php');

//Si el usuario no ha iniciado sesión, lo redirige al login
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

//Obtiene información básica del pasajero actual
$idPasajero = $_SESSION['user_id'];
$rol = $_SESSION['rol'] ?? 'pasajero';

//Contar reservas por estado (pendientes, aceptadas, rechazadas
$sqlResumen = "SELECT 
                  SUM(CASE WHEN estadoReserva = 'pendiente' THEN 1 ELSE 0 END) AS pendientes,
                  SUM(CASE WHEN estadoReserva = 'aceptada' THEN 1 ELSE 0 END) AS aceptadas,
                  SUM(CASE WHEN estadoReserva = 'rechazada' THEN 1 ELSE 0 END) AS rechazadas
               FROM Reservas
               WHERE idPasajero = :id";
$stmt = $pdo->prepare($sqlResumen);
$stmt->execute([':id' => $idPasajero]);
$resumen = $stmt->fetch(PDO::FETCH_ASSOC);

//CONSULTA DE SOLICITUDES RECIENTES (últimos 5 viajes)
$sqlSolicitudes = "SELECT r.estadoReserva, v.nombreViaje, v.origen, v.destino, v.fecha
                   FROM Reservas r
                   INNER JOIN Viajes v ON r.idViaje = v.idViaje
                   WHERE r.idPasajero = :id
                   ORDER BY r.fechaSolicitud DESC
                   LIMIT 5";
$stmt = $pdo->prepare($sqlSolicitudes);
$stmt->execute([':id' => $idPasajero]);
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);

//  Verificar si tiene vehículo y su estado
$sqlVehiculo = "SELECT estado FROM Vehiculos WHERE idChofer = :id LIMIT 1";
$stmt = $pdo->prepare($sqlVehiculo);
$stmt->execute([':id' => $idPasajero]);
$vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

// Si el vehículo fue aprobado, actualizar el rol automáticamente
if ($vehiculo && $vehiculo['estado'] === 'aprobado' && $_SESSION['rol'] !== 'chofer') {
    $updateRol = $pdo->prepare("UPDATE Usuarios SET rol = 'chofer' WHERE idUsuario = :id");
    $updateRol->execute([':id' => $idPasajero]);
    $_SESSION['rol'] = 'chofer'; // Refrescar también en sesión
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel del Pasajero - Aventones CR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/pasajero.css">
</head>
<body>

  <!--  Banner -->
  <div class="banner">
    <div class="banner-content">
      <h2>¡Hola, <?= htmlspecialchars($_SESSION['nombreUsuario'] ?? 'Pasajero') ?>!</h2>
      <p>Bienvenido a tu panel personal. Aquí podés ver tus viajes y convertirte en chofer.</p>
    </div>
  </div>

  <!--  Resumen de reservas -->
  <div class="container my-5 main-content">
    <h3 class="section-title mb-4 fw-bold text-success">Resumen de tus reservas</h3>
    <div class="row text-center mb-5">
      <div class="col-md-4">
        <div class="stats-card shadow-sm">
          <h5>Pendientes</h5>
          <p class="stats-number"><?= $resumen['pendientes'] ?? 0 ?></p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stats-card shadow-sm">
          <h5>Aceptadas</h5>
          <p class="stats-number"><?= $resumen['aceptadas'] ?? 0 ?></p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stats-card shadow-sm">
          <h5>Rechazadas</h5>
          <p class="stats-number"><?= $resumen['rechazadas'] ?? 0 ?></p>
        </div>
      </div>
    </div>

    <!--  Solicitudes recientes -->
    <div class="card shadow-sm mb-5">
      <div class="card-header bg-success text-white fw-bold">
        <i class="bi bi-clock-history me-2"></i> Solicitudes recientes
      </div>
      <div class="card-body">
        <?php if (count($solicitudes) > 0): ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-success">
                <tr>
                  <th>Viaje</th>
                  <th>Origen</th>
                  <th>Destino</th>
                  <th>Fecha</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($solicitudes as $s): ?>
                <tr>
                  <td><?= htmlspecialchars($s['nombreViaje']) ?></td>
                  <td><?= htmlspecialchars($s['origen']) ?></td>
                  <td><?= htmlspecialchars($s['destino']) ?></td>
                  <td><?= htmlspecialchars($s['fecha']) ?></td>
                  <td>
                    <?php
                      $estado = strtolower($s['estadoReserva']);
                      $color = match($estado) {
                        'pendiente' => 'warning',
                        'aceptada' => 'success',
                        'rechazada' => 'danger',
                        default => 'secondary'
                      };
                    ?>
                    <span class="badge bg-<?= $color ?>"><?= ucfirst($s['estadoReserva']) ?></span>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p class="text-muted mb-0">No tenés solicitudes recientes.</p>
        <?php endif; ?>
      </div>
    </div>

     <!-- === CONVERSIÓN A CHOFER === -->      
    <?php if ($rol === 'pasajero' && !$vehiculo): ?>
       <!-- Opción para registrar vehículo -->
      <div class="card border-0 shadow-sm text-center p-4 bg-light">
        <h4 class="fw-bold text-success mb-2">¿Querés ser chofer?</h4>
        <p>Registrá tu primer vehículo y empezá a ofrecer viajes a otros usuarios.</p>
        <a href="../public/registrar_vehiculo_pasajero.php" class="btn btn-success">
          <i class="bi bi-car-front"></i> Registrar mi vehículo
        </a>
      </div>

    <!-- Estado pendiente de aprobación -->
    <?php elseif ($vehiculo && $vehiculo['estado'] === 'pendiente'): ?>
      <div class="alert alert-warning text-center mt-4 shadow-sm">
        Tu solicitud de chofer está en revisión. Pronto recibirás la aprobación 
      </div>
      
    <!-- Vehículo aprobado -->
    <?php elseif ($vehiculo && $vehiculo['estado'] === 'aprobado'): ?>
      <div class="alert alert-success text-center mt-4 shadow-sm">
        ¡Tu vehículo fue aprobado! Ya podés acceder al panel de chofer 
      </div>
      <a href="dashboard_chofer.php" class="btn btn-success mt-3 px-4 d-block mx-auto" style="width:max-content;">
        Ir al panel de chofer
      </a>
    <?php endif; ?>
  </div>

  <!--  Footer -->
  <footer class="text-center py-3 bg-dark text-white mt-5" style="width:100%;">
    © 2025 Aventones CR
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
