<?php
session_start();
require_once '../../config/funciones_ride.php';
require_once '../../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status'=>'error','mensaje'=>'Debes iniciar sesión']);
  exit;
}

$idChofer = $_SESSION['user_id'];
$idViaje = $_POST['idViaje'] ?? null;

if (!$idViaje) {
  echo json_encode(['status'=>'error','mensaje'=>'Falta ID del viaje']);
  exit;
}

try {
  $stmt = $pdo->prepare("SELECT idChofer, estado FROM Viajes WHERE idViaje=?");
  $stmt->execute([$idViaje]);
  $viaje = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$viaje) {
    echo json_encode(['status'=>'error','mensaje'=>'Viaje no encontrado']);
    exit;
  }
  if ((int)$viaje['idChofer'] !== (int)$idChofer) {
    echo json_encode(['status'=>'error','mensaje'=>'No tienes permiso para cancelar este viaje']);
    exit;
  }

  $pdo->beginTransaction();

  // Marcar viaje como cancelado
  $pdo->prepare("UPDATE Viajes SET estado='cancelado', espaciosDisponibles=0 WHERE idViaje=?")
      ->execute([$idViaje]);

  // Marcar reservas como canceladas por chofer
  $pdo->prepare("
      UPDATE Reservas 
      SET estadoReserva='cancelada_por_chofer' 
      WHERE idViaje=? AND estadoReserva IN ('pendiente','aceptada')
  ")->execute([$idViaje]);

  $pdo->commit();
  echo json_encode(['status'=>'ok','mensaje'=>'Viaje cancelado correctamente.']);
} catch (Exception $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(['status'=>'error','mensaje'=>'Error al cancelar el viaje.']);
}
