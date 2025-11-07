<?php
require_once '../config/database.php';
header('Content-Type: application/json');

// Leer los datos enviados desde fetch()
$data = json_decode(file_get_contents("php://input"), true);

$origen = trim($data['origen'] ?? '');
$destino = trim($data['destino'] ?? '');
$fecha = trim($data['fecha'] ?? '');
$pasajeros = intval($data['pasajeros'] ?? 1);

// Validar datos y si no hay datos, devolvemos error
if (empty($origen) || empty($destino) || empty($fecha)) {
    echo json_encode(["status" => "error", "message" => "Faltan datos"]);
    exit;
}

// Consulta a la base de datos
$sql = "SELECT COUNT(*) AS total
        FROM Viajes
        WHERE origen LIKE :origen
          AND destino LIKE :destino
          AND fecha = :fecha
          AND espaciosDisponibles >= :pasajeros
          AND estado = 'activo'";

$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':origen' => "%$origen%",
  ':destino' => "%$destino%",
  ':fecha' => $fecha,
  ':pasajeros' => $pasajeros
]);

$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Devolver resultado en JSON
if ($result && $result['total'] > 0) {
    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "no_results"]);
}
?>
