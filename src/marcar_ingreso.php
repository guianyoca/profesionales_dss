<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
                            date_default_timezone_set('America/Argentina/Buenos_Aires');
                            $fecha=date('Y-m-d');
                            $hora=date('H:i:s');
                            $query = mysqli_query($conexion, "SELECT * FROM asistencias WHERE idusuario='$id_user' AND fecha_ingreso='$hoy'");
                            $result = mysqli_num_rows($query);
                            if ($result == 0) {                                     
                                    $query_insert = mysqli_query($conexion, "INSERT INTO asistencias(idusuario,fecha_ingreso,hora_ingreso) values ('$id_user', '$fecha', '$hora')");
                                    header('Location: asistencia.php');
                            }else{
                                echo "<script>
                                location.href = 'asistencia.php';
                                alert('YA SE INGRESO ASISTENCIA EN EL DIA')</script>";                             
                            }
                                


?>