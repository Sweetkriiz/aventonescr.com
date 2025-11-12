<?php
session_start();
require_once '../../config/funciones_carro.php';
include '../includes/navbar.php'; 

// Obtiene el ID del chofer logueado desde la sesión
$idChofer = $_SESSION['user_id'] ?? null;
// Array para almacenar errores de validación o ejecución
$errors = [];

// Si no se detecta usuario en sesión, se muestra error y se detiene el script
if (!$idChofer) {
    die('<div class="alert alert-danger text-center mt-5">No se detectó usuario logueado.</div>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpieza y obtención de datos del formulario
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $anio = trim($_POST['anio'] ?? '');
    $color = trim($_POST['color'] ?? '');
    $placa = trim($_POST['placa'] ?? '');
    
    // Valida los campos con una función definida en funciones_carro.php
    $errors = validateVehiculo($marca, $modelo, $anio, $color, $placa);

   // Manejar subida de imagen
    $fotoNombre = null;
    if (!empty($_FILES['fotografia']['name'])) {
        // Define el directorio donde se guardarán las fotos (../uploads/)
        $directorio = __DIR__ . '/../uploads/';
        // Crea la carpeta si no existe
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        // Obtiene la extensión del archivo subido
        $extension = pathinfo($_FILES['fotografia']['name'], PATHINFO_EXTENSION);
        // Genera un nombre único para evitar conflictos
        $nombreArchivo = time() . '_' . uniqid() . '.' . $extension;
        $rutaDestino = $directorio . $nombreArchivo;
        // Extensiones permitidas
        $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

        // Verifica el tipo de archivo y procede a moverlo
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

    // Si no hay errores, intenta crear el vehículo en la base de datos
    if (empty($errors)) {
        $resultado = createVehiculo($idChofer, $marca, $modelo, $anio, $color, $placa, $fotoNombre);

        if ($resultado === true) {
            $_SESSION['success'] = "Vehículo agregado correctamente. Pendiente de aprobación.";
            header("Location: listar_vehiculo.php");
            exit();
        } else {
            // Si la función devuelve un mensaje (como placa duplicada), lo agregamos al array de errores
            $errors[] = $resultado;
        }
    }

}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Vehículo - Aventones CR</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  
</head>
<body class="bg-light">

<div class="container mt-5">
    <h3 class="text-success mb-4">Agregar nuevo vehículo</h3>

    <!-- Mensajes de error -->
    <?php if (!empty($errors)): ?>
    <div class="alert alert-warning d-flex align-items-center shadow-sm border-0 rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>
                <?php foreach ($errors as $e): ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success d-flex align-items-center shadow-sm border-0 rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        </div>
    <?php endif; ?>


    <!-- Formulario de creación de vehículo -->                    
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

        <!-- Subida de imagen -->            
        <div class="mb-3">
            <label class="form-label fw-semibold">Fotografía del vehículo</label>
            <input type="file" name="fotografia" class="form-control" accept="image/*">
            <div class="form-text text-muted">Formatos permitidos: JPG, PNG o WEBP (máx. 2 MB)</div>
        </div>
        <!-- Botones de acción -->            
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="listar_vehiculo.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-success">Guardar vehículo</button>
        </div>
    </form>
</div>
   
</body>
</html>
