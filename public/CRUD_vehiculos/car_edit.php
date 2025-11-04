<?php
session_start();
require_once '../../config/funciones_carro.php';


$idVehiculo = intval($_GET['id']);
$vehiculo = getVehiculoById($idVehiculo);

if (!$vehiculo) {
  die('<div class="alert alert-danger text-center mt-5">Vehículo no encontrado.</div>');
}

$mensaje = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $marca = trim($_POST['marca']);
  $modelo = trim($_POST['modelo']);
  $anio = trim($_POST['anio']);
  $color = trim($_POST['color']);
  $placa = trim($_POST['placa']);

  if (actualizarVehiculo($idVehiculo, $marca, $modelo, $anio, $color, $placa)) {
    $_SESSION['success'] = "Vehículo actualizado correctamente. 
    Deberá ser aprobado nuevamente por un administrador.";
    header('Location: listar_vehiculo.php');
    exit;
  } else {
    $error = '<div class="alert alert-danger">Error al actualizar el vehículo.</div>';
  }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Vehículo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="text-success mb-4">Editar Vehículo</h2>
  <?= $error ?? '' ?>
  <form method="POST">
    <div class="mb-3"><label>Marca</label><input type="text" name="marca" value="<?= htmlspecialchars($vehiculo['marca']) ?>" class="form-control" required></div>
    <div class="mb-3"><label>Modelo</label><input type="text" name="modelo" value="<?= htmlspecialchars($vehiculo['modelo']) ?>" class="form-control" required></div>
    <div class="mb-3"><label>Año</label><input type="number" name="anio" value="<?= htmlspecialchars($vehiculo['anio']) ?>" class="form-control" required></div>
    <div class="mb-3"><label>Color</label><input type="text" name="color" value="<?= htmlspecialchars($vehiculo['color']) ?>" class="form-control" required></div>
    <div class="mb-3"><label>Placa</label><input type="text" name="placa" value="<?= htmlspecialchars($vehiculo['placa']) ?>" class="form-control" required></div>
    <button type="submit" class="btn btn-success">Guardar Cambios</button>
    <a href="listar_vehiculo.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
</body>
</html>
