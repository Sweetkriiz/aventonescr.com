<?php
require_once '../config/database.php';
header('Content-Type: application/json');

// Leer los datos enviados desde fetch()
$data = json_decode(file_get_contents("php://input"), true);

$origen = trim($data['origen'] ?? '');
$destino = trim($data['destino'] ?? '');
$fecha = trim($data['fecha'] ?? '');
$pasajeros = intval($data['pasajeros'] ?? 1);

// Si no hay datos, devolvemos error
if (empty($origen) || empty($destino)) {
    echo json_encode(["status" => "error", "message" => "Faltan datos"]);
    exit;
}

// Consulta a la base de datos
$sql = "SELECT COUNT(*) AS total
        FROM Viajes
        WHERE lugarSalida LIKE :origen 
          AND destino LIKE :destino
          AND espaciosDisponibles >= :pasajeros";

$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':origen' => "%$origen%",
  ':destino' => "%$destino%",
  ':pasajeros' => $pasajeros
]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Si hay viajes, devolvemos "ok"
if ($result && $result['total'] > 0) {
    echo json_encode(["status" => "ok"]);
} else {
    // Si no hay viajes, devolvemos "no_results"
    echo json_encode(["status" => "no_results"]);
}
?>
