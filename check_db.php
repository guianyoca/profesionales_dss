<?php
include "conexion.php";
$result = mysqli_query($conexion, "DESCRIBE turnos");
echo "<pre>";
while($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}
echo "</pre>";
?>
