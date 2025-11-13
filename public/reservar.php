<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

/*  VALIDACIÓN DEL MÉTODO HTTP */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['status' => 'error', 'mensaje' => 'Método no permitido']);
  exit();
}

/* VERIFICAR SESIÓN ACTIVA (usuario logueado) */
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'mensaje' => 'Debes iniciar sesión.']);
  exit();
}


/* CAPTURA Y VALIDACIÓN DE DATOS */
$idViaje = $_POST['idViaje'] ?? null; // ID del viaje a reservar
$idPasajero = $_SESSION['user_id']; // ID del pasajero (desde la sesión)

if (!$idViaje) {
  echo json_encode(['status' => 'error', 'mensaje' => 'Viaje no válido.']);
  exit();
}

try {
  // Evitar doble reserva (solo si no está cancelada)
  $check = $pdo->prepare("
  SELECT 1 
  FROM Reservas 
  WHERE idViaje = ? 
    AND idPasajero = ? 
    AND estadoReserva NOT IN ('cancelada_por_pasajero', 'cancelada_por_chofer')
");
  $check->execute([$idViaje, $idPasajero]);

  if ($check->fetch()) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Ya reservaste este viaje.']);
    exit();
  }


  // Inicia una transacción para garantizar consistencia
  $pdo->beginTransaction();

  // Inserta la nueva reserva
  $pdo->prepare("INSERT INTO Reservas (idViaje, idPasajero) VALUES (?, ?)")->execute([$idViaje, $idPasajero]);

  // Reduce la cantidad de espacios disponibles del viaje
  $pdo->prepare("UPDATE Viajes SET espaciosDisponibles = espaciosDisponibles - 1 WHERE idViaje = ?")->execute([$idViaje]);

  // Si todo salió bien, confirma la transacción
  $pdo->commit();

  echo json_encode(['status' => 'ok', 'mensaje' => 'Reserva registrada correctamente.']);
} catch (Exception $e) {
  //  Si algo falla, se revierte la transacción
  if ($pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(['status' => 'error', 'mensaje' => 'Error al registrar la reserva.']);
}
