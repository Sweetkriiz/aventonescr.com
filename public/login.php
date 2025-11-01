<?php
session_start();
require_once '../config/database.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombreUsuario = trim($_POST["nombreUsuario"]);
    $password = trim($_POST["password"]);

    //Buscar usuario por nombre de usuario
    $sql = "SELECT * FROM Usuarios WHERE nombreUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombreUsuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        
        //Reemplaza por password_verify() si usas hashes
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
        $error = "No existe una cuenta con ese nombre de usuario.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - Aventones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container d-flex">
        <div class="col-md-6 image-side">
            <img src="images/login.png" alt="Imagen de login">
        </div>

        <div class="col-md-6 form-side">
            <img src="images/logo.jpeg" alt="Logo Aventones" class="logo-login">
            <h3 class="text-center mb-4">Iniciar sesión</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nombreUsuario" class="form-label">Nombre de usuario</label>
                    <input type="text" id="nombreUsuario" name="nombreUsuario" class="form-control" placeholder="Ingresa tu nombre de usuario" required>

                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>

            <p class="text-center mt-3 mb-0">
                ¿No tienes una cuenta? 
                <a href="registrarse.php" class="text-decoration-none">Regístrate</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
