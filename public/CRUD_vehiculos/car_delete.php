<?php

session_start();
require_once '../../config/funciones_carro.php';

// Obtiene el ID del vehículo desde la URL (GET) y lo convierte a entero
$idVehiculo = intval($_GET['id'] ?? 0);

// Obtiene los datos del vehículo antes de eliminar (sirve para validar y borrar la foto)
$vehiculo = getVehiculoById($idVehiculo);

// verifica que el vehículo exista y que pertenezca al chofer logueado
if (!$vehiculo) {
    $_SESSION['error'] = "Vehículo no encontrado.";
    header('Location: listar_vehiculo.php');
    exit();
}

if ($vehiculo['idChofer'] !== ($_SESSION['user_id'] ?? null)) {
    die('<div class="alert alert-danger text-center mt-5">
            No tenés permiso para eliminar este vehículo.
         </div>');
}

// Si existe el vehículo y la función de eliminación se ejecuta correctamente
if (deleteVehiculo($idVehiculo)) {

    // Define la ruta absoluta hacia la carpeta /uploads/
    $directorio = __DIR__ . '/../uploads/';
    $rutaArchivo = $directorio . $vehiculo['fotografia'];

    // Si el archivo de la fotografía existe, se elimina físicamente
    if (!empty($vehiculo['fotografia']) && file_exists($rutaArchivo)) {
        unlink($rutaArchivo);
    }

    // Mensaje de confirmación
    $_SESSION['success'] = "Vehículo y su fotografía fueron eliminados correctamente.";
} else {
    // Si falla la eliminación en base de datos
    $_SESSION['error'] = "Error al eliminar el vehículo.";
}

// Redirige de vuelta al listado de vehículos
header('Location: listar_vehiculo.php');
exit();
