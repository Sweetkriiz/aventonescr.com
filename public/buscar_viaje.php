<?php
require_once '../config/database.php';

// Indicamos que la respuesta será JSON (para fetch())
header('Content-Type: application/json');

// --- Leer datos enviados desde index.php (fetch) ---
$data = json_decode(file_get_contents("php://input"), true);

// --- Limpieza y validación de los datos recibidos ---
$origen   = trim($data['origen'] ?? '');
$destino  = trim($data['destino'] ?? '');
$fecha    = trim($data['fecha'] ?? '');
$pasajeros = intval($data['pasajeros'] ?? 1);

// Validar campos obligatorios
if ($origen === '' || $destino === '' || $fecha === '') {
    echo json_encode([
        "status"  => "error",
        "message" => "Faltan datos para realizar la búsqueda."
    ]);
    exit;
}

// Validar formato de fecha (evita consultas con basura)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    echo json_encode([
        "status"  => "error",
        "message" => "Formato de fecha inválido."
    ]);
    exit;
}

//  Validar que la fecha no sea anterior a hoy
if (strtotime($fecha) < strtotime('today')) {
    echo json_encode([
        "status" => "error",
        "message" => "La fecha seleccionada no puede ser anterior a hoy."
    ]);
    exit;
}
// Validar que la cantidad de pasajeros sea positiva
if ($pasajeros < 1) {
    echo json_encode([
        "status"  => "error",
        "message" => "El número de pasajeros debe ser al menos 1."
    ]);
    exit;
}

try {
    // --- Consulta SQL mejorada ---
    // Busca viajes que coincidan con origen, destino y fecha exacta
    // y con suficientes espacios disponibles, solo si están activos.
    $sql = "SELECT COUNT(*) AS total
            FROM Viajes
            WHERE origen LIKE :origen
              AND destino LIKE :destino
              AND fecha = :fecha
              AND espaciosDisponibles >= :pasajeros
              AND estado = 'activo'";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':origen'     => "%$origen%",
        ':destino'    => "%$destino%",
        ':fecha'      => $fecha,
        ':pasajeros'  => $pasajeros
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // --- Respuesta JSON según los resultados ---
    if ($result && $result['total'] > 0) {
        // Hay resultados → se redirige en index.js
        echo json_encode(["status" => "ok"]);
    } else {
        // No hay resultados, pero seguimos el flujo normal
        echo json_encode(["status" => "no_results"]);
    }
} catch (PDOException $e) {
    // Manejo de error interno (evita exponer detalles al usuario)
    error_log("Error en buscar_viaje.php: " . $e->getMessage());
    echo json_encode([
        "status"  => "error",
        "message" => "Error al realizar la búsqueda en la base de datos."
    ]);
}
