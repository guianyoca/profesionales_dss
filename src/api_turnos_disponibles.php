<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluir archivo de conexión a la base de datos
require_once "../conexion.php";

// Función principal para obtener turnos disponibles
function getTurnosDisponibles($conexion) {
    // Parámetros de filtro
    $fecha = isset($_GET['fecha']) ? mysqli_real_escape_string($conexion, $_GET['fecha']) : date('Y-m-d');
    $profesional_nombre = isset($_GET['profesional']) ? mysqli_real_escape_string($conexion, $_GET['profesional']) : null;
    $dias_futuros = isset($_GET['dias_futuros']) ? intval($_GET['dias_futuros']) : 7; // Por defecto, buscar hasta 7 días adelante
    
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
    
    // Arreglo que contendrá la respuesta
    $respuesta = array(
        'status' => 'success',
        'message' => 'Turnos disponibles',
        'data' => array(),
        'buttons' => array(),
        'session_data' => array(
            'last_search' => $fecha,
            'selected_professional' => $profesional_nombre
        )
    );
    
    // Construimos la consulta para obtener profesionales (sólo rol=2)
    $where_profesional = "";
    if ($profesional_nombre) {
        $where_profesional = " AND (u.nombre = '$profesional_nombre') ";
    }
    
    // Consulta para obtener los profesionales
    $query_profesionales = "SELECT u.idusuario, u.nombre 
                          FROM usuario u 
                          WHERE u.rol = 2 
                          $where_profesional
                          ORDER BY u.nombre ASC";
    
    $result_profesionales = mysqli_query($conexion, $query_profesionales);
    
    // Fecha límite para la búsqueda
    $fecha_limite = date('Y-m-d', strtotime($fecha . ' + ' . $dias_futuros . ' days'));
    
    // Procesar cada profesional
    while ($profesional = mysqli_fetch_assoc($result_profesionales)) {
        $prof_id = $profesional['idusuario'];
        $prof_nombre = $profesional['nombre'];
        
        // Verificar horarios disponibles para cada día en el rango
        $fecha_actual = $fecha;
        
        while (strtotime($fecha_actual) <= strtotime($fecha_limite)) {
            // Array para almacenar los horarios disponibles de este profesional en este día
            $horarios_disponibles = array();
            
            // Obtener el día de la semana para la fecha actual (1 para lunes, 7 para domingo)
            $dia_semana = date('N', strtotime($fecha_actual));
            
            // Consultar los horarios configurados para este profesional en este día de la semana
            $query_horarios = "SELECT hora_inicio, hora_fin, capacidad 
                              FROM horarios_profesionales 
                              WHERE idusuario = '$prof_id' 
                              AND dia_semana = '$dia_semana' 
                              AND estado = 1 
                              ORDER BY hora_inicio ASC";
                              
            $result_horarios = mysqli_query($conexion, $query_horarios);
            
            // Verificar si hay horarios configurados
            if (mysqli_num_rows($result_horarios) > 0) {
                // Crear array con todos los horarios posibles según configuración
                $horas_posibles = array();
                
                while ($horario_config = mysqli_fetch_assoc($result_horarios)) {
                    $hora_inicio = new DateTime($horario_config['hora_inicio']);
                    $hora_fin = new DateTime($horario_config['hora_fin']);
                    $capacidad = $horario_config['capacidad'];
                    
                    // Generar horarios por intervalos de 1 hora
                    $intervalo = new DateInterval('PT1H'); // 1 hora
                    $periodo = new DatePeriod($hora_inicio, $intervalo, $hora_fin);
                    
                    foreach ($periodo as $hora) {
                        $horas_posibles[] = array(
                            'hora' => $hora->format('H:i:s'),
                            'capacidad' => $capacidad
                        );
                    }
                }
            } else {
                // Si no hay horarios específicos configurados para este profesional,
                // usar la configuración global del sistema
                $dia_semana_numero = date('N', strtotime($fecha_actual)); // 1 (lunes) a 7 (domingo)
                
                // Verificar si es un día laborable según configuración global
                if (in_array($dia_semana_numero, $dias_laborables)) {
                    // Crear array con horarios predeterminados
                    $horas_posibles = array();
                    
                    // Convertir horas a objetos DateTime
                    $hora_inicio = new DateTime($hora_inicio_default);
                    $hora_fin = new DateTime($hora_fin_default);
                    
                    // Calcular el intervalo basado en la duración del turno
                    $intervalo_minutos = "PT{$duracion_turno}M"; // Formato: PT30M para 30 minutos
                    $intervalo = new DateInterval($intervalo_minutos);
                    
                    // Generar todos los horarios posibles según la configuración
                    $periodo = new DatePeriod($hora_inicio, $intervalo, $hora_fin);
                    
                    foreach ($periodo as $hora) {
                        $horas_posibles[] = array(
                            'hora' => $hora->format('H:i:s'),
                            'capacidad' => $pacientes_por_turno
                        );
                    }
                } else {
                    // No es un día laborable según configuración
                    $horas_posibles = array();
                }
            }
            
            // Para cada hora posible, verificar si hay turnos ya asignados
            foreach ($horas_posibles as $hora_item) {
                $hora_valor = $hora_item['hora'];
                
                // Consulta para contar cuántos turnos ya existen para esta fecha, hora y profesional
                $query_turnos = "SELECT COUNT(*) as total 
                                FROM turnos 
                                WHERE usuario_carga = '$prof_nombre' 
                                AND fecha = '$fecha_actual' 
                                AND hora = '$hora_valor'";
                                
                $result_turnos = mysqli_query($conexion, $query_turnos);
                $turnos_existentes = mysqli_fetch_assoc($result_turnos)['total'];
                
                // Usar la capacidad configurada para este horario
                $capacidad_maxima = $hora_item['capacidad'];
                $disponibles = $capacidad_maxima - $turnos_existentes;
                
                if ($disponibles > 0) {
                    // Hay espacio disponible, agregar al array de horarios
                    $horarios_disponibles[] = array(
                        'hora' => $hora_valor,
                        'disponibles' => $disponibles
                    );
                }
            }
            
            // Si hay horarios disponibles para este día, agregarlo a la respuesta
            if (!empty($horarios_disponibles)) {
                $data_entry = array(
                    'profesional' => array(
                        'id' => $prof_id,
                        'nombre' => $prof_nombre
                    ),
                    'fecha' => $fecha_actual,
                    'horarios' => $horarios_disponibles
                );
                
                $respuesta['data'][] = $data_entry;
                
                // Crear botones para este profesional y fecha
                $fecha_formateada = date('d/m/Y', strtotime($fecha_actual));
                
                // Botón para seleccionar el profesional
                $respuesta['buttons'][] = array(
                    'type' => 'profesional',
                    'text' => "Dr(a). {$prof_nombre}",
                    'value' => $prof_nombre,
                    'id' => $prof_id
                );
                
                // Botones para cada horario disponible
                foreach ($horarios_disponibles as $horario) {
                    $hora_formateada = substr($horario['hora'], 0, 5); // Formato HH:MM
                    $button_text = "{$hora_formateada} - {$horario['disponibles']} " . 
                                   ($horario['disponibles'] > 1 ? "lugares" : "lugar");
                                   
                    // Crear un identificador único para este turno
                    $turno_id = "t_{$prof_id}_{$fecha_actual}_{$hora_formateada}";
                    
                    $respuesta['buttons'][] = array(
                        'type' => 'horario',
                        'text' => $button_text,
                        'value' => $hora_formateada,
                        'profesional' => $prof_nombre,
                        'fecha' => $fecha_actual,
                        'turno_id' => $turno_id,
                        'disponibles' => $horario['disponibles']
                    );
                    
                    // También guardar la referencia en session_data para consultas posteriores
                    $respuesta['session_data'][$turno_id] = array(
                        'profesional_id' => $prof_id,
                        'profesional_nombre' => $prof_nombre,
                        'fecha' => $fecha_actual,
                        'hora' => $horario['hora'],
                        'disponibles' => $horario['disponibles']
                    );
                }
                
                // Si sólo queremos una entrada por profesional, podemos hacer break aquí
                // para mostrar solo el primer día con disponibilidad
                break;
            }
            
            // Avanzar al siguiente día
            $fecha_actual = date('Y-m-d', strtotime($fecha_actual . ' + 1 day'));
        }
    }
    
    return $respuesta;
}

// Procesar solicitud GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $respuesta = getTurnosDisponibles($conexion);
    echo json_encode($respuesta);
} else {
    // Método no permitido
    http_response_code(405);
    echo json_encode(array(
        "status" => "error",
        "message" => "Método no permitido"
    ));
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
