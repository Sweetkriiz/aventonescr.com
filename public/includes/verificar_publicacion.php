<?php
session_start();
require_once '../config/database.php';

//Si no ha iniciado sesión → login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$idUsuario = $_SESSION['usuario_id'];

//Consulta para saber rol y vehículos
$sql = "SELECT rol, 
               (SELECT COUNT(*) 
                FROM Vehiculos 
                WHERE idChofer = ? AND estado = 'aprobado') AS vehiculos_aprobados,
               (SELECT COUNT(*) 
                FROM Vehiculos 
                WHERE idChofer = ? AND estado = 'pendiente') AS vehiculos_pendientes
        FROM Usuarios WHERE idUsuario = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idUsuario, $idUsuario, $idUsuario]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

//Si sigue siendo pasajero → lo mandamos a registrar vehículo
if ($user['rol'] === 'pasajero') {
    header("Location: registrar_vehiculo.php");
    exit;
}

//Si ya es chofer pero su vehículo está pendiente
if ($user['vehiculos_aprobados'] == 0 && $user['vehiculos_pendientes'] > 0) {
    echo "<script>alert('Tu vehículo está pendiente de aprobación.'); window.location.href='index.php';</script>";
    exit;
}

//Si ya tiene un vehículo aprobado → puede publicar viaje
if ($user['vehiculos_aprobados'] > 0) {
    header("Location: publicar_viaje.php");
    exit;
}

// Si no tiene ningún vehículo → registrar
echo "<script>alert('Primero debes registrar un vehículo para poder publicar viajes.'); window.location.href='registrar_vehiculo.php';</script>";
exit;
?>
