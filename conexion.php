<?php
    $host = "localhost";
    $user = "root";
    $clave = "";
    $bd = "profesionales_dss";
    $conexion = mysqli_connect($host,$user,$clave,$bd);
    if (mysqli_connect_errno()){
        echo "No se pudo conectar a la base de datos";
        exit();
    }
    mysqli_select_db($conexion,$bd) or die("No se encuentra la base de datos");
    mysqli_set_charset($conexion,"utf8");
    date_default_timezone_set('America/Argentina/Buenos_Aires'); 
?>
