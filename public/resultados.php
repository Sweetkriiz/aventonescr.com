<?php
session_start();
require_once '../config/database.php';
include('includes/navbar.php');

// --- Obtener los datos enviados desde index.php ---
$origen = $_GET['origen'] ?? '';
$destino = $_GET['destino'] ?? '';
$fecha = $_GET['fecha'] ?? '';
$pasajeros = $_GET['pasajeros'] ?? 1;

// --- Consulta dinámica a la base de datos ---
$sql = "SELECT v.*, u.nombre AS chofer_nombre, ve.modelo, ve.marca, ve.color
        FROM Viajes v
        INNER JOIN Usuarios u ON v.idChofer = u.idUsuario
        INNER JOIN Vehiculos ve ON v.idVehiculo = ve.idVehiculo
        WHERE v.lugarSalida LIKE :origen 
          AND v.destino LIKE :destino
          AND v.espaciosDisponibles >= :pasajeros";

$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':origen' => "%$origen%",
  ':destino' => "%$destino%",
  ':pasajeros' => $pasajeros
]);

$viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultados de búsqueda - Aventones CR</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/index.css">
    
  <style>
  /* Solo afecta esta página */
  .navbar {
    background-color: #b8a9a2ff !important; /* mismo café oscuro del index */
  }
</style>

</head>

<body style="font-family: 'Poppins', sans-serif; background-color: #f9f9f9;">

  <div class="container my-5">
    <h2 class="text-center mb-4 fw-bold" style="color:#285936;">Resultados de tu búsqueda</h2>

    <?php if (count($viajes) > 0): ?>
      <div class="row g-4">
        <?php foreach ($viajes as $viaje): ?>
          <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <h5 class="card-title text-success fw-bold"><?= htmlspecialchars($viaje['nombreViaje']) ?></h5>
                <p class="mb-1"><strong>Chofer:</strong> <?= htmlspecialchars($viaje['chofer_nombre']) ?></p>
                <p class="mb-1"><strong>Vehículo:</strong> <?= htmlspecialchars($viaje['marca']) . " " . htmlspecialchars($viaje['modelo']) ?> (<?= htmlspecialchars($viaje['color']) ?>)</p>
                <p class="mb-1"><strong>Salida:</strong> <?= htmlspecialchars($viaje['lugarSalida']) ?> - <?= htmlspecialchars($viaje['horaSalida']) ?></p>
                <p class="mb-1"><strong>Destino:</strong> <?= htmlspecialchars($viaje['destino']) ?> - <?= htmlspecialchars($viaje['horaLlegada']) ?></p>
                <p class="mb-1"><strong>Días:</strong> <?= htmlspecialchars($viaje['diasSemana']) ?></p>
                <p class="mb-1"><strong>Tarifa:</strong> ₡<?= number_format($viaje['tarifa'], 2) ?></p>
                <p><strong>Espacios disponibles:</strong> <?= htmlspecialchars($viaje['espaciosDisponibles']) ?></p>

                <?php if (isset($_SESSION['usuario_id'])): ?>
                  <a href="reservar.php?idViaje=<?= $viaje['idViaje'] ?>" class="btn btn-success w-100">Reservar cupo</a>
                <?php else: ?>
                  <a href="login.php" class="btn btn-outline-success w-100">Inicia sesión para reservar</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-warning text-center mt-5 shadow-sm">
        <strong>No hay viajes disponibles</strong> que coincidan con tu búsqueda.
      </div>
    <?php endif; ?>

    <div class="text-center mt-4">
      <a href="index.php" class="btn btn-secondary px-4">Volver al inicio</a>
    </div>
  </div>

  <footer class="text-center py-3 bg-dark text-white mt-5">
    © 2025 Aventones CR
  </footer>

</body>
</html>
