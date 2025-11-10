document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('verUsuarioModal');

  modal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;

    // Extraer datos del botón
    const id = button.getAttribute('data-id');
    const nombre = button.getAttribute('data-nombre') || '';
    const apellidos = button.getAttribute('data-apellidos') || '';
    const correo = button.getAttribute('data-correo') || '';
    const telefono = button.getAttribute('data-telefono') || '';
    const rol = button.getAttribute('data-rol') || '';
    const usuario = button.getAttribute('data-usuario') || '';
    const cedula = button.getAttribute('data-cedula') || '';
    const fecha = button.getAttribute('data-fecha') || '';
    const fechaRegistro = button.getAttribute('data-fechaRegistro') || '';

    // Llenar campos en el modal
    document.getElementById('modal-nombreCompleto').textContent = `${nombre} ${apellidos}`.trim();
    document.getElementById('modal-rol').textContent = rol;
    document.getElementById('modal-correo').textContent = correo;
    document.getElementById('modal-telefono').textContent = telefono;
    document.getElementById('modal-usuario').textContent = usuario || '(Sin usuario asignado)';
    document.getElementById('modal-cedula').textContent = cedula;
    document.getElementById('modal-fecha').textContent = fecha;
    document.getElementById('modal-fechaRegistro').textContent = fechaRegistro;

    // Enlaces dinámicos
    document.getElementById('editarUsuarioLink').href = "user_edit.php?id=" + id;
    document.getElementById('eliminarUsuarioLink').href = "user_delete.php?id=" + id;

    // Color del badge según rol
    const rolBadge = document.getElementById('modal-rol');
    rolBadge.className = 'badge';
    switch (rol.toLowerCase()) {
      case 'administrador': rolBadge.classList.add('bg-danger'); break;
      case 'chofer': rolBadge.classList.add('bg-primary'); break;
      case 'pasajero': rolBadge.classList.add('bg-success'); break;
      default: rolBadge.classList.add('bg-secondary');
    }
  });
});

