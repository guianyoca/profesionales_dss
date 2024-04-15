<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$fecha_salida=date('Y-m-d');
$id_turno=$_GET['id_turno'];

$sql_update = mysqli_query($conexion, "UPDATE turnos SET estado = 2 WHERE idturno = $id_turno ");

header('Location: pacientes_dia.php');

?>