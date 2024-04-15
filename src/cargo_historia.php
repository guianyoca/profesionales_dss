<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$nombre_user = $_SESSION['nombre'];
$nombre = $_POST['nombre'];
$dni = $_POST['dni'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$telefono = $_POST['telefono'];
$texto = $_POST['texto'];
$texto_escaped = mysqli_real_escape_string($conexion, $texto);


$query_insert = mysqli_query($conexion, "INSERT INTO historias_clinicas(nombre,dni,fecha_nacimiento,telefono,texto,usuario_carga) values ('$nombre', '$dni', '$fecha_nacimiento','$telefono','$texto_escaped','$id_user')");
header('Location: historias_clinicas.php');

?>