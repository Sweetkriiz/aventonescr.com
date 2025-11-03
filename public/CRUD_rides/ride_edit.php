<?php
session_start();
require_once '../../config/funciones_ride.php';

$idViaje = intval($_GET['id'] ?? 0);
$viaje = getViajeById($idViaje);

if (!$viaje) {
  die('<div class="alert alert-danger">Viaje no encontrado.</div>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $origen = $_POST['origen'];
  $destino = $_POST['destino'];
  $fecha = $_POST['fecha'];
  $horaSalida = $_POST['horaSalida'];
  $horaLlegada = $_POST['horaLlegada'];
  $tarifa = $_POST['tarifa'];
  $espacios = $_POST['espaciosDisponibles'];

  if (updateViaje($idViaje, $origen, $destino, $fecha, $horaSalida, $horaLlegada, $tarifa, $espacios)) {
    $_SESSION['success'] = "Ride actualizado correctamente.";
    header('Location: listar_ride.php');
    exit;
  } else {
    $error = '<div class="alert alert-danger">Error al actualizar el ride.</div>';
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Ride</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="text-success mb-4">Editar Ride</h2>
  <?= $error ?? '' ?>
  <form method="POST">
    <div class="mb-3"><label>Origen</label><input type="text" name="origen" value="<?= htmlspecialchars($viaje['origen']) ?>" class="form-control" required></div>
    <div class="mb-3"><label>Destino</label><input type="text" name="destino" value="<?= htmlspecialchars($viaje['destino']) ?>" class="form-control" required></div>
    <div class="mb-3"><label>Fecha</label><input type="date" name="fecha" value="<?= htmlspecialchars($viaje['fecha']) ?>" class="form-control" required></div>
    <div class="mb-3"><label>Hora de salida</label><input type="time" name="horaSalida" value="<?= htmlspecialchars($viaje['horaSalida']) ?>" class="form-control" required></div>
    <div class="mb-3"><label>Hora de llegada</label><input type="time" name="horaLlegada" value="<?= htmlspecialchars($viaje['horaLlegada']) ?>" class="form-control" required></div>
    <div class="mb-3"><label>Tarifa (₡)</label><input type="number" step="0.01" name="tarifa" value="<?= htmlspecialchars($viaje['tarifa']) ?>" class="form-control" required></div>
    <div class="mb-3"><label>Espacios disponibles</label><input type="number" name="espaciosDisponibles" value="<?= htmlspecialchars($viaje['espaciosDisponibles']) ?>" class="form-control" required></div>
    <button type="submit" class="btn btn-success">Guardar cambios</button>
    <a href="listar_ride.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
</body>
</html>
