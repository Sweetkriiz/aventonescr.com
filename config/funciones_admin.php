
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



?>