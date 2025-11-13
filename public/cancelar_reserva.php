<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Sesión no válida']);
    exit;
}

$idReserva  = $_POST['idReserva'] ?? null;
$idPasajero = $_SESSION['user_id'];

if (!$idReserva) {
    echo json_encode(['status' => 'error', 'mensaje' => 'ID de reserva faltante']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Traer la reserva y su viaje asociado
    $q = $pdo->prepare("
        SELECT r.idReserva, r.idViaje, r.estadoReserva, v.estado AS estadoViaje
        FROM Reservas r
        JOIN Viajes v ON v.idViaje = r.idViaje
        WHERE r.idReserva = ? AND r.idPasajero = ?
    ");
    $q->execute([$idReserva, $idPasajero]);
    $reserva = $q->fetch(PDO::FETCH_ASSOC);

    if (!$reserva) {
        echo json_encode(['status' => 'error', 'mensaje' => 'Reserva no encontrada']);
        exit;
    }

    // Solo permitir cancelar si la reserva está pendiente o aceptada
    if (!in_array($reserva['estadoReserva'], ['pendiente', 'aceptada'])) {
        echo json_encode(['status' => 'error', 'mensaje' => 'Esta reserva ya no puede cancelarse.']);
        exit;
    }

    // Marcar la reserva como cancelada por pasajero (en lugar de borrarla)
    $pdo->prepare("
        UPDATE Reservas
        SET estadoReserva = 'cancelada_por_pasajero'
        WHERE idReserva = ?
    ")->execute([$idReserva]);

    // Devolver espacio solo si el viaje sigue activo
    if ($reserva['estadoViaje'] === 'activo') {
        $pdo->prepare("
            UPDATE Viajes
            SET espaciosDisponibles = espaciosDisponibles + 1
            WHERE idViaje = ?
        ")->execute([$reserva['idViaje']]);
    }

    $pdo->commit();
    echo json_encode(['status' => 'ok', 'mensaje' => 'Reserva cancelada correctamente.']);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['status' => 'error', 'mensaje' => 'Error al cancelar la reserva.']);
}
