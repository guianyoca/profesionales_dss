<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$nombre_user = $_SESSION['nombre'];
$nombre = $_POST['nombre'];
$dni = $_POST['dni'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];

$query_insert = mysqli_query($conexion, "INSERT INTO turnos(nombre,dni,fecha,hora,usuario_carga) values ('$nombre', '$dni', '$fecha','$hora','$nombre_user')");

header('Location: pacientes_dia.php');

?>