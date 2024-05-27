<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$nombre_user = $_SESSION['nombre'];
$nombre = $_POST['nombre'];
$dni = $_POST['dni'];
$fecha_ingresa = $_POST['fecha'];
$hora = $_POST['hora'];

$query = mysqli_query($conexion, "SELECT * FROM turnos WHERE usuario_carga='$nombre_user' AND fecha='$fecha_ingresa' AND dni='$dni'");
$result = mysqli_num_rows($query);

if($result!=0){
    echo "<script>
    location.href = 'pacientes_dia.php';
    alert('YA SE INGRESO EL PACIENTE EN EL DIA')
    </script>";
}else{
    $query_insert = mysqli_query($conexion, "INSERT INTO turnos(nombre,dni,fecha,hora,usuario_carga) values ('$nombre', '$dni', '$fecha_ingresa','$hora','$nombre_user')");
    header('Location: pacientes_dia.php');
}

// if ($result ==0) {
// $query_insert = mysqli_query($conexion, "INSERT INTO turnos(nombre,dni,fecha,hora,usuario_carga) values ('$nombre', '$dni', '$fecha_ingresa','$hora','$nombre_user')");
// header('Location: pacientes_dia.php');
// }else{
//     echo "<script>
//     location.href = 'pacientes_dia.php';
//     alert('YA SE INGRESO EL PACIENTE EN EL DIA')
//     </script>";
// }

?>