<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$fecha_salida=date('Y-m-d');

$sql_update = mysqli_query($conexion, "UPDATE asistencias SET fecha_salida = NOW() , hora_salida = NOW() WHERE idusuario = $id_user AND fecha_ingreso='$fecha_salida'");

header('Location: asistencia.php');

?>
