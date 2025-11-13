<?php
require_once '../config/database.php';

// Indicamos que la respuesta será JSON
header('Content-Type: application/json');

// Leer datos desde fetch()
$data = json_decode(file_get_contents("php://input"), true);

// Limpiar inputs
$origen    = trim($data['origen'] ?? '');
$destino   = trim($data['destino'] ?? '');
$fecha     = trim($data['fecha'] ?? '');
$pasajeros = intval($data['pasajeros'] ?? 1);

// Validar campos mínimos (origen y destino)
if ($origen === '' || $destino === '') {
    echo json_encode([
        "status"  => "error",
        "message" => "Debe ingresar origen y destino."
    ]);
    exit;
}

// Validar cantidad de pasajeros
if ($pasajeros < 1) {
    echo json_encode([
        "status"  => "error",
        "message" => "El número de pasajeros debe ser al menos 1."
    ]);
    exit;
}

// Si el usuario SÍ escogió fecha → validar formato
if ($fecha !== '') {

    // Validar formato
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Formato de fecha inválido."
        ]);
        exit;
    }

    // Validar que no sea fecha pasada
    if (strtotime($fecha) < strtotime('today')) {
        echo json_encode([
            "status" => "error",
            "message" => "La fecha seleccionada no puede ser anterior a hoy."
        ]);
        exit;
    }
}

try {

    // Base del SQL
    $sql = "SELECT COUNT(*) AS total
            FROM Viajes
            WHERE LOWER(TRIM(origen)) LIKE LOWER(TRIM(:origen))
              AND LOWER(TRIM(destino)) LIKE LOWER(TRIM(:destino))
              AND espaciosDisponibles >= :pasajeros
              AND estado = 'activo'";

    $params = [
        ':origen'    => "%" . $origen . "%",
        ':destino'   => "%" . $destino . "%",
        ':pasajeros' => $pasajeros
    ];

    // Si el usuario SÍ escogió fecha → filtrar
    if ($fecha !== '') {
        $sql .= " AND fecha = :fecha";
        $params[':fecha'] = $fecha;
    }

    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Respuesta
    if ($result && $result['total'] > 0) {
        echo json_encode(["status" => "ok"]);
    } else {
        echo json_encode(["status" => "no_results"]);
    }

} catch (PDOException $e) {

    error_log("Error en buscar_viaje.php: " . $e->getMessage());

    echo json_encode([
        "status"  => "error",
        "message" => "Error al realizar la búsqueda en la base de datos."
    ]);
}