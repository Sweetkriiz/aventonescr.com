<?php
require_once __DIR__ . '/database.php'; 

// Obtener todos los vehículos de un chofer
function getVehiculosByChofer($idChofer) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM vehiculos WHERE idChofer = ? AND estado = 'aprobado' ORDER BY idVehiculo DESC");
    $stmt->execute([$idChofer]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Obtener vehículo por ID
function getVehiculoById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM vehiculos WHERE idVehiculo = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Crear vehículo nuevo (queda pendiente de aprobación)
function createVehiculo($idChofer, $marca, $modelo, $anio, $color, $placa, $fotografia = null) {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO vehiculos (idChofer, marca, modelo, anio, color, placa, fotografia, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente')
    ");
    return $stmt->execute([$idChofer, $marca, $modelo, $anio, $color, $placa, $fotografia]);
}


// Actualizar vehículo y volver a estado pendiente
function actualizarVehiculo($id, $marca, $modelo, $anio, $color, $placa) {
   global $pdo;
    $stmt = $pdo->prepare("
        UPDATE vehiculos
        SET marca = ?, modelo = ?, anio = ?, color = ?, placa = ?, estado = 'pendiente'
        WHERE idVehiculo = ?
    ");
    return $stmt->execute([$marca, $modelo, $anio, $color, $placa, $id]);
}


// Eliminar vehículo
function deleteVehiculo($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM vehiculos WHERE idVehiculo = ?");
    return $stmt->execute([$id]);
}

// Obtener vehículos pendientes de aprobación
function getVehiculosPendientes($idChofer) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM vehiculos WHERE idChofer = ? AND estado = 'pendiente' ORDER BY idVehiculo DESC");
    $stmt->execute([$idChofer]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Validar datos del formulario
function validateVehiculo($marca, $modelo, $anio, $color, $placa) {
    $errors = [];

    if (empty(trim($marca)) || strlen($marca) > 50) {
        $errors[] = 'La marca es obligatoria y no debe exceder 50 caracteres.';
    }

    if (empty(trim($modelo)) || strlen($modelo) > 50) {
        $errors[] = 'El modelo es obligatorio y no debe exceder 50 caracteres.';
    }

    if (!is_numeric($anio) || $anio < 1990 || $anio > date('Y') + 1) {
        $errors[] = 'Ingrese un año válido.';
    }

    if (empty(trim($color)) || strlen($color) > 30) {
        $errors[] = 'El color es obligatorio.';
    }

    if (empty(trim($placa))) {
        $errors[] = 'La placa es obligatoria.';
    }

    return $errors;
}

// Obtener vehículos aprobados de un chofer
function getVehiculosAprobados($idChofer) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT idVehiculo, marca, modelo, placa FROM vehiculos WHERE idChofer = ? AND estado = 'aprobado'");
    $stmt->execute([$idChofer]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
