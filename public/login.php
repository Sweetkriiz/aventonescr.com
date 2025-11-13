<?php
require_once __DIR__ . '/../config/start_app.php';
require_once __DIR__ . '/../config/database.php';

// si el usuario ya inició sesión, redirigir según su rol
if (isset($_SESSION["usuario"]) && isset($_SESSION["rol"])) {
    switch ($_SESSION["rol"]) {
        case 'administrador':
            header("Location: dashboard_admin.php");
            exit();
        case 'chofer':
        case 'pasajero':
            header("Location: index.php");
            exit();
    }
}

// procesar formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["nombreUsuario"] ?? '');
    $contrasena = trim($_POST["password"] ?? '');

    // valida que los campos no estén vacíos
    if (empty($usuario) || empty($contrasena)) {
        $_SESSION["error"] = "Por favor, complete todos los campos";
        header("Location: login.php");
        exit();
    }

    try {
        // crear conexión PDO
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // preparar y ejecutar la consulta del usuario
        $stmt = $pdo->prepare("SELECT idUsuario, nombreUsuario, contrasena, rol 
                                   FROM Usuarios 
                                   WHERE nombreUsuario = ? 
                                   LIMIT 1");
        $stmt->execute([$usuario]);
        $user_data = $stmt->fetch();

        // verifica si el usuario existe
        if ($user_data) {
            // verifica la contraseña usando SHA-256
            $password_hash = hash('sha256', $contrasena);

            if ($password_hash === $user_data['contrasena']) {
                // login exitoso
                $_SESSION["usuario"] = $user_data['nombreUsuario'];
                $_SESSION["user_id"] = $user_data['idUsuario'];
                $_SESSION["rol"] = $user_data['rol'];

                // limpiar errores previos
                unset($_SESSION["error"]);

                // redirigir según el rol
                switch ($user_data['rol']) {
                    case 'administrador':
                        header("Location: dashboard_admin.php");
                        exit();
                    case 'chofer':
                    case 'pasajero':
                        header("Location: index.php");
                        exit();
                }
            } else {
                // contraseña incorrecta
                $_SESSION["error"] = "Usuario o contraseña incorrectos";
                header("Location: login.php");
                exit();
            }
        } else {
            // usuario no encontrado
            $_SESSION["error"] = "Usuario no encontrado";
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        // manejo de errores de conexión
        error_log("Error de base de datos en login: " . $e->getMessage());
        $_SESSION["error"] = "Error del sistema. Intente más tarde.";
        header("Location: login.php");
        exit();
    }
}

// mostrar mensaje de error si existe
$error = $_SESSION["error"] ?? "";
unset($_SESSION["error"]);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - Aventones CR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="login-container d-flex">
        <div class="col-md-6 image-side">
            <img src="images/login.png" alt="Imagen de login">
        </div>

        <div class="col-md-6 form-side">
            <img src="images/logo.png" alt="Logo Aventones" class="logo-login">
            <h3 class="text-center mb-4">Iniciar sesión</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nombreUsuario" class="form-label fw-bold">Nombre de usuario</label>
                    <input type="text" id="nombreUsuario" name="nombreUsuario" class="form-control" placeholder="Ingresa tu nombre de usuario" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-bold">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold">Entrar</button>
            </form>

            <p class="text-center mt-3 mb-0">
                ¿No tienes una cuenta?
                <a href="registrarse.php" class="text-decoration-none fw-semibold">Regístrate</a>
            </p>
            <p class="text-center mt-2">
                ¿Olvidaste tu contraseña?
                <a href="index.php" class="text-decoration-none fw-semibold">Recupérala aquí</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>