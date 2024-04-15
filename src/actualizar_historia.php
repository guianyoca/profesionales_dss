<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$nombre_user = $_SESSION['nombre'];
$id_historia = $_POST['id_historia'];
$nombre = $_POST['nombre'];
$dni = $_POST['dni'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$telefono = $_POST['telefono'];
$texto = $_POST['texto'];
$texto_escaped = mysqli_real_escape_string($conexion, $texto);


$sql_update = mysqli_query($conexion, "UPDATE historias_clinicas SET nombre = '$nombre' , dni = '$dni' , fecha_nacimiento = '$fecha_nacimiento', telefono = '$telefono', texto = '$texto_escaped' WHERE idhistoria= '$id_historia'");

header('Location: historias_clinicas.php');
?>