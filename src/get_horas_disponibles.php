<?php
session_start();
include "../conexion.php";

// Verificar si se han enviado los parámetros necesarios
if (!isset($_POST['fecha']) || empty($_POST['fecha'])) {
    echo json_encode(['error' => 'Se requiere una fecha']);
    exit;
}

$fecha = mysqli_real_escape_string($conexion, $_POST['fecha']);
$profesional_id = isset($_POST['profesional_id']) ? mysqli_real_escape_string($conexion, $_POST['profesional_id']) : null;

// Si es un profesional (rol=2), usar su propio ID
if ($_SESSION['rol'] == 2) {
    $profesional_id = $_SESSION['idUser'];
}

// Si no se ha especificado un profesional_id, no podemos continuar
if (!$profesional_id) {
    echo json_encode(['error' => 'Se requiere seleccionar un profesional']);
    exit;
}

// Obtener el nombre del profesional para las consultas de turnos existentes
$query_prof = mysqli_query($conexion, "SELECT nombre FROM usuario WHERE idusuario = '$profesional_id'");
$prof_nombre = mysqli_fetch_assoc($query_prof)['nombre'];

// Obtener configuración global del sistema
$query_config = "SELECT * FROM configuracion_sistema ORDER BY id DESC LIMIT 1";
$result_config = mysqli_query($conexion, $query_config);
$config_sistema = mysqli_fetch_assoc($result_config);

// Valores predeterminados por si no hay configuración
$duracion_turno = 30; // minutos
$pacientes_por_turno = 1;
$hora_inicio_default = '08:00:00';
$hora_fin_default = '18:00:00';
$dias_laborables = [1,2,3,4,5]; // De lunes a viernes

// Si hay configuración en la base de datos, usarla
if ($config_sistema) {
    $duracion_turno = $config_sistema['duracion_turno_default'];
    $pacientes_por_turno = $config_sistema['pacientes_por_turno_default'];
    $hora_inicio_default = $config_sistema['hora_inicio_default'];
    $hora_fin_default = $config_sistema['hora_fin_default'];
    $dias_laborables = explode(',', $config_sistema['dias_laborables']);
}

// Obtener el día de la semana para la fecha seleccionada
$dia_semana = date('N', strtotime($fecha)); // 1 (lunes) a 7 (domingo)

// Array para almacenar los horarios disponibles
$horas_disponibles = [];

// Consultar los horarios configurados para este profesional en este día de la semana
$query_horarios = "SELECT hora_inicio, hora_fin, capacidad 
                  FROM horarios_profesionales 
                  WHERE idusuario = '$profesional_id' 
                  AND dia_semana = '$dia_semana' 
                  AND estado = 1 
                  ORDER BY hora_inicio ASC";
                  
$result_horarios = mysqli_query($conexion, $query_horarios);

// Verificar si hay horarios específicos configurados
if (mysqli_num_rows($result_horarios) > 0) {
    // Crear array con todos los horarios posibles según configuración específica
    $horas_posibles = [];
    
    while ($horario_config = mysqli_fetch_assoc($result_horarios)) {
        $hora_inicio = new DateTime($horario_config['hora_inicio']);
        $hora_fin = new DateTime($horario_config['hora_fin']);
        $capacidad = $horario_config['capacidad'];
        
        // Generar horarios por intervalos según duración configurada
        $intervalo = new DateInterval("PT{$duracion_turno}M");
        $periodo = new DatePeriod($hora_inicio, $intervalo, $hora_fin);
        
        foreach ($periodo as $hora) {
            $hora_valor = $hora->format('H:i:s');
            
            // Consultar turnos ya existentes para esta hora
            $query_turnos = "SELECT COUNT(*) as total 
                            FROM turnos 
                            WHERE usuario_carga = '$prof_nombre' 
                            AND fecha = '$fecha' 
                            AND hora = '$hora_valor'";
                            
            $result_turnos = mysqli_query($conexion, $query_turnos);
            $turnos_existentes = mysqli_fetch_assoc($result_turnos)['total'];
            
            // Calcular disponibilidad
            $disponibles = $capacidad - $turnos_existentes;
            
            if ($disponibles > 0) {
                // Hay espacio disponible
                $horas_disponibles[] = [
                    'hora' => $hora_valor,
                    'hora_formato' => substr($hora_valor, 0, 5),
                    'disponibles' => $disponibles
                ];
            }
        }
    }
} else {
    // Si no hay horarios específicos, usar configuración general
    // Verificar si es un día laborable según configuración
    if (in_array($dia_semana, $dias_laborables)) {
        // Convertir horas a objetos DateTime
        $hora_inicio = new DateTime($hora_inicio_default);
        $hora_fin = new DateTime($hora_fin_default);
        
        // Generar todos los horarios posibles según la duración configurada
        $intervalo = new DateInterval("PT{$duracion_turno}M");
        $periodo = new DatePeriod($hora_inicio, $intervalo, $hora_fin);
        
        foreach ($periodo as $hora) {
            $hora_valor = $hora->format('H:i:s');
            
            // Consultar turnos ya existentes para esta hora
            $query_turnos = "SELECT COUNT(*) as total 
                            FROM turnos 
                            WHERE usuario_carga = '$prof_nombre' 
                            AND fecha = '$fecha' 
                            AND hora = '$hora_valor'";
                            
            $result_turnos = mysqli_query($conexion, $query_turnos);
            $turnos_existentes = mysqli_fetch_assoc($result_turnos)['total'];
            
            // Calcular disponibilidad
            $disponibles = $pacientes_por_turno - $turnos_existentes;
            
            if ($disponibles > 0) {
                // Hay espacio disponible
                $horas_disponibles[] = [
                    'hora' => $hora_valor,
                    'hora_formato' => substr($hora_valor, 0, 5),
                    'disponibles' => $disponibles
                ];
            }
        }
    }
}

// Devolver las horas disponibles en formato JSON
header('Content-Type: application/json');
echo json_encode([
    'fecha' => $fecha,
    'profesional_id' => $profesional_id,
    'profesional_nombre' => $prof_nombre,
    'horas_disponibles' => $horas_disponibles
]);
?>
