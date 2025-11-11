<?php

session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/funciones_admin.php';


// --- Consultar vehículos pendientes ---
$stmt = $pdo->query("
    SELECT 
        v.idVehiculo AS id,
        v.marca,
        v.modelo,
        v.anio,
        v.color,
        v.placa,
        v.fotografia,
        u.nombreUsuario,
        CONCAT(u.nombre, ' ', u.apellidos) AS nombreCompleto
    FROM vehiculos v
    INNER JOIN usuarios u ON v.idChofer = u.idUsuario
    WHERE v.estado = 'pendiente'
    ORDER BY v.idVehiculo DESC
");
$vehiculosPendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aventones CR | Procesar Solicitudes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  
  <style>
    <style>
  body {
    background-color: #f8f9fa;
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
  }

  h2 {
    color: #212529;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    letter-spacing: 0.5px;
  }

  .container {
    max-width: 1200px;
  }

  .card {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    transition: transform 0.2s ease, box-shadow 0.3s ease;
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  }

  .card img {
    border-radius: 1rem 1rem 0 0;
    object-fit: cover;
    height: 180px;
    width: 100%;
  }

  .card-body {
    background-color: #fff;
    padding: 1.5rem;
  }

  .card-title {
    font-weight: 600;
    color: #212529;
  }

  .card-text {
    color: #555;
    font-size: 0.95rem;
  }

  .btn-aprobar {
    background-color: #28a745;
    border: none;
    color: #fff;
    font-weight: 500;
    padding: 6px 16px;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
  }

  .btn-aprobar:hover {
    background-color: #218838;
    box-shadow: 0 3px 8px rgba(40,167,69,0.3);
  }

  .btn-rechazar {
    background-color: #dc3545;
    border: none;
    color: #fff;
    font-weight: 500;
    padding: 6px 16px;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
  }

  .btn-rechazar:hover {
    background-color: #c82333;
    box-shadow: 0 3px 8px rgba(220,53,69,0.3);
  }

  .modal-content {
    border-radius: 1rem;
    border: none;
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
  }

  .modal-header {
    border-bottom: none;
    background: linear-gradient(45deg, #dc3545, #b52a36);
  }

  .modal-title {
    font-weight: 600;
  }

  textarea.form-control {
    border-radius: 0.5rem;
    resize: none;
  }

  .alert {
    border-radius: 0.75rem;
    font-weight: 500;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }

  .alert-success {
    background-color: #d1e7dd;
    color: #0f5132;
    border: none;
  }

  .alert-danger {
    background-color: #f8d7da;
    color: #842029;
    border: none;
  }

  .alert-info {
    background-color: #cff4fc;
    color: #055160;
    border: none;
  }

  @media (max-width: 768px) {
    .card-body {
      text-align: center;
    }
    .btn-aprobar, .btn-rechazar {
      width: 100%;
      margin-bottom: 8px;
    }
  }
</style>
</head>
<body>
  <?php include('includes/navbar.php'); ?>

  <!-- Mensajes -->
  <div class="container mt-4">
    <?php if (isset($_SESSION['mensaje'])): ?>
      <div class="alert alert-success alert-dismissible fade show text-center mx-auto" style="max-width:600px;">
        <?= htmlspecialchars($_SESSION['mensaje']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show text-center mx-auto" style="max-width:600px;">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
  </div>

  <div class="container py-5">
    <h2 class="text-center fw-bold mb-4">Solicitudes de Vehículos Pendientes</h2>

    <?php if (empty($vehiculosPendientes)): ?>
      <div class="alert alert-info text-center">No hay solicitudes pendientes por procesar.</div>
    <?php else: ?>
      <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($vehiculosPendientes as $vehiculo): ?>
          <div class="col">
            <div class="card h-100">
              <?php if (!empty($vehiculo['fotografia'])): ?>
                <img src="../uploads/<?= htmlspecialchars($vehiculo['fotografia']) ?>" alt="Foto del vehículo">
              <?php else: ?>
                <img src="https://cdn-icons-png.flaticon.com/512/743/743131.png" alt="Vehículo genérico">
              <?php endif; ?>

              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($vehiculo['marca'] . ' ' . $vehiculo['modelo']) ?></h5>
                <p class="card-text mb-1"><strong>Chofer:</strong> <?= htmlspecialchars($vehiculo['nombreCompleto']) ?></p>
                <p class="card-text mb-1"><strong>Usuario:</strong> <?= htmlspecialchars($vehiculo['nombreUsuario']) ?></p>
                <p class="card-text mb-1"><strong>Placa:</strong> <?= htmlspecialchars($vehiculo['placa']) ?></p>
                <p class="card-text mb-3"><strong>Año:</strong> <?= htmlspecialchars($vehiculo['anio']) ?></p>

                <!-- Formulario de aprobación -->
                <form method="POST" class="d-inline">
                  <input type="hidden" name="id" value="<?= $vehiculo['id'] ?>">
                  <button type="submit" name="accion" value="aprobar" class="btn btn-aprobar btn-sm">
                    <i class="bi bi-check-circle"></i> Aprobar
                  </button>
                </form>

                <!-- Botón para modal de rechazo -->
                <button type="button" class="btn btn-rechazar btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalRechazo" data-id="<?= $vehiculo['id'] ?>">
                  <i class="bi bi-x-circle"></i> Rechazar
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Modal de rechazo -->
  <div class="modal fade" id="modalRechazo" tabindex="-1">
    <div class="modal-dialog">
      <form method="POST" class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title"><i class="bi bi-x-circle"></i> Rechazar solicitud</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="vehiculoId">
          <div class="mb-3">
            <label for="motivo" class="form-label">Motivo de rechazo</label>
            <textarea name="motivo" id="motivo" class="form-control" rows="4" placeholder="Escriba el motivo del rechazo..." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="accion" value="rechazar" class="btn btn-danger">Rechazar</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const modalRechazo = document.getElementById('modalRechazo');
    modalRechazo.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget;
      modalRechazo.querySelector('#vehiculoId').value = button.getAttribute('data-id');
    });
  </script>
</body>
</html>