<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
                            $hoy=date('Y-m-d');
                            $query = mysqli_query($conexion, "SELECT * FROM asistencias WHERE idusuario='$id_user' AND fecha_ingreso='$hoy'");
                            $result = mysqli_num_rows($query);
                            if ($result == 0) {                                     
                                    $query_insert = mysqli_query($conexion, "INSERT INTO asistencias(idusuario,fecha_ingreso,hora_ingreso) values ('$id_user', NOW(), NOW())");
                                    header('Location: asistencia.php');
                            }else{
                                echo "<script>
                                location.href = 'asistencia.php';
                                alert('YA SE INGRESO ASISTENCIA EN EL DIA')</script>";                             
                            }
                                


?>