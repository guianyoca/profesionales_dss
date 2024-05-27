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

$query = mysqli_query($conexion, "SELECT * FROM turnos WHERE usuario_carga='$nombre_user' AND fecha='$fecha' AND dni='$dni'");
$result = mysqli_num_rows($query);
$data = mysqli_fetch_assoc($query);

if ($result ==0) {
    $sql_update = mysqli_query($conexion, "UPDATE turnos SET nombre = '$nombre' , dni = '$dni' , fecha= '$fecha', hora = '$hora' WHERE idturno= '$id_turno'");
header('Location: todos_turnos.php');
}if(($result==1)&&($data['dni']==$dni)){
    $sql_update = mysqli_query($conexion, "UPDATE turnos SET nombre = '$nombre' , fecha= '$fecha', hora = '$hora' WHERE idturno= '$id_turno'");
    echo "<script>
    alert('LOS DATOS DEL PACIENTE FUERON MODIFICADOS')
    location.href = 'todos_turnos.php';
    </script>";

}else{
    echo "<script>
    location.href = 'todos_turnos.php';
    alert('YA SE INGRESO EL PACIENTE EN EL DIA')
    </script>";
}

?>