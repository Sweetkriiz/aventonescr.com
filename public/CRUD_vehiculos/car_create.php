<?php
session_start();
require_once '../../config/funciones_carro.php';

$idChofer = $_SESSION['user_id'] ?? null;
$errors = [];

if (!$idChofer) {
    die('<div class="alert alert-danger text-center mt-5">No se detectó usuario logueado.</div>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $anio = trim($_POST['anio'] ?? '');
    $color = trim($_POST['color'] ?? '');
    $placa = trim($_POST['placa'] ?? '');

    $errors = validateVehiculo($marca, $modelo, $anio, $color, $placa);

    // Manejar subida de imagen
    $fotoNombre = null;
    if (!empty($_FILES['fotografia']['name'])) {
        $directorio = __DIR__ . '/../../uploads/vehiculos/';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $extension = pathinfo($_FILES['fotografia']['name'], PATHINFO_EXTENSION);
        $nombreArchivo = time() . '_' . uniqid() . '.' . $extension;
        $rutaDestino = $directorio . $nombreArchivo;

        $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array(strtolower($extension), $permitidos)) {
            if (move_uploaded_file($_FILES['fotografia']['tmp_name'], $rutaDestino)) {
                $fotoNombre = $nombreArchivo;
            } else {
                $errors[] = "Error al subir la fotografía.";
            }
        } else {
            $errors[] = "Formato de imagen no permitido (solo JPG, PNG o WEBP).";
        }
    }

    if (empty($errors)) {
        if (createVehiculo($idChofer, $marca, $modelo, $anio, $color, $placa, $fotoNombre)) {
            $_SESSION['success'] = "✅ Vehículo agregado correctamente. Pendiente de aprobación.";
            header("Location: listar_vehiculo.php");
            exit();
        } else {
            $errors[] = "Error al agregar el vehículo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Vehículo - Aventones CR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h3 class="text-success mb-4">Agregar nuevo vehículo</h3>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm bg-white border-0 rounded-3">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Marca</label>
                <input type="text" name="marca" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Modelo</label>
                <input type="text" name="modelo" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">Año</label>
                <input type="number" name="anio" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">Color</label>
                <input type="text" name="color" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold">Placa</label>
                <input type="text" name="placa" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Fotografía del vehículo</label>
            <input type="file" name="fotografia" class="form-control" accept="image/*">
            <div class="form-text text-muted">Formatos permitidos: JPG, PNG o WEBP (máx. 2 MB)</div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="listar_vehiculo.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-success">Guardar vehículo</button>
        </div>
    </form>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
