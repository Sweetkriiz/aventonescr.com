<?php
require_once __DIR__ . '/database.php'; 

define('UPLOADS_PATH', '/aventones/uploads');


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

    // Verificar si ya existe una placa igual
    $sqlCheck = "SELECT COUNT(*) FROM vehiculos WHERE placa = :placa";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([':placa' => $placa]);
    $existe = $stmtCheck->fetchColumn();

    if ($existe > 0) {
        // Si la placa ya existe, devolvemos un mensaje de error en lugar de hacer el INSERT
        return "Esa placa ya está registrada. Verifica los datos.";
    }

    //  Insertar el vehículo nuevo si la placa no existe
    $stmt = $pdo->prepare("
        INSERT INTO vehiculos (idChofer, marca, modelo, anio, color, placa, fotografia, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente')
    ");

    // Si el insert se ejecuta correctamente, devolvemos true
    if ($stmt->execute([$idChofer, $marca, $modelo, $anio, $color, $placa, $fotografia])) {
        return true;
    } else {
        return "Error al registrar el vehículo.";
    }
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

//  Esto es cuando el pasajero quiere ser chofer
function createVehiculoPasajero($idUsuario, $marca, $modelo, $placa, $color, $foto) {
    global $pdo;
    try {
        // Insertar el vehículo en estado pendiente
        $sql = "INSERT INTO Vehiculos (idChofer, marca, modelo, placa, color, fotografia, estado)
                VALUES (:idChofer, :marca, :modelo, :placa, :color, :foto, 'pendiente')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':idChofer' => $idUsuario,
            ':marca' => $marca,
            ':modelo' => $modelo,
            ':placa' => $placa,
            ':color' => $color,
            ':foto' => $foto
        ]);

        // Obtener rol actual del usuario
        $rolStmt = $pdo->prepare("SELECT rol FROM Usuarios WHERE idUsuario = :id");
        $rolStmt->execute([':id' => $idUsuario]);
        $rolActual = strtolower($rolStmt->fetchColumn());

        // Solo cambiar rol si NO es administrador
        if ($rolActual !== 'administrador') {
            $update = $pdo->prepare("UPDATE Usuarios SET rol = 'pendiente_chofer' WHERE idUsuario = :id");
            $update->execute([':id' => $idUsuario]);
        }

        return true;
    } catch (PDOException $e) {
        error_log("Error al registrar vehículo pasajero: " . $e->getMessage());
        return false;
    }
}


?>


