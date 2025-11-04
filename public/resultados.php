<?php
session_start();
require_once '../config/database.php';
include('includes/navbar.php');

// --- Obtener los datos enviados desde index.php ---
$origen = $_GET['origen'] ?? '';
$destino = $_GET['destino'] ?? '';
$fecha = $_GET['fecha'] ?? '';
$pasajeros = $_GET['pasajeros'] ?? 1;

// --- Consulta dinámica a la base de datos ---
$sql = "SELECT v.*, u.nombre AS chofer_nombre, ve.modelo, ve.marca, ve.color
        FROM Viajes v
        INNER JOIN Usuarios u ON v.idChofer = u.idUsuario
        INNER JOIN Vehiculos ve ON v.idVehiculo = ve.idVehiculo
        WHERE v.lugarSalida LIKE :origen 
          AND v.destino LIKE :destino
          AND v.espaciosDisponibles >= :pasajeros";

$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':origen' => "%$origen%",
  ':destino' => "%$destino%",
  ':pasajeros' => $pasajeros
]);

$viajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultados de búsqueda - Aventones CR</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/index.css">
</head>

<body style="font-family: 'Poppins', sans-serif; background-color: #f9f9f9;">

  <div class="container my-5">
    <h2 class="text-center mb-4 fw-bold" style="color:#285936;">Resultados de tu búsqueda</h2>

    <?php if (count($viajes) > 0): ?>
      <div class="row g-4">
        <?php foreach ($viajes as $viaje): ?>
          <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
              <div class="card-body">
                <h5 class="card-title text-success fw-bold"><?= htmlspecialchars($viaje['nombreViaje']) ?></h5>
                <p class="mb-1"><strong>Chofer:</strong> <?= htmlspecialchars($viaje['chofer_nombre']) ?></p>
                <p class="mb-1"><strong>Vehículo:</strong> <?= htmlspecialchars($viaje['marca']) . " " . htmlspecialchars($viaje['modelo']) ?> (<?= htmlspecialchars($viaje['color']) ?>)</p>
                <p class="mb-1"><strong>Salida:</strong> <?= htmlspecialchars($viaje['lugarSalida']) ?> - <?= htmlspecialchars($viaje['horaSalida']) ?></p>
                <p class="mb-1"><strong>Destino:</strong> <?= htmlspecialchars($viaje['destino']) ?> - <?= htmlspecialchars($viaje['horaLlegada']) ?></p>
                <p class="mb-1"><strong>Tarifa:</strong> ₡<?= number_format($viaje['tarifa'], 2) ?></p>
                <p><strong>Espacios disponibles:</strong> <?= htmlspecialchars($viaje['espaciosDisponibles']) ?></p>

                <?php if (isset($_SESSION['user_id'])): ?>
                  <button type="button" class="btn btn-success w-100 btn-reservar"
                          data-id="<?= $viaje['idViaje'] ?>"
                          data-nombre="<?= htmlspecialchars($viaje['nombreViaje']) ?>"
                          data-chofer="<?= htmlspecialchars($viaje['chofer_nombre']) ?>"
                          data-salida="<?= htmlspecialchars($viaje['lugarSalida']) ?>"
                          data-hsalida="<?= htmlspecialchars($viaje['horaSalida']) ?>"
                          data-destino="<?= htmlspecialchars($viaje['destino']) ?>"
                          data-hllegada="<?= htmlspecialchars($viaje['horaLlegada']) ?>"
                          data-tarifa="<?= htmlspecialchars($viaje['tarifa']) ?>"
                          data-espacios="<?= htmlspecialchars($viaje['espaciosDisponibles']) ?>">
                    Reservar cupo
                  </button>
                <?php else: ?>
                  <a href="login.php" class="btn btn-outline-success w-100">Inicia sesión para reservar</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-warning text-center mt-5 shadow-sm">
        <strong>No hay viajes disponibles</strong> que coincidan con tu búsqueda.
      </div>
    <?php endif; ?>

    <div class="text-center mt-4">
      <a href="index.php" class="btn btn-secondary px-4">Volver al inicio</a>
    </div>
  </div>

  <!-- Modal de confirmación -->
  <div class="modal fade" id="modalReserva" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title fw-bold">Confirmar reserva</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p><strong>Viaje:</strong> <span id="res-nombre"></span></p>
          <p><strong>Chofer:</strong> <span id="res-chofer"></span></p>
          <p><strong>Salida:</strong> <span id="res-salida"></span> - <span id="res-hsalida"></span></p>
          <p><strong>Destino:</strong> <span id="res-destino"></span> - <span id="res-hllegada"></span></p>
          <p><strong>Tarifa:</strong> ₡<span id="res-tarifa"></span></p>
          <p><strong>Espacios disponibles:</strong> <span id="res-espacios"></span></p>
          <div id="mensaje-reserva" class="alert d-none mt-3"></div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-success" id="btn-confirmar">Confirmar</button>
        </div>
      </div>
    </div>
  </div>

  <footer class="text-center py-3 bg-dark text-white mt-5">
    © 2025 Aventones CR
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  const modalEl = document.getElementById("modalReserva");
  const modal = new bootstrap.Modal(modalEl);
  let idViajeActual = null;

  document.querySelectorAll(".btn-reservar").forEach(btn => {
    btn.addEventListener("click", e => {
      e.preventDefault(); // Evita abrir otra página
      idViajeActual = btn.dataset.id;
      document.getElementById("res-nombre").textContent = btn.dataset.nombre;
      document.getElementById("res-chofer").textContent = btn.dataset.chofer;
      document.getElementById("res-salida").textContent = btn.dataset.salida;
      document.getElementById("res-hsalida").textContent = btn.dataset.hsalida;
      document.getElementById("res-destino").textContent = btn.dataset.destino;
      document.getElementById("res-hllegada").textContent = btn.dataset.hllegada;
      document.getElementById("res-tarifa").textContent = btn.dataset.tarifa;
      document.getElementById("res-espacios").textContent = btn.dataset.espacios;
      const msg = document.getElementById("mensaje-reserva");
      msg.className = "alert d-none mt-3";
      document.getElementById("btn-confirmar").disabled = false;
      modal.show();
    });
  });

  document.getElementById("btn-confirmar").addEventListener("click", () => {
    fetch("reservar.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "idViaje=" + encodeURIComponent(idViajeActual)
    })
    .then(r => r.json())
    .then(data => {
      const msg = document.getElementById("mensaje-reserva");
      msg.classList.remove("d-none", "alert-success", "alert-danger");
      msg.classList.add(data.status === "ok" ? "alert-success" : "alert-danger");
      msg.textContent = data.mensaje;

      if (data.status === "ok") {
        document.getElementById("btn-confirmar").disabled = true;
        setTimeout(() => {
          modal.hide();
          location.reload(); // recarga los cupos actualizados
        }, 1800);
      }
    })
    .catch(() => {
      const msg = document.getElementById("mensaje-reserva");
      msg.classList.remove("d-none");
      msg.classList.add("alert-danger");
      msg.textContent = "Error al conectar con el servidor.";
    });
  });
  </script>
</body>
</html>
