<?php
session_start();

require_once __DIR__ . '/../config/database.php';

include('includes/navbar.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aventones CR</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- CSS personalizado -->
  <link rel="stylesheet" href="/css/index.css?v=5">

</head>

<body class="bg-light">

  <!-- Sección principal con imagen fija -->
  <section class="position-relative">
    <img src="images/Banner1.jpg" alt="Aventones" class="banner w-100">

    <!-- FRASE EN LA ESQUINA DERECHA -->
    <div class="position-absolute text-end" style="top: 120px; right: 40px; z-index: 20;">
      <h2 class="fw-bold text-white text-shadow display-5 mb-2">
        ¿VAMOS EN LA MISMA DIRECCIÓN?
      </h2>
      <p class="text-white fs-5 text-shadow mb-0">
        Comparte tu viaje y únete a la comunidad Aventones CR
      </p>
    </div>

    <!-- FORMULARIO flotante sobre la imagen -->
    <div class="position-absolute start-50 translate-middle-x bottom-0 mb-5 w-75 form-glass">
      <form id="buscarViajeForm" class="row g-3 justify-content-center">
        <div class="col-md-3">
          <label for="origen" class="form-label">Origen</label>
          <input type="text" class="form-control" id="origen" placeholder="Ej. San José" required>
        </div>
        <div class="col-md-3">
          <label for="destino" class="form-label">Destino</label>
          <input type="text" class="form-control" id="destino" placeholder="Ej. Cartago" required>
        </div>
        <div class="col-md-2">
          <label for="fecha" class="form-label">Fecha</label>
          <input type="date" class="form-control" id="fecha" required>
        </div>
        <div class="col-md-2">
          <label for="pasajeros" class="form-label">Pasajeros</label>
          <input type="number" class="form-control" id="pasajeros" min="1" required>
        </div>
        <div class="col-12 text-center">
          <button type="submit" class="btn btn-success mt-3 px-4">Buscar viaje</button>
        </div>
      </form>
    </div>
  </section>


  <?php
  if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
            SELECT marca, modelo 
            FROM vehiculos 
            WHERE idChofer = ? AND estado = 'rechazado' AND leido = 0
            LIMIT 1
        ");
    $stmt->execute([$_SESSION['user_id']]);
    $v = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($v) {
      echo "
            <div class='alert alert-warning d-flex align-items-center shadow-sm border-0 rounded-3 mt-3 mx-auto' role='alert' style='max-width: 700px;'>
                <i class='bi bi-exclamation-triangle-fill me-2 fs-5'></i>
                <div class='flex-grow-1 text-start'>
                    Tu vehículo <strong>{$v['marca']} {$v['modelo']}</strong> fue <b>rechazado</b>.
                    <a href='dashboard_pasajero.php' class='alert-link'>Ver detalles</a>.
                </div>
            </div>
            ";
    }
  }
  ?>


  <?php
  // --- Mostrar los últimos viajes publicados ---

  // Recupera los tres viajes más recientes activos desde la base de datos
  $ultimosViajes = $pdo->query("
      SELECT nombreViaje, origen, destino, fecha, tarifa 
      FROM Viajes 
      WHERE estado='activo' 
      ORDER BY fecha DESC 
      LIMIT 3
  ")->fetchAll(PDO::FETCH_ASSOC);

  // Si hay resultados, los muestra en tarjetas (cards)
  if ($ultimosViajes):
  ?>
    <section class="container my-5">
      <h3 class="fw-bold text-dark text-center mb-4"> Últimos viajes publicados</h3>
      <div class="row justify-content-center">
        <?php foreach ($ultimosViajes as $v): ?>
          <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-3">
              <div class="card-body text-center">
                <h5 class="fw-bold text-success"><?= htmlspecialchars($v['nombreViaje']) ?></h5>
                <p class="mb-0"><?= htmlspecialchars($v['origen']) ?> → <?= htmlspecialchars($v['destino']) ?></p>
                <small><?= htmlspecialchars($v['fecha']) ?></small>
                <p class="fw-bold mt-2">₡<?= number_format($v['tarifa'], 2) ?></p>
                <a href="resultados.php?origen=<?= urlencode($v['origen']) ?>&destino=<?= urlencode($v['destino']) ?>&fecha=<?= urlencode($v['fecha']) ?>&pasajeros=1"
                  class="btn btn-outline-success btn-sm mt-2 w-100">
                  Ver detalles
                </a>

              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- SECCIÓN 3 PASOS -->
  <section class="container text-center my-5">
    <h2 class="fw-bold mb-4">Reserva tu cupo en 3 pasos</h2>
    <div class="row gy-4">
      <div class="col-md-4">
        <img src="images/paso1.svg" width="100" alt="">
        <h4>Busca tu ruta</h4>
        <p>Encuentra quién te puede llevar. Si no hay viajes disponibles, publica tu búsqueda y aumenta tus posibilidades.</p>
      </div>
      <div class="col-md-4">
        <img src="images/paso2.svg" width="100" alt="">
        <h4>Elige tu viaje</h4>
        <p>Revisa las opciones disponibles y escoge según horarios, contribución de gastos y calificación.</p>
      </div>
      <div class="col-md-4">
        <img src="images/paso3.svg" width="100" alt="">
        <h4>Reserva tu cupo</h4>
        <p>Paga en línea, coordina tu recogida y viaja con total comodidad.</p>
      </div>
    </div>
  </section>

  <!-- Script que maneja la búsqueda de viajes -->
  <script>
    // Maneja el envío del formulario de búsqueda
    document.getElementById("buscarViajeForm").addEventListener("submit", function(e) {
      e.preventDefault(); // evita que recargue la página

      // Captura los valores ingresados
      const origen = document.getElementById("origen");
      const destino = document.getElementById("destino");
      const fecha = document.getElementById("fecha");
      const pasajeros = document.getElementById("pasajeros");

      fetch("buscar_viaje.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            origen: origen.value.trim(),
            destino: destino.value.trim(),
            fecha: fecha.value,
            pasajeros: pasajeros.value
          })
        })
        .then(res => res.json())
        .then(data => {

          // Independientemente del resultado, redirige a la página de resultados
          if (data.status === "ok" || data.status === "no_results") {
            // Redirige siempre a resultados.php (maneja ambos casos)
            window.location.href =
              "resultados.php?origen=" + encodeURIComponent(origen.value) +
              "&destino=" + encodeURIComponent(destino.value) +
              "&fecha=" + encodeURIComponent(fecha.value) +
              "&pasajeros=" + encodeURIComponent(pasajeros.value);
          }
        })
        .catch(() => alert("Ocurrió un error al buscar viajes."));
    });
  </script>

  <script>
    // Bloquea la selección de fechas anteriores al día actual
    document.addEventListener("DOMContentLoaded", () => {
      const inputFecha = document.getElementById("fecha");
      const hoy = new Date();
      const yyyy = hoy.getFullYear();
      const mm = String(hoy.getMonth() + 1).padStart(2, "0");
      const dd = String(hoy.getDate()).padStart(2, "0");
      const fechaMinima = `${yyyy}-${mm}-${dd}`;
      inputFecha.setAttribute("min", fechaMinima);
    });
  </script>

  <!-- Footer -->
  <footer class="text-center py-3 bg-dark text-white">
    © 2025 Aventones CR
  </footer>

</body>

</html>