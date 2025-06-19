<?php
include "conexion.php";

// Check the structure of the turnos table
echo "<h2>Estructura de la tabla 'turnos'</h2>";
$result = mysqli_query($conexion, "DESCRIBE turnos");
echo "<pre>";
while($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}
echo "</pre>";

// Check an example of existing data
echo "<h2>Ejemplo de datos en la tabla 'turnos'</h2>";
$result = mysqli_query($conexion, "SELECT * FROM turnos LIMIT 1");
echo "<pre>";
while($row = mysqli_fetch_assoc($result)) {
    print_r($row);
}
echo "</pre>";

// Check for any errors
echo "<h2>Posibles errores</h2>";
echo "Error actual: " . mysqli_error($conexion);
?>
