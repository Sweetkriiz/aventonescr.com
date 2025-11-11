<?php

// Inicia o reanuda la sesión del usuario
session_start();

require_once '../../config/funciones_ride.php';
require_once '../../config/funciones_carro.php';
include '../includes/navbar.php'; 

// Obtiene el ID del chofer desde la sesión actual
$idChofer = $_SESSION['user_id'];
// Arreglo para guardar errores de validación
$errors = [];

// Obtener solo vehículos aprobados
$vehiculosAprobados = getVehiculosAprobados($idChofer);

// Si el formulario fue enviado por método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura y limpia los datos enviados por el formulario
    $idVehiculo = $_POST['idVehiculo'];
    $origen = trim($_POST['origen']);
    $destino = trim($_POST['destino']);
    $fecha = $_POST['fecha'];
    $horaSalida = $_POST['horaSalida'];
    $horaLlegada = $_POST['horaLlegada'];
    $tarifa = $_POST['tarifa'];
    $espaciosDisponibles = $_POST['espaciosDisponibles'];

    //  Genera nombre de viaje automáticamente
    // Ejemplo: "San José a Alajuela - 10/11/2025"
    $nombreViaje = ucfirst($origen) . " a " . ucfirst($destino) . " - " . date('d/m/Y', strtotime($fecha));

    // Valida los datos antes de insertar (función definida en funciones_ride.php)
    $errors = validateViaje($origen, $destino, $fecha, $horaSalida, $horaLlegada, $tarifa, $espaciosDisponibles);
    
    // Si no hay errores, intenta crear el viaje en la base de datos
    if (empty($errors)) {
        // Si se crea con éxito, muestra mensaje y redirige al listado
        if (createViaje($idChofer, $idVehiculo, $nombreViaje, $origen, $destino, $fecha, $horaSalida, $horaLlegada, $tarifa, $espaciosDisponibles)) {
            $_SESSION['success'] = "Ride publicado correctamente.";
            header("Location: listar_ride.php");
            exit();
        } else {
            // Si ocurre algún error en la creación, se agrega al arreglo de errores
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container py-5">
<h2 class="text-success mb-4"> Publicar nuevo Ride</h2>

 <!-- Muestra errores de validación si existen -->
  <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
          <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
      </div>
  <?php endif; ?>

    <!-- Formulario para crear un nuevo ride -->
    <form method="POST">
        <!-- Selección del vehículo -->
        <div class="mb-3">
            <label>Seleccioná tu vehículo</label>
            <select name="idVehiculo" class="form-select" required>
                <option value="">-- Elegí un vehículo aprobado --</option>
              
                <!-- Lista los vehículos aprobados del chofer -->
                <?php foreach ($vehiculosAprobados as $v): ?>
                    <option value="<?= $v['idVehiculo']; ?>">
                        <?= htmlspecialchars($v['marca'] . ' ' . $v['modelo'] . ' (' . $v['placa'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- Campos de datos del viaje -->
        <div class="mb-3"><label>Origen</label><input type="text" name="origen" class="form-control" required></div>
        <div class="mb-3"><label>Destino</label><input type="text" name="destino" class="form-control" required></div>
        <div class="mb-3"><label>Fecha</label><input type="date" name="fecha" class="form-control" required></div>
        <div class="mb-3"><label>Hora de salida</label><input type="time" name="horaSalida" class="form-control" required></div>
        <div class="mb-3"><label>Hora de llegada</label><input type="time" name="horaLlegada" class="form-control" required></div>
        <div class="mb-3"><label>Tarifa (₡)</label><input type="number" name="tarifa" step="0.01" class="form-control" required></div>
        <div class="mb-3"><label>Espacios disponibles</label><input type="number" name="espaciosDisponibles" min="1" class="form-control" required></div>
        
        <!-- Botones de acción -->
        <button type="submit" class="btn btn-success">Publicar Ride</button>
        <a href="listar_ride.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>