<?php
include "conexion.php";

// Verificar que la tabla existe
$result = mysqli_query($conexion, "SHOW TABLES LIKE 'horarios_profesionales'");
if (mysqli_num_rows($result) == 0) {
    die("<p>La tabla horarios_profesionales no existe en la base de datos</p>");
}

// Obtener estructura de la tabla
echo "<h3>Estructura de la tabla horarios_profesionales</h3>";
$result = mysqli_query($conexion, "DESCRIBE horarios_profesionales");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Llave</th><th>Default</th></tr>";

while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['Field']}</td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>{$row['Default']}</td>";
    echo "</tr>";
}
echo "</table><br>";

// Verificar si hay registros
$result = mysqli_query($conexion, "SELECT COUNT(*) as total FROM horarios_profesionales");
$count = mysqli_fetch_assoc($result);
echo "<p>Total de registros configurados: {$count['total']}</p>";

// Mostrar ejemplos si existen registros
if ($count['total'] > 0) {
    echo "<h3>Ejemplos de horarios configurados</h3>";
    $sql = "SELECT hp.*, u.nombre 
           FROM horarios_profesionales hp
           LEFT JOIN usuario u ON hp.id_profesional = u.idusuario
           LIMIT 5";
           
    $result = mysqli_query($conexion, $sql);
    
    if (!$result) {
        echo "<p>Error en la consulta: " . mysqli_error($conexion) . "</p>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Profesional</th>";
        echo "<th>ID Prof</th>";
        echo "<th>Día</th>";
        echo "<th>Inicio</th>";
        echo "<th>Fin</th>";
        echo "<th>Duración</th>";
        echo "<th>Pacientes/turno</th>";
        echo "<th>Estado</th>";
        echo "</tr>";
        
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nombre']}</td>";
            echo "<td>{$row['id_profesional']}</td>";
            echo "<td>" . (isset($dias[$row['dia_semana']]) ? $dias[$row['dia_semana']] : $row['dia_semana']) . "</td>";
            echo "<td>{$row['hora_inicio']}</td>";
            echo "<td>{$row['hora_fin']}</td>";
            echo "<td>{$row['duracion_turno']} min</td>";
            echo "<td>{$row['pacientes_por_turno']}</td>";
            echo "<td>" . ($row['activo'] == 1 ? 'Activo' : 'Inactivo') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}
?>
