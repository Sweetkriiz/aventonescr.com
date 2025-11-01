<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['status' => 'error', 'mensaje' => 'Método no permitido']);
  exit();
}

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'mensaje' => 'Debes iniciar sesión.']);
  exit();
}

$idViaje = $_POST['idViaje'] ?? null;
$idPasajero = $_SESSION['user_id'];

if (!$idViaje) {
  echo json_encode(['status' => 'error', 'mensaje' => 'Viaje no válido.']);
  exit();
}

try {
  // Evitar doble reserva
  $check = $pdo->prepare("SELECT 1 FROM Reservas WHERE idViaje = ? AND idPasajero = ?");
  $check->execute([$idViaje, $idPasajero]);
  if ($check->fetch()) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Ya reservaste este viaje.']);
    exit();
  }

  $pdo->beginTransaction();
  $pdo->prepare("INSERT INTO Reservas (idViaje, idPasajero) VALUES (?, ?)")->execute([$idViaje, $idPasajero]);
  $pdo->prepare("UPDATE Viajes SET espaciosDisponibles = espaciosDisponibles - 1 WHERE idViaje = ?")->execute([$idViaje]);
  $pdo->commit();

  echo json_encode(['status' => 'ok', 'mensaje' => 'Reserva registrada correctamente.']);
} catch (Exception $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(['status' => 'error', 'mensaje' => 'Error al registrar la reserva.']);
}
