<?php
session_start();
require_once '../../config/funciones_ride.php';
require_once '../../config/funciones_carro.php';

$idChofer = $_SESSION['user_id'];
$errors = [];

// Obtener solo vehículos aprobados
$vehiculosAprobados = getVehiculosAprobados($idChofer);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idVehiculo = $_POST['idVehiculo'];
    $origen = trim($_POST['origen']);
    $destino = trim($_POST['destino']);
    $fecha = $_POST['fecha'];
    $horaSalida = $_POST['horaSalida'];
    $horaLlegada = $_POST['horaLlegada'];
    $tarifa = $_POST['tarifa'];
    $espaciosDisponibles = $_POST['espaciosDisponibles'];

    //  Genera nombre de viaje automáticamente
    $nombreViaje = ucfirst($origen) . " a " . ucfirst($destino) . " - " . date('d/m/Y', strtotime($fecha));

    //  Validar datos
    $errors = validateViaje($origen, $destino, $fecha, $horaSalida, $horaLlegada, $tarifa, $espaciosDisponibles);

    if (empty($errors)) {
        //  Crear viaje
        if (createViaje($idChofer, $idVehiculo, $nombreViaje, $origen, $destino, $fecha, $horaSalida, $horaLlegada, $tarifa, $espaciosDisponibles)) {
            $_SESSION['success'] = "Ride publicado correctamente.";
            header("Location: listar_ride.php");
            exit();
        } else {
            $errors[] = "Error al publicar el ride.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Publicar Ride</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="text-success mb-4"> Publicar nuevo Ride</h2>

  <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
          <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
      </div>
  <?php endif; ?>

  <form method="POST">
      <div class="mb-3">
          <label>Seleccioná tu vehículo</label>
          <select name="idVehiculo" class="form-select" required>
              <option value="">-- Elegí un vehículo aprobado --</option>
              <?php foreach ($vehiculosAprobados as $v): ?>
                  <option value="<?= $v['idVehiculo']; ?>">
                      <?= htmlspecialchars($v['marca'] . ' ' . $v['modelo'] . ' (' . $v['placa'] . ')'); ?>
                  </option>
              <?php endforeach; ?>
          </select>
      </div>

      <div class="mb-3"><label>Origen</label><input type="text" name="origen" class="form-control" required></div>
      <div class="mb-3"><label>Destino</label><input type="text" name="destino" class="form-control" required></div>
      <div class="mb-3"><label>Fecha</label><input type="date" name="fecha" class="form-control" required></div>
      <div class="mb-3"><label>Hora de salida</label><input type="time" name="horaSalida" class="form-control" required></div>
      <div class="mb-3"><label>Hora de llegada</label><input type="time" name="horaLlegada" class="form-control" required></div>
      <div class="mb-3"><label>Tarifa (₡)</label><input type="number" name="tarifa" step="0.01" class="form-control" required></div>
      <div class="mb-3"><label>Espacios disponibles</label><input type="number" name="espaciosDisponibles" min="1" class="form-control" required></div>

      <button type="submit" class="btn btn-success">Publicar Ride</button>
      <a href="listar_ride.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
</body>
</html>