<?php
require_once __DIR__ . '/database.php';

// Obtener todos los viajes de un chofer
function getViajesByChofer($idChofer) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Viajes WHERE idChofer = ? ORDER BY fecha DESC");
    $stmt->execute([$idChofer]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener viaje por ID
function getViajeById($idViaje) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM Viajes WHERE idViaje = ?");
    $stmt->execute([$idViaje]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Crear nuevo viaje
function createViaje($idChofer, $idVehiculo, $nombreViaje, $origen, $destino, $fecha, $horaSalida, $horaLlegada, $tarifa, $espaciosDisponibles) {
    global $pdo;

    $stmt = $pdo->prepare("
        INSERT INTO Viajes (idChofer, idVehiculo, nombreViaje, origen, destino, fecha, horaSalida, horaLlegada, tarifa, espaciosDisponibles)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([$idChofer, $idVehiculo, $nombreViaje, $origen, $destino, $fecha, $horaSalida, $horaLlegada, $tarifa, $espaciosDisponibles]);
}


// Actualizar viaje
function updateViaje($idViaje, $origen, $destino, $fecha, $horaSalida, $horaLlegada, $tarifa, $espaciosDisponibles) {
    global $pdo;
    $stmt = $pdo->prepare("
        UPDATE Viajes
        SET origen = ?, destino = ?, fecha = ?, horaSalida = ?, horaLlegada = ?, tarifa = ?, espaciosDisponibles = ?
        WHERE idViaje = ?
    ");
    return $stmt->execute([$origen, $destino, $fecha, $horaSalida, $horaLlegada, $tarifa, $espaciosDisponibles, $idViaje]);
}

// Eliminar viaje
function deleteViaje($idViaje) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM Viajes WHERE idViaje = ?");
    return $stmt->execute([$idViaje]);
}

// Validar datos
function validateViaje($origen, $destino, $fecha, $horaSalida, $horaLlegada, $tarifa, $espaciosDisponibles) {
    $errors = [];

    if (empty(trim($origen))) $errors[] = 'El campo origen es obligatorio.';
    if (empty(trim($destino))) $errors[] = 'El campo destino es obligatorio.';
    if (empty($fecha)) $errors[] = 'Debe seleccionar una fecha.';
    if (empty($horaSalida) || empty($horaLlegada)) $errors[] = 'Debe indicar las horas de salida y llegada.';
    if (!is_numeric($tarifa) || $tarifa <= 0) $errors[] = 'La tarifa debe ser un número válido.';
    if (!is_numeric($espaciosDisponibles) || $espaciosDisponibles <= 0) $errors[] = 'Debe indicar al menos un espacio disponible.';

    return $errors;
}
?>
