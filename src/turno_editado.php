<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$nombre_user = $_SESSION['nombre'];
$id_turno = $_POST['id_turno'];
$nombre = $_POST['nombre'];
$dni = $_POST['dni'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];



$sql_update = mysqli_query($conexion, "UPDATE turnos SET nombre = '$nombre' , dni = '$dni' , fecha= '$fecha', hora = '$hora' WHERE idturno= '$id_turno'");

header('Location: todos_turnos.php');
?>