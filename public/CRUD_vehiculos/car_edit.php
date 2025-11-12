<?php
session_start();
require_once '../../config/funciones_carro.php';

include '../includes/navbar.php';

// Obtiene el ID del vehículo desde la URL y lo convierte a entero
// Y Busca el vehículo en la base de datos
$idVehiculo = intval($_GET['id']);
$vehiculo = getVehiculoById($idVehiculo);

// Si no se encuentra, se muestra mensaje de error y se detiene el script
if (!$vehiculo) {
  die('<div class="alert alert-danger text-center mt-5">Vehículo no encontrado.</div>');
}

// Variables para manejar mensajes de éxito o error
$mensaje = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Limpia y obtiene los valores del formulario
  $marca = trim($_POST['marca']);
  $modelo = trim($_POST['modelo']);
  $anio = trim($_POST['anio']);
  $color = trim($_POST['color']);
  $placa = trim($_POST['placa']);

  // Llama a la función para actualizar el vehículo
  if (actualizarVehiculo($idVehiculo, $marca, $modelo, $anio, $color, $placa)) {
    // Mensaje de éxito y redirección
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

</head>

<body class="bg-light">
  <div class="container py-5">
    <h2 class="text-success mb-4">Editar Vehículo</h2>
    <?= $error ?? '' ?>

    <!-- Formulario de edición -->
    <form method="POST">
      <div class="mb-3"><label>Marca</label><input type="text" name="marca" value="<?= htmlspecialchars($vehiculo['marca']) ?>" class="form-control" required></div>
      <div class="mb-3"><label>Modelo</label><input type="text" name="modelo" value="<?= htmlspecialchars($vehiculo['modelo']) ?>" class="form-control" required></div>
      <div class="mb-3"><label>Año</label><input type="number" name="anio" value="<?= htmlspecialchars($vehiculo['anio']) ?>" class="form-control" required></div>
      <div class="mb-3"><label>Color</label><input type="text" name="color" value="<?= htmlspecialchars($vehiculo['color']) ?>" class="form-control" required></div>
      <div class="mb-3"><label>Placa</label><input type="text" name="placa" value="<?= htmlspecialchars($vehiculo['placa']) ?>" class="form-control" required></div>

      <!-- Campo opcional para actualizar la fotografía -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Fotografía del vehículo (opcional)</label>
        <input type="file" name="fotografia" class="form-control" accept="image/*">
        <div class="form-text text-muted">Si no seleccionás una nueva imagen, se conservará la actual.</div>
      </div>

      <!-- Botones de acción -->
      <button type="submit" class="btn btn-success">Guardar Cambios</button>
      <a href="listar_vehiculo.php" class="btn btn-secondary">Cancelar</a>
    </form>
  </div>
</body>

</html>