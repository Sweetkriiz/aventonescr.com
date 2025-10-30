<?php
session_start();
require_once '../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE nombreUsuario = ?");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user['contrasena']) { 
        // ⚠️ Nota: más adelante reemplazamos esto por password_verify()
        $_SESSION['idUsuario'] = $user['idUsuario'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['nombre'] = $user['nombre'];

        switch ($user['rol']) {
            case 'administrador':
                header("Location: ../views/admin/dashboard.php");
                break;
            case 'chofer':
                header("Location: ../views/chofer/dashboard.php");
                break;
            default:
                header("Location: ../views/pasajero/dashboard.php");
        }
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login | Aventones</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <h2>Iniciar sesión</h2>

    <form method="POST">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
    </form>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
</body>
</html>

