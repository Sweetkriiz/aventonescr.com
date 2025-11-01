<?php
require_once __DIR__ . '/../config/start_app.php';
require_once __DIR__ . '/../config/database.php';

// --- Lógica principal ---
if (isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}

// --- Función para manejar el login ---
function loginUsuario(string $usuario, string $contrasena): bool {
    global $host, $db, $user, $pass;

    try {
        // Crear conexión PDO
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Buscar usuario por nombre de usuario
        $stmt = $pdo->prepare("SELECT idUsuario, nombreUsuario, contrasena, rol FROM Usuarios WHERE nombreUsuario = ? LIMIT 1");
        $stmt->execute([$usuario]);
        $user_data = $stmt->fetch();

        // Verificar si el usuario existe
        if (!$user_data) {
            $_SESSION["error"] = "Usuario no encontrado";
            return false;
        }

        // Verificar contraseña (usando SHA-256)
        $password_hash = hash('sha256', $contrasena);
        if ($password_hash !== $user_data['contrasena']) {
            $_SESSION["error"] = "Usuario o contraseña incorrectos";
            return false;
        }

        // Inicio de sesión exitoso
        $_SESSION["usuario"] = $user_data['nombreUsuario'];
        $_SESSION["user_id"] = $user_data['idUsuario'];
        $_SESSION["rol"] = $user_data['rol']; // guarda el rol del usuario
        unset($_SESSION["error"]);
        return true;

    } catch (PDOException $e) {
        error_log("Error de base de datos en login: " . $e->getMessage());
        $_SESSION["error"] = "Error del sistema. Intente más tarde.";
        return false;
    }
}


// Mostrar error (si existe) y limpiarlo siempre
$error = $_SESSION["error"] ?? "";
unset($_SESSION["error"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["nombreUsuario"] ?? '');
    $contrasena = trim($_POST["password"] ?? '');

    if (empty($usuario) || empty($contrasena)) {
        $_SESSION["error"] = "Por favor, complete todos los campos";
        header("Location: login.php");
        exit();
    }

    if (loginUsuario($usuario, $contrasena)) {
    // Redirigir según el rol
    switch ($_SESSION["rol"]) {
        case 'administrador':
            header("Location: dashboard_admin.php");
            break;

        case 'chofer':
            header("Location: dashboard_chofer.php");
            break;

        case 'pasajero':
            header("Location: index.php");
            break;
    }

    exit();
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
                <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
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
