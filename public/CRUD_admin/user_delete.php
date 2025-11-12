<?php
session_start();

require_once '../../config/database.php';
require_once __DIR__ . '/../../config/funciones_admin.php';
include("../includes/navbar.php");
//Verificar que el usuario es administrador
if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== 'administrador') {
    $_SESSION['error'] = 'Acceso denegado.';
    header('Location: listar_usuarios.php');
    exit();
}

// Verificar que la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Método no permitido.';
    header('Location: listar_usuarios.php');
    exit();
}
// Obtener y validar ID del usuario a eliminar
$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['error'] = 'ID inválido.';
    header('Location: listar_usuarios.php');
    exit();
}

try {
    // Verificar existencia del usuario
    $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE idUsuario = ?");
    $stmt->execute([$id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $_SESSION['error'] = 'Usuario no encontrado.';
        header('Location: listar_usuarios.php');
        exit();
    }

    // Evitar que un admin se elimine a sí mismo
    if ($_SESSION['usuario_id'] == $id) {
        $_SESSION['error'] = 'No puedes eliminar tu propia cuenta.';
        header('Location: listar_usuarios.php');
        exit();
    }

    // Eliminar usuario
    $stmtDel = $pdo->prepare("DELETE FROM Usuarios WHERE idUsuario = ?");
    $stmtDel->execute([$id]);

    $_SESSION['success'] = 'Usuario "' . htmlspecialchars($usuario['nombreUsuario'] ?? $usuario['nombre']) . '" eliminado correctamente.';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error al eliminar el usuario: ' . $e->getMessage();
}

header('Location: listar_usuarios.php');
exit();
?>