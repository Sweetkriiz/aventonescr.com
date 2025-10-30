<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreUsuario = $_POST['nombreUsuario'];
    $contrasena = $_POST['contrasena'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];   
    $cedula = $_POST['cedula'];
    $fechaNacimiento = $_POST['fechaNacimiento'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];

    // ⚠️ En producción, usar password_hash()
    $sql = "INSERT INTO Usuarios (nombreUsuario, contrasena, nombre, apellidos, cedula, fechaNacimiento, correo, telefono)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombreUsuario, $contrasena, $nombre, $apellidos, $cedula, $fechaNacimiento, $correo, $telefono]);

    echo "<p>Usuario registrado correctamente. <a href='views/login.php'>Iniciar sesión</a></p>";
}
?>

<form method="POST">
    <input name="nombreUsuario" placeholder="Usuario" required>
    <input name="contrasena" type="password" placeholder="Contraseña" required>
    <input name="nombre" placeholder="Nombre" required>
    <input name="apellidos" placeholder="Apellidos" required>
    <input name="cedula" placeholder="Cédula" required>
    <input type="date" name="fechaNacimiento" required>
    <input type="email" name="correo" placeholder="Correo" required>
    <input name="telefono" placeholder="Teléfono" required>
    <button type="submit">Registrar</button>
</form>
