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

  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }

    .btn-sm {
      border-radius: 50%;
      width: 34px;
      height: 34px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      font-size: 1rem;
      transition: all 0.2s ease-in-out;
    }

    .btn-sm:hover {
      transform: scale(1.1);
    }

    .table td {
      vertical-align: middle;
    }

    .modal-content {
      border-radius: 10px;
      overflow: hidden;
    }

    .table th {
      font-weight: 600;
    }

    .table td,
    .table th {
      padding: 0.5rem;
    }

    footer {
      background: #198754;
    }

    .modal-body i {
      color: #198754;
    }
  </style>
</head>

<body class="bg-light">

  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold text-success">Usuarios</h2>
      <a href="usuario_create.php" class="btn btn-success">+ Agregar Usuario</a>
    </div>

    <!-- Tabla de usuarios -->
    <div class="card mb-4 shadow-sm">
      <div class="card-header bg-success text-white fw-bold">Lista de Usuarios</div>
      <div class="card-body">
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
                      <!-- Ver (abre modal) -->
                      <a href="#"
                        class="btn btn-outline-primary btn-sm"
                        title="Ver"
                        data-bs-toggle="modal"
                        data-bs-target="#verUsuarioModal"
                        data-id="<?= $u['idUsuario'] ?>"
                        data-nombre="<?= htmlspecialchars($u['nombre']) ?>"
                        data-apellidos="<?= htmlspecialchars($u['apellidos']) ?>"
                        data-correo="<?= htmlspecialchars($u['correo']) ?>"
                        data-telefono="<?= htmlspecialchars($u['telefono']) ?>"
                        data-rol="<?= htmlspecialchars($u['rol']) ?>"
                        data-usuario="<?= htmlspecialchars($u['nombreUsuario'] ?? '') ?>"
                        data-contrasena="<?= htmlspecialchars($u['contrasena'] ?? '') ?>"
                        data-cedula="<?= htmlspecialchars($u['cedula'] ?? '') ?>"
                        data-fecha="<?= htmlspecialchars($u['fechaNacimiento'] ?? '') ?>">
                        <i class="bi bi-eye"></i>
                      </a>

                      <!-- Editar -->
                      <a href="user_edit.php?id=<?= $u['idUsuario'] ?>" class="btn btn-outline-warning btn-sm" title="Editar">
                        <i class="bi bi-pencil-square"></i>
                      </a>

                      <!-- Eliminar -->
                      <a href="user_delete.php?id=<?= $u['idUsuario'] ?>" class="btn btn-outline-danger btn-sm" title="Eliminar" onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">
                        <i class="bi bi-trash"></i>
                      </a>
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
            <a href="usuario_create.php" class="btn btn-primary">
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

  <!-- Modal Detalles del Usuario -->
  <div class="modal fade" id="verUsuarioModal" tabindex="-1" aria-labelledby="verUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-sm">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title"><i class="bi bi-eye me-2"></i> Información del Usuario</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Columna Izquierda -->
            <div class="col-md-4 text-center border-end">
              <div class="p-3">
                <i class="bi bi-person-circle text-success" style="font-size: 4rem;"></i>
                <h5 class="mt-3" id="modal-nombre"></h5>
                <span id="modal-rol" class="badge bg-secondary mt-2"></span>
                <hr>
                <p><i class="bi bi-person-badge"></i> <span id="modal-cedula"></span></p>
                <p><i class="bi bi-calendar-date"></i> <span id="modal-fecha"></span></p>
                <p><i class="bi bi-telephone"></i> <span id="modal-telefono"></span></p>
                <p><i class="bi bi-envelope"></i> <span id="modal-correo"></span></p>
              </div>
            </div>
            <!-- Columna Derecha -->
            <div class="col-md-8">
              <div class="p-3">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle"></i> Información General</h6>
                <table class="table table-borderless">
                  <tbody>
                    <tr><th>ID:</th><td id="modal-id"></td></tr>
                    <tr><th>Nombre completo:</th><td id="modal-nombreCompleto"></td></tr>
                    <tr><th>Nombre de usuario:</th><td id="modal-usuario"></td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <a href="#" id="editarUsuarioLink" class="btn btn-warning text-dark">
            <i class="bi bi-pencil-square"></i> Editar Usuario
          </a>
          <a href="#" id="eliminarUsuarioLink" class="btn btn-danger">
            <i class="bi bi-trash"></i> Eliminar Usuario
          </a>
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">
            <i class="bi bi-x-circle"></i> Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

  <footer class="text-center py-3 text-white mt-5">
    © 2025 Aventones CR | Administración de Usuarios
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const modal = document.getElementById('verUsuarioModal');
      modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        // Datos del botón
        const id = button.getAttribute('data-id');
        const nombre = button.getAttribute('data-nombre');
        const apellidos = button.getAttribute('data-apellidos');
        const correo = button.getAttribute('data-correo');
        const telefono = button.getAttribute('data-telefono');
        const rol = button.getAttribute('data-rol');
        const usuario = button.getAttribute('data-usuario');
        const contrasena = button.getAttribute('data-contrasena');
        const cedula = button.getAttribute('data-cedula');
        const fecha = button.getAttribute('data-fecha');

        // Asignar valores al modal
        document.getElementById('modal-id').textContent = id;
        document.getElementById('modal-nombre').textContent = nombre;
        document.getElementById('modal-nombreCompleto').textContent = nombre + ' ' + apellidos;
        document.getElementById('modal-correo').textContent = correo;
        document.getElementById('modal-telefono').textContent = telefono;
        document.getElementById('modal-usuario').textContent = usuario;
        document.getElementById('modal-cedula').textContent = cedula;
        document.getElementById('modal-fecha').textContent = fecha;
        document.getElementById('modal-contrasena').textContent = '********';
        document.getElementById('modal-rol').textContent = rol;
        document.getElementById('modal-rol2').textContent = rol;

        // Enlaces dinámicos
        document.getElementById('editarUsuarioLink').href = "user_edit.php?id=" + id;
        document.getElementById('eliminarUsuarioLink').href = "user_delete.php?id=" + id;

        // Colores del badge según rol
        const rolBadge = document.getElementById('modal-rol');
        rolBadge.className = 'badge';
        switch (rol) {
          case 'administrador':
            rolBadge.classList.add('bg-danger');
            break;
          case 'chofer':
            rolBadge.classList.add('bg-primary');
            break;
          case 'pasajero':
            rolBadge.classList.add('bg-success');
            break;
          default:
            rolBadge.classList.add('bg-secondary');
        }
      });
    });
  </script>
</body>

</html>
