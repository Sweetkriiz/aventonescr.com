<?php
session_start();
require_once '../../config/funciones_carro.php';


$idVehiculo = intval($_GET['id']);

if (deleteVehiculo($idVehiculo)) {
    $_SESSION['success'] = "Vehículo eliminado correctamente.";
} else {
    $_SESSION['error'] = "❌ Error al eliminar el vehículo.";
}

header('Location: listar_vehiculo.php');
exit();
?>
