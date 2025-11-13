<?php
session_start();
require_once '../../config/funciones_ride.php';
require_once '../../config/funciones_carro.php';
include '../includes/navbar.php';

// Obtiene el ID del chofer desde la sesión
$idChofer = $_SESSION['user_id'];

// Obtiene todos los viajes publicados por el chofer actual
$viajes = getViajesByChofer($idChofer);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Mis Rides - Aventones CR</title>

  <!-- Carga Bootstrap, íconos y fuentes externas -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container py-5">

    <!-- Título y botón para crear ride -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold text-success">Mis Rides</h2>
      <a href="ride_create.php" class="btn btn-success">+ Publicar Ride</a>
    </div>

    <!-- Si hay rides publicados -->
    <?php if ($viajes): ?>
      <div class="card shadow">
        <div class="card-header bg-success text-white fw-bold">
          Listado de Viajes
        </div>
        <div class="card-body">
          <table class="table table-bordered align-middle">
            <thead class="table-success">
              <tr>
                <th>Origen</th>
                <th>Destino</th>
                <th>Fecha</th>
                <th>Hora Salida</th>
                <th>Tarifa</th>
                <th>Espacios</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>

              <!-- Recorre todos los viajes del chofer -->
              <?php foreach ($viajes as $v): ?>
                <tr>
                  <td><?= htmlspecialchars($v['origen']) ?></td>
                  <td><?= htmlspecialchars($v['destino']) ?></td>
                  <td><?= htmlspecialchars($v['fecha']) ?></td>
                  <td><?= htmlspecialchars($v['horaSalida']) ?></td>
                  <td>₡<?= number_format($v['tarifa'], 2) ?></td>
                  <td><?= htmlspecialchars($v['espaciosDisponibles']) ?></td>

                  <!-- Muestra el estado del ride con un badge de color -->
                  <td>
                    <?php
                    // match selecciona el color del badge según el estado
                    $badge = match ($v['estado']) {
                      'activo' => 'success',
                      'completado' => 'primary',
                      'cancelado' => 'danger',
                      default => 'secondary'
                    };
                    ?>
                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($v['estado']) ?></span>
                  </td>

                  <!-- BOTONES DE ACCIÓN -->
                  <td>
                    <!-- Botón Ver -->
                    <a href="ride_view.php?id=<?= $v['idViaje'] ?>" class="btn btn-outline-primary btn-sm">
                      Ver
                    </a>

                    <!-- Botón Editar -->
                    <a href="ride_edit.php?id=<?= $v['idViaje'] ?>" class="btn btn-outline-warning btn-sm">
                      Editar
                    </a>

                    <!-- botón Cancelar (solo si está activo) -->
                    <?php if ($v['estado'] === 'activo'): ?>
                      <button
                        class="btn btn-outline-danger btn-sm cancelar-viaje"
                        data-id="<?= $v['idViaje'] ?>"
                        data-bs-toggle="modal"
                        data-bs-target="#modalCancelarViaje">
                        <i class="bi bi-slash-circle"></i> Cancelar
                      </button>
                    <?php else: ?>
                      <button class="btn btn-outline-secondary btn-sm" disabled>
                        <i class="bi bi-x-circle"></i> Cancelado
                      </button>
                    <?php endif; ?>


                    <!-- Botón Eliminar -->
                    <button type="button"
                      class="btn btn-outline-danger btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#confirmarEliminar<?= $v['idViaje'] ?>">
                      Eliminar
                    </button>

                    <!-- Modal de confirmación -->
                    <div class="modal fade" id="confirmarEliminar<?= $v['idViaje'] ?>" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg">
                          <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-semibold">Confirmar eliminación</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body text-center">
                            <p class="fw-semibold mb-2">
                              ¿Seguro que querés eliminar el ride
                              <span class="text-danger"><?= htmlspecialchars($v['origen']) ?> → <?= htmlspecialchars($v['destino']) ?></span>?
                            </p>
                            <p class="text-muted small mb-0">
                              Esta acción eliminará el viaje y sus reservas asociadas permanentemente.
                            </p>
                          </div>
                          <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <a href="ride_delete.php?id=<?= $v['idViaje'] ?>" class="btn btn-danger">Sí, eliminar</a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-secondary text-center">
        No tenés rides publicados.
      </div>
    <?php endif; ?>

    <!-- Botón volver -->
    <div class="mt-4 text-center">
      <a href="../dashboard_chofer.php" class="btn btn-secondary">Volver al Panel</a>
    </div>

  </div>

  <!-- Modal confirmar cancelación -->
  <div class="modal fade" id="modalCancelarViaje" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-semibold">
            Confirmar cancelación
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body text-center">
          <p class="mb-2">
            ¿Seguro que querés <strong>cancelar este viaje</strong>?<br>
            Esto también marcará como canceladas las reservas activas.
          </p>
          <div id="cancel-alert" class="alert d-none small mb-0"></div>
        </div>

        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, volver</button>
          <button type="button" class="btn btn-danger" id="btn-confirmar-cancelacion">
            Sí, cancelar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!--  Script para cancelar viaje JS -->
  <script>
    let viajeSeleccionado = null;
    let filaSeleccionada = null;

    // 1 Cuando se abre el modal, guardamos cuál viaje se quiere cancelar
    document.querySelectorAll('.cancelar-viaje').forEach(btn => {
      btn.addEventListener('click', () => {
        viajeSeleccionado = btn.dataset.id;
        filaSeleccionada = btn.closest('tr');

        // Limpiamos el mensaje anterior (por si queda algo en el modal)
        const alertBox = document.getElementById('cancel-alert');
        alertBox.className = 'alert d-none small mb-0';
        alertBox.textContent = '';
      });
    });

    // 2 Cuando se confirma dentro del modal, recién ahí hacemos el fetch()
    document.getElementById('btn-confirmar-cancelacion').addEventListener('click', () => {
      if (!viajeSeleccionado || !filaSeleccionada) return;

      const alertBox = document.getElementById('cancel-alert');
      alertBox.className = 'alert alert-warning small';
      alertBox.innerHTML = `<i class="bi bi-hourglass-split"></i> Cancelando el viaje...`;

      const btnConfirm = document.getElementById('btn-confirmar-cancelacion');
      btnConfirm.disabled = true;

      fetch('ride_cancel.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'idViaje=' + encodeURIComponent(viajeSeleccionado)
        })
        .then(r => r.json())
        .then(data => {
          alertBox.classList.remove('alert-warning');
          alertBox.classList.add(data.status === 'ok' ? 'alert-success' : 'alert-danger');
          alertBox.innerHTML = `
        <i class="bi ${data.status === 'ok' ? 'bi-check-circle' : 'bi-exclamation-triangle'}"></i>
        ${data.mensaje}
      `;

          if (data.status === 'ok') {
            // Actualiza visualmente la fila
            const estado = filaSeleccionada.querySelector('.badge');
            estado.className = 'badge bg-danger';
            estado.textContent = 'Cancelado';

            const boton = filaSeleccionada.querySelector('.cancelar-viaje');
            boton.disabled = true;
            boton.classList.remove('btn-outline-danger');
            boton.classList.add('btn-outline-secondary');
            boton.innerHTML = '<i class="bi bi-x-circle"></i> Cancelado';

            // Cerrar el modal con un pequeño delay
            setTimeout(() => {
              const modal = bootstrap.Modal.getInstance(document.getElementById('modalCancelarViaje'));
              modal.hide();
            }, 1000);
          }
        })
        .catch(() => {
          alertBox.classList.remove('alert-warning');
          alertBox.classList.add('alert-danger');
          alertBox.innerHTML = `<i class="bi bi-exclamation-triangle"></i> Error al conectar con el servidor.`;
        })
        .finally(() => {
          btnConfirm.disabled = false;
        });
    });
  </script>


</body>

</html>