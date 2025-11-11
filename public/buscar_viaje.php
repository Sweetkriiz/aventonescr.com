<?php
require_once '../config/database.php';

// Indicamos que la respuesta será en formato JSON
header('Content-Type: application/json');

// Leer los datos enviados desde fetch()
$data = json_decode(file_get_contents("php://input"), true);

// Se obtienen los valores, usando valores por defecto si no existen
$origen = trim($data['origen'] ?? '');
$destino = trim($data['destino'] ?? '');
$fecha = trim($data['fecha'] ?? '');
$pasajeros = intval($data['pasajeros'] ?? 1);

// Validar datos y si no hay datos, devolvemos error
if (empty($origen) || empty($destino) || empty($fecha)) {
    echo json_encode(["status" => "error", "message" => "Faltan datos"]);
    exit;// Se detiene la ejecución
}

// --- Consulta SQL ---
// si existen viajes que coincidan con origen, destino y fecha 
// también verifica que haya suficientes espacios disponibles
$sql = "SELECT COUNT(*) AS total
        FROM Viajes
        WHERE origen LIKE :origen
          AND destino LIKE :destino
          AND fecha = :fecha
          AND espaciosDisponibles >= :pasajeros
          AND estado = 'activo'";

// Prepara la consulta usando PDO (previene inyecciones SQL)
$stmt = $pdo->prepare($sql);
// Ejecuta la consulta con los valores recibidos
$stmt->execute([
  ':origen' => "%$origen%",
  ':destino' => "%$destino%",
  ':fecha' => $fecha,
  ':pasajeros' => $pasajeros
]);

// Obtiene el resultado como arreglo asociativo
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// --- Devuelve respuesta en formato JSON ---
// Si hay al menos un resultado, devuelve “ok”
// Si no hay coincidencias, devuelve “no_results”
if ($result && $result['total'] > 0) {
    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "no_results"]);
}
?>
