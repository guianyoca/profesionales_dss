<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];

$query_insert = mysqli_query($conexion, "INSERT INTO asistencias(idusuario,fecha_ingreso,hora_ingreso) values ('$id_user', NOW(), NOW())");
header('Location: asistencia.php');

?>