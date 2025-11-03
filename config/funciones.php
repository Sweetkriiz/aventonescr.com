
<?php 
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

?>