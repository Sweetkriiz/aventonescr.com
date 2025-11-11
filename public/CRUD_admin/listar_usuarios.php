<?php
session_start();
require_once '../../config/database.php';
require_once __DIR__ . '/../../config/funciones_admin.php';
include("../includes/navbar.php");

// Obtener lista de usuarios
$usuarios = obtenerUsuarios();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuarios - Aventones CR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/admin.css">
</head>

<body class="bg-light">

  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold text-success">Usuarios</h2>
      <a href="user_create.php" class="btn btn-success">+ Agregar Usuario</a>
    </div>

    <!-- Tabla de usuarios -->
    <div class="card mb-4 shadow-sm" style="height: auto; min-height: auto;">
      <div class="card-header bg-success text-white fw-bold">Lista de Usuarios</div>
        <div class="card-body p-3">
        <?php if (!empty($usuarios)): ?>
          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-success">
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Apellidos</th>
                  <th>Correo</th>
                  <th>Teléfono</th>
                  <th>Rol</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($usuarios as $u): ?>
                  <tr>
                    <td><?= htmlspecialchars($u['idUsuario']) ?></td>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= htmlspecialchars($u['apellidos']) ?></td>
                    <td><?= htmlspecialchars($u['correo']) ?></td>
                    <td><?= htmlspecialchars($u['telefono']) ?></td>
                    <td>
                      <?php
                      $rolColor = match ($u['rol']) {
                        'administrador' => 'bg-danger',
                        'chofer' => 'bg-primary',
                        'pasajero' => 'bg-success',
                        default => 'bg-secondary'
                      };
                      ?>
                      <span class="badge <?= $rolColor ?>"><?= ucfirst($u['rol']) ?></span>
                    </td>
                    <td>
                      <!-- Ver -->
                      <a href="#"
                        class="btn btn-outline-primary btn-sm"
                        title="Ver"
                        data-bs-toggle="modal"
                        data-bs-target="#verUsuarioModal"
                        data-id="<?= $u['idUsuario'] ?>"
                        data-nombre="<?= htmlspecialchars($u['nombre']) ?>"
                        data-apellidos="<?= htmlspecialchars($u['apellidos']) ?>"
                        data-usuario="<?= htmlspecialchars($u['nombreUsuario'] ?? '') ?>"
                        data-cedula="<?= htmlspecialchars($u['cedula'] ?? '') ?>"
                        data-correo="<?= htmlspecialchars($u['correo'] ?? '') ?>"
                        data-telefono="<?= htmlspecialchars($u['telefono'] ?? '') ?>"
                        data-rol="<?= htmlspecialchars($u['rol'] ?? '') ?>"
                        data-fecha="<?= htmlspecialchars($u['fechaNacimiento'] ?? '') ?>"
                        data-fechaRegistro="<?= htmlspecialchars($u['fechaRegistro'] ?? '') ?>">
                        <i class="bi bi-eye"></i>
                      </a>

                      <!-- Editar -->
                      <a href="user_edit.php?id=<?= $u['idUsuario'] ?>" class="btn btn-outline-warning btn-sm" title="Editar">
                        <i class="bi bi-pencil-square"></i>
                      </a>

                      <!-- Eliminar (abre modal) -->
                      <button type="button"
                              class="btn btn-outline-danger btn-sm"
                              title="Eliminar"
                              data-bs-toggle="modal"
                              data-bs-target="#modalEliminarUsuario"
                              data-id="<?= $u['idUsuario'] ?>"
                              data-nombre="<?= htmlspecialchars($u['nombreUsuario'] ?? $u['nombre']) ?>">
                        <i class="bi bi-trash"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

        <?php else: ?>
          <div class="text-center py-5">
            <i class="bi bi-people fs-1 text-muted mb-3"></i>
            <h5 class="text-muted">No hay usuarios registrados</h5>
            <p class="text-muted">Comienza agregando tu primer usuario</p>
            <a href="user_create.php" class="btn btn-primary">
              <i class="bi bi-plus"></i> Agregar Primer Usuario
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Botón Volver -->
    <div class="mt-4 text-start">
      <a href="../dashboard_admin.php" class="btn btn-success">
        <i class="bi bi-arrow-left-circle me-2"></i> Volver al Panel
      </a>
    </div>
  </div>

  <!-- Modal Confirmar Eliminación -->
  <div class="modal fade" id="modalEliminarUsuario" tabindex="-1" aria-labelledby="modalEliminarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle"></i> Confirmar eliminación</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="mb-2">¿Estás seguro de que deseas eliminar al usuario <strong id="usuarioAEliminar"></strong>?</p>
          <p class="text-muted small mb-0">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle"></i> Cancelar
          </button>
          <form id="formEliminarUsuario" action="user_delete.php" method="POST" style="display:inline;">
            <input type="hidden" name="id" id="inputIdUsuario">
            <button type="submit" class="btn btn-danger">
              <i class="bi bi-trash"></i> Eliminar
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Información del Usuario -->
  <div class="modal fade" id="verUsuarioModal" tabindex="-1" aria-labelledby="verUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-sm">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title"><i class="bi bi-eye me-2"></i> Información del Usuario</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-5 text-center border-end">
              <div class="p-3">
                <i class="bi bi-person-circle text-success" style="font-size: 4rem;"></i>
                <h5 class="mt-3 fw-semibold" id="modal-nombreCompleto"></h5>
                <span id="modal-rol" class="badge bg-secondary mb-3"></span>
                <hr>
                <p><i class="bi bi-envelope"></i> <span id="modal-correo"></span></p>
                <p><i class="bi bi-telephone"></i> <span id="modal-telefono"></span></p>
              </div>
            </div>

            <div class="col-md-7">
              <div class="p-3">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle"></i> Información General</h6>
                <table class="table table-borderless">
                  <tbody>
                    <tr><th>Nombre de usuario:</th><td id="modal-usuario"></td></tr>
                    <tr><th>Cédula:</th><td id="modal-cedula"></td></tr>
                    <tr><th>Fecha de nacimiento:</th><td id="modal-fecha"></td></tr>
                    <tr><th>Fecha de registro:</th><td id="modal-fechaRegistro"></td></tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <a href="#" id="editarUsuarioLink" class="btn btn-warning">
            <i class="bi bi-pencil-square"></i> Editar
          </a>
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">
            <i class="bi bi-x-circle"></i> Cerrar
          </button>
        </div>

      </div>
    </div>
  </div>
  <footer class="text-center py-3 bg-dark text-white mt-5">
  © 2025 Aventones CR | Usuarios
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Script modal de eliminación -->
  <script>
    const modalEliminar = document.getElementById('modalEliminarUsuario');
    modalEliminar.addEventListener('show.bs.modal', event => {
      const button = event.relatedTarget;
      const idUsuario = button.getAttribute('data-id');
      const nombreUsuario = button.getAttribute('data-nombre');

      modalEliminar.querySelector('#usuarioAEliminar').textContent = nombreUsuario;
      modalEliminar.querySelector('#inputIdUsuario').value = idUsuario;
    });
  </script>

  <script src="../js/usuarios.js"></script>
</body>
</html>
