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

$query = mysqli_query($conexion, "SELECT * FROM historias_clinicas WHERE usuario_carga='$id_user'  AND dni='$dni'");
$result = mysqli_num_rows($query);

if ($result ==0) {
    $query_insert = mysqli_query($conexion, "INSERT INTO historias_clinicas(nombre,dni,fecha_nacimiento,telefono,texto,usuario_carga) values ('$nombre', '$dni', '$fecha_nacimiento','$telefono','$texto_escaped','$id_user')");
    header('Location: cargar_historia_clinica.php');
}else{
    echo "<script>
    location.href = 'cargar_historia_clinica.php';
    alert('YA SE INGRESO LA HISTORIA CLINICA DEL PACIENTE')
    </script>";
}


?>