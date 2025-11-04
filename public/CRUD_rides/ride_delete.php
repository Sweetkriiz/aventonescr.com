<?php
session_start();
require_once '../../config/funciones_ride.php';

$idViaje = intval($_GET['id'] ?? 0);
if (deleteViaje($idViaje)) {
  $_SESSION['success'] = "Ride eliminado correctamente.";
  header("Location: listar_ride.php");
  exit();
} else {
  echo "<div class='alert alert-danger text-center mt-5'>Error al eliminar el ride.</div>";
}
?>
