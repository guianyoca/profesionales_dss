<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
date_default_timezone_set('America/Argentina/Buenos_Aires');
                            $fecha=date('Y-m-d');
                            $hora=date('H:i:s');

$sql_update = mysqli_query($conexion, "UPDATE asistencias SET fecha_salida = '$hora' , hora_salida = '$hora' WHERE idusuario = $id_user AND fecha_ingreso= '$fecha'");

header('Location: asistencia.php');

?>
