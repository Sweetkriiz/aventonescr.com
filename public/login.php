<?php
session_start();
require_once '../config/database.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = trim($_POST["correo"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT * FROM Usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        
        // Aquí usarías password_verify si guardaste hash
        if ($password === $usuario["contrasena"]) { 
            $_SESSION["usuario_id"] = $usuario["idUsuario"];
            $_SESSION["rol"] = $usuario["rol"];
            $_SESSION["nombre"] = $usuario["nombre"];

            if ($usuario["rol"] === "administrador") {
                header("Location: views/admin/panel.php");
            } elseif ($usuario["rol"] === "chofer") {
                header("Location: views/chofer/mis_viajes.php");
            } else {
                header("Location: views/pasajero/mis_rides.php");
            }
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "No existe una cuenta con ese correo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - Aventones</title>
    <link rel="stylesheet" href="public/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="image-side">
            <img src="public/images/login.jpg" alt="imagen de login">
        </div>
        <div class="form-side">
            <h2>Iniciar sesión</h2>

            <?php if (!empty($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" placeholder="email@example.com" required>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>

                <button type="submit">Entrar</button>
            </form>

            <p>¿No tienes una cuenta? <a href="register.php">Regístrate</a></p>
        </div>
    </div>
</body>
</html>