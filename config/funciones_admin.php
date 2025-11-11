
<?php 

require_once __DIR__ . '/database.php'; 


// Función para obtener todos los usuarios
function obtenerUsuarios() {
  global $pdo;
  $stmt = $pdo->query("SELECT idUsuario,nombreUsuario, nombre, apellidos, cedula, fechaNacimiento, correo, telefono, rol, fechaRegistro FROM Usuarios ORDER BY idUsuario DESC");
  return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

//Función para verificar si el usuario está autenticado
function checkAuth(){
    if(session_status() === PHP_SESSION_NONE){
        session_start();
    }if(!isset($_SESSION["usuario"])){
        header("Location: login.php");
        exit();
    }
}

//Función para crear un nuevo usuario

function crearUsuario($nombre, $apellidos, $cedula, $fechaNacimiento, $nombreUsuario, $correo, $password, $telefono, $rol = 'pasajero'){
    require 'database.php';

    // Hashear la contraseña antes de guardarla
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO Usuarios (nombre, apellidos, cedula, fechaNacimiento, nombreUsuario, correo, contrasena, telefono, rol) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombre, $apellidos, $cedula, $fechaNacimiento, $nombreUsuario, $correo, $hashedPassword, $telefono, $rol]);
    
    return $pdo->lastInsertId();
}

// Eliminar usuario por ID
function deleteUser($id) {
    try {
        $pdo = getConnection(); // o usa require_once '../config/database.php' si no tienes una función global
        $stmt = $pdo->prepare("DELETE FROM Usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        // Manejo básico de errores (útil para debug)
        error_log("Error al eliminar usuario: " . $e->getMessage());
        return false;
    }
}

// Actualizar usuario
function updateUser($id, $nombre, $apellidos, $cedula, $fechaNacimiento, $nombreUsuario, $correo, $telefono, $rol) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("
            UPDATE Usuarios 
            SET 
                nombre = ?, 
                apellidos = ?, 
                cedula = ?, 
                fechaNacimiento = ?, 
                nombreUsuario = ?, 
                correo = ?, 
                telefono = ?, 
                rol = ?
            WHERE idUsuario = ?
        ");
        return $stmt->execute([$nombre, $apellidos, $cedula, $fechaNacimiento, $nombreUsuario, $correo, $telefono, $rol, $id]);
    } catch (PDOException $e) {
        error_log("Error al actualizar usuario: " . $e->getMessage());
        return false;
    }
}

function verificarAdmin() {
    session_start();
    if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
        header('Location: login.php');
        exit();
    }
}

/**
 *  APROBACIÓN/RECHAZO DE VEHÍCULOS
 */

/**
 * Obtiene el ID del usuario propietario de un vehículo.
 */
function obtenerUsuarioPorVehiculo(PDO $pdo, int $vehiculoId): ?int {
    $stmt = $pdo->prepare("SELECT usuario_id FROM vehiculos WHERE id = ?");
    $stmt->execute([$vehiculoId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['usuario_id'] : null;
}

/**
 * Aprueba un vehículo y, si es el primero aprobado, cambia el rol del usuario a 'chofer'.
 */
function aprobarVehiculo(PDO $pdo, int $vehiculoId): void {
    // Aprobar el vehículo
    $stmt = $pdo->prepare("UPDATE vehiculos SET estado = 'aprobado', motivo_rechazo = NULL WHERE id = ?");
    $stmt->execute([$vehiculoId]);

    // Obtener el ID del usuario
    $usuarioId = obtenerUsuarioPorVehiculo($pdo, $vehiculoId);
    if (!$usuarioId) return;

    // Verificar cantidad de vehículos aprobados
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM vehiculos WHERE usuario_id = ? AND estado = 'aprobado'");
    $stmtCheck->execute([$usuarioId]);
    $cantidadAprobados = $stmtCheck->fetchColumn();

    // Si es el primero, cambiar el rol
    if ($cantidadAprobados == 1) {
        $stmtRol = $pdo->prepare("UPDATE usuarios SET rol = 'chofer' WHERE id = ?");
        $stmtRol->execute([$usuarioId]);
    }
}

/**
 * Rechaza un vehículo con un motivo específico.
 */
function rechazarVehiculo(PDO $pdo, int $vehiculoId, string $motivo): void {
    $motivo = trim($motivo);
    if (empty($motivo)) {
        $_SESSION['error'] = 'Debe indicar el motivo de rechazo.';
        header('Location: procesarSolicitudes.php');
        exit();
    }

    $stmt = $pdo->prepare("UPDATE vehiculos SET estado = 'rechazado', motivo_rechazo = ? WHERE id = ?");
    $stmt->execute([$motivo, $vehiculoId]);
}

/**
 * Procesa la acción enviada por el formulario (aprobar o rechazar).
 */
function procesarAccionVehiculo(PDO $pdo): void {
    $id = $_POST['id'] ?? null;
    $accion = $_POST['accion'] ?? null;

    if (!$id || !$accion) {
        header('Location: procesarSolicitudes.php');
        exit();
    }

    if ($accion === 'aprobar') {
        aprobarVehiculo($pdo, (int)$id);
        $_SESSION['mensaje'] = '✅ Vehículo aprobado correctamente.';
    } elseif ($accion === 'rechazar') {
        rechazarVehiculo($pdo, (int)$id, $_POST['motivo'] ?? '');
        $_SESSION['mensaje'] = '❌ Vehículo rechazado correctamente.';
    }

    header('Location: procesarSolicitudes.php');
    exit();
}


?>