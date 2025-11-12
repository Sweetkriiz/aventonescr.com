<?php
session_start();
require_once '../../config/funciones_ride.php';

// Obtiene el ID del viaje desde la URL (GET) y lo convierte en entero por seguridad
// Si no viene ningún ID, se usa 0 como valor por defecto
$idViaje = intval($_GET['id'] ?? 0);
// Llama a la función que elimina el viaje en la base de datos
if (deleteViaje($idViaje)) {
  $_SESSION['success'] = "Ride eliminado correctamente.";
  header("Location: listar_ride.php");
  exit();
} else {
  echo "<div class='alert alert-danger text-center mt-5'>Error al eliminar el ride.</div>";
}
