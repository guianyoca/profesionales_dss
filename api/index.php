<?php
// Configurar encabezados para API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Para solicitudes OPTIONS (pre-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

// Incluir archivo de conexión a la base de datos
require_once "../conexion.php";

// Definir respuesta por defecto
$response = [
    'status' => 'error',
    'message' => 'Endpoint no válido',
    'data' => null
];

// Obtener la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];

// Determinar la base path según varios escenarios posibles
if (strpos($request_uri, '/profesionales_dss/api/') !== false) {
    $base_path = '/profesionales_dss/api/';
} elseif (strpos($request_uri, '/api/') !== false) {
    $base_path = '/api/';
} else {
    // Si no podemos detectar automáticamente, usamos un enfoque más simple
    // y asumimos que estamos en la raíz del directorio api
    $path_parts = explode('?', $request_uri);
    $base_path = '';
    $request_uri = $path_parts[0];
}

// Extraer la ruta de la API
$route = str_replace($base_path, '', $request_uri);
$route = strtok($route, '?'); // Eliminar query string si existe

// Para debug - desactivar en producción
// error_log("REQUEST_URI: {$_SERVER['REQUEST_URI']}");
// error_log("BASE_PATH: {$base_path}");
// error_log("ROUTE: {$route}");

// Router básico
switch ($route) {
    case '':
    case '/':
        // Documentación básica de la API
        $response = [
            'status' => 'success',
            'message' => 'API de Profesionales DSS',
            'version' => '1.0',
            'endpoints' => [
                'GET /turnos/disponibles' => 'Obtener turnos disponibles',
                'POST /turnos/crear' => 'Crear un nuevo turno',
                'GET /profesionales' => 'Listar profesionales disponibles'
            ]
        ];
        break;
        
    case 'turnos/disponibles':
        handleGetTurnosDisponibles($conexion);
        break;
        
    case 'turnos/crear':
        handleCrearTurno($conexion);
        break;
        
    case 'profesionales':
        handleGetProfesionales($conexion);
        break;
        
    default:
        // Endpoint no encontrado
        http_response_code(404);
        $response = [
            'status' => 'error',
            'message' => 'Endpoint no encontrado',
            'data' => null
        ];
        break;
}

// Función para obtener turnos disponibles
function handleGetTurnosDisponibles($conexion) {
    global $response;
    
    try {
        // Parámetros de búsqueda
        $fecha = isset($_GET['fecha']) ? mysqli_real_escape_string($conexion, $_GET['fecha']) : date('Y-m-d');
        $id_profesional = isset($_GET['profesional']) ? intval($_GET['profesional']) : 0;
        $nombre_profesional = isset($_GET['nombre_profesional']) ? mysqli_real_escape_string($conexion, $_GET['nombre_profesional']) : '';
        
        // Validaciones básicas
        if (empty($fecha)) {
            throw new Exception("La fecha es obligatoria");
        }
        
        // Convertir fecha al formato correcto
        $fecha_obj = new DateTime($fecha);
        $fecha_formateada = $fecha_obj->format('Y-m-d');
        $dia_semana = intval($fecha_obj->format('w')); // 0 (domingo) a 6 (sábado)
        
        // Construir la consulta según los parámetros
        if ($id_profesional > 0) {
            // Buscar por ID de profesional
            $query_prof = "SELECT idusuario, nombre FROM usuario WHERE idusuario = $id_profesional AND rol = 2";
        } elseif (!empty($nombre_profesional)) {
            // Buscar por nombre de profesional
            $query_prof = "SELECT idusuario, nombre FROM usuario WHERE nombre LIKE '%$nombre_profesional%' AND rol = 2";
        } else {
            // Todos los profesionales
            $query_prof = "SELECT idusuario, nombre FROM usuario WHERE rol = 2";
        }
        
        $result_prof = mysqli_query($conexion, $query_prof);
        
        if (mysqli_num_rows($result_prof) == 0) {
            throw new Exception("No se encontró el profesional especificado");
        }
        
        $turnos_disponibles = [];
        
        // Para cada profesional
        while ($profesional = mysqli_fetch_assoc($result_prof)) {
            $id_prof = $profesional['idusuario'];
            $nombre_prof = $profesional['nombre'];
            
            // Obtener configuración de horarios para este profesional y día
            $query_horarios = "SELECT * FROM horarios_profesionales 
                              WHERE id_profesional = $id_prof 
                              AND dia_semana = $dia_semana 
                              AND activo = 1";
            
            $result_horarios = mysqli_query($conexion, $query_horarios);
            
            // Si no hay configuración específica, usar la configuración por defecto
            if (mysqli_num_rows($result_horarios) == 0) {
                // Verificar si el día está en la configuración por defecto
                $query_config = "SELECT * FROM configuracion_sistema LIMIT 1";
                $result_config = mysqli_query($conexion, $query_config);
                
                if (mysqli_num_rows($result_config) > 0) {
                    $config = mysqli_fetch_assoc($result_config);
                    $dias_laborables = explode(',', $config['dias_laborables']);
                    
                    // Si el día actual no es laborable según la configuración
                    if (!in_array($dia_semana, $dias_laborables)) {
                        continue; // Pasar al siguiente profesional
                    }
                    
                    // Usar configuración por defecto
                    $hora_inicio = $config['hora_inicio_default'];
                    $hora_fin = $config['hora_fin_default'];
                    $duracion_turno = $config['duracion_turno_default'];
                    $pacientes_por_turno = $config['pacientes_por_turno_default'];
                    
                    // Generar horarios disponibles
                    $slots = generarHorariosDisponibles(
                        $conexion,
                        $fecha_formateada,
                        $hora_inicio,
                        $hora_fin,
                        $duracion_turno,
                        $pacientes_por_turno,
                        $nombre_prof
                    );
                    
                    if (!empty($slots)) {
                        $turnos_disponibles[] = [
                            'profesional' => [
                                'id' => $id_prof,
                                'nombre' => $nombre_prof
                            ],
                            'fecha' => $fecha_formateada,
                            'horarios' => $slots
                        ];
                    }
                }
            } else {
                // Usar configuración específica del profesional
                while ($horario = mysqli_fetch_assoc($result_horarios)) {
                    $hora_inicio = $horario['hora_inicio'];
                    $hora_fin = $horario['hora_fin'];
                    $duracion_turno = $horario['duracion_turno'];
                    $pacientes_por_turno = $horario['pacientes_por_turno'];
                    
                    // Generar horarios disponibles
                    $slots = generarHorariosDisponibles(
                        $conexion,
                        $fecha_formateada,
                        $hora_inicio,
                        $hora_fin,
                        $duracion_turno,
                        $pacientes_por_turno,
                        $nombre_prof
                    );
                    
                    if (!empty($slots)) {
                        $turnos_disponibles[] = [
                            'profesional' => [
                                'id' => $id_prof,
                                'nombre' => $nombre_prof
                            ],
                            'fecha' => $fecha_formateada,
                            'horarios' => $slots
                        ];
                    }
                }
            }
        }
        
        http_response_code(200);
        $response = [
            'status' => 'success',
            'message' => 'Turnos disponibles',
            'data' => $turnos_disponibles
        ];
        
    } catch (Exception $e) {
        http_response_code(400);
        $response = [
            'status' => 'error',
            'message' => $e->getMessage(),
            'data' => null
        ];
    }
}

// Función para generar los horarios disponibles
function generarHorariosDisponibles($conexion, $fecha, $hora_inicio, $hora_fin, $duracion_turno, $pacientes_por_turno, $nombre_profesional) {
    // Convertir a objetos DateTime
    $inicio = new DateTime("$fecha $hora_inicio");
    $fin = new DateTime("$fecha $hora_fin");
    
    // Duración en formato intervalo
    $intervalo = new DateInterval("PT{$duracion_turno}M"); // PT30M = 30 minutos
    
    $slots = [];
    $current = clone $inicio;
    
    // Generar todos los slots posibles
    while ($current < $fin) {
        $hora_slot = $current->format('H:i:s');
        
        // Verificar cuántos turnos ya hay en este horario
        $query = "SELECT COUNT(*) as total FROM turnos 
                  WHERE fecha = '$fecha' 
                  AND hora = '$hora_slot' 
                  AND usuario_carga = '$nombre_profesional'";
        
        $result = mysqli_query($conexion, $query);
        $row = mysqli_fetch_assoc($result);
        $turnos_ocupados = intval($row['total']);
        
        // Si hay slots disponibles
        if ($turnos_ocupados < $pacientes_por_turno) {
            $slots[] = [
                'hora' => $hora_slot,
                'disponibles' => $pacientes_por_turno - $turnos_ocupados
            ];
        }
        
        // Avanzar al siguiente slot
        $current->add($intervalo);
    }
    
    return $slots;
}

// Función para crear un nuevo turno
function handleCrearTurno($conexion) {
    global $response;
    
    try {
        // Solo aceptar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception("Método no permitido");
        }
        
        // Obtener datos del cuerpo de la solicitud
        $json_str = file_get_contents("php://input");
        $data = json_decode($json_str, true);
        
        if (!$data) {
            $json_error = json_last_error_msg();
            error_log("Error de JSON: " . $json_error . " - Input recibido: " . $json_str);
            throw new Exception("Datos inválidos: " . $json_error);
        }
        
        // Validar campos requeridos
        $campos_requeridos = ['nombre', 'dni', 'telefono', 'fecha', 'hora', 'profesional'];
        
        foreach ($campos_requeridos as $campo) {
            if (!isset($data[$campo]) || empty($data[$campo])) {
                throw new Exception("El campo '$campo' es obligatorio");
            }
        }
        
        // Sanitizar datos
        $nombre = mysqli_real_escape_string($conexion, $data['nombre']);
        $dni = mysqli_real_escape_string($conexion, $data['dni']);
        $telefono = mysqli_real_escape_string($conexion, $data['telefono']);
        $fecha = mysqli_real_escape_string($conexion, $data['fecha']);
        $hora = mysqli_real_escape_string($conexion, $data['hora']);
        $profesional = mysqli_real_escape_string($conexion, $data['profesional']);
        $apiKey = isset($data['apiKey']) ? mysqli_real_escape_string($conexion, $data['apiKey']) : '';
        
        // Validar API Key si está configurada
        // Aquí puedes agregar tu lógica de validación de API Key
        
        // Verificar si el profesional existe
        $query_prof = "SELECT idusuario, nombre FROM usuario WHERE nombre = '$profesional' AND rol = 2";
        $result_prof = mysqli_query($conexion, $query_prof);
        
        if (mysqli_num_rows($result_prof) == 0) {
            throw new Exception("El profesional especificado no existe");
        }
        
        $profesional_info = mysqli_fetch_assoc($result_prof);
        
        // Verificar si ya existe un turno en esa fecha y hora para ese profesional
        $query_check = "SELECT COUNT(*) as total FROM turnos 
                      WHERE fecha = '$fecha' 
                      AND hora = '$hora' 
                      AND usuario_carga = '$profesional'";
        
        $result_check = mysqli_query($conexion, $query_check);
        $row_check = mysqli_fetch_assoc($result_check);
        $turnos_existentes = intval($row_check['total']);
        
        // Verificar cuántos pacientes puede atender en ese horario
        $dia_semana = date('w', strtotime($fecha));
        
        $query_capacidad = "SELECT pacientes_por_turno FROM horarios_profesionales 
                          WHERE id_profesional = {$profesional_info['idusuario']}
                          AND dia_semana = $dia_semana
                          AND TIME('$hora') >= hora_inicio
                          AND TIME('$hora') < hora_fin
                          AND activo = 1";
        
        $result_capacidad = mysqli_query($conexion, $query_capacidad);
        
        if (mysqli_num_rows($result_capacidad) > 0) {
            $row_capacidad = mysqli_fetch_assoc($result_capacidad);
            $capacidad = intval($row_capacidad['pacientes_por_turno']);
        } else {
            // Usar capacidad por defecto
            $query_default = "SELECT pacientes_por_turno_default FROM configuracion_sistema LIMIT 1";
            $result_default = mysqli_query($conexion, $query_default);
            
            if (mysqli_num_rows($result_default) > 0) {
                $row_default = mysqli_fetch_assoc($result_default);
                $capacidad = intval($row_default['pacientes_por_turno_default']);
            } else {
                $capacidad = 1; // Valor por defecto si no hay configuración
            }
        }
        
        // Verificar si hay capacidad disponible
        if ($turnos_existentes >= $capacidad) {
            throw new Exception("No hay turnos disponibles para ese horario");
        }
        
        // Determinar el origen del turno (API/bot)
        $origen = isset($data['origen']) ? mysqli_real_escape_string($conexion, $data['origen']) : 'API';
        
        // Usar observaciones personalizadas si se proporcionan, o un texto por defecto si no
        if (isset($data['observaciones']) && !empty($data['observaciones'])) {
            $observaciones = mysqli_real_escape_string($conexion, $data['observaciones']);
        } else {
            $observaciones = "Turno creado vía $origen";
        }
        
        // Insertar el turno con estado 0 (pendiente de aprobación)
        // Los turnos creados via API siempre quedan como pendientes
        $query_insert = "INSERT INTO turnos (nombre, dni, telefono, fecha, hora, estado, observaciones, usuario_carga) 
                       VALUES ('$nombre', '$dni', '$telefono', '$fecha', '$hora', 0, '$observaciones', '$profesional')";
        
        if (mysqli_query($conexion, $query_insert)) {
            $id_turno = mysqli_insert_id($conexion);
            
            http_response_code(201); // Created
            $response = [
                'status' => 'success',
                'message' => 'Turno creado correctamente y enviado para aprobación del profesional',
                'data' => [
                    'id_turno' => $id_turno,
                    'nombre' => $nombre,
                    'profesional' => $profesional,
                    'fecha' => $fecha,
                    'hora' => $hora,
                    'estado' => 'Pendiente de aprobación'
                ]
            ];
        } else {
            throw new Exception("Error al crear el turno: " . mysqli_error($conexion));
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        $response = [
            'status' => 'error',
            'message' => $e->getMessage(),
            'data' => null
        ];
    }
}

// Función para obtener lista de profesionales
function handleGetProfesionales($conexion) {
    global $response;
    
    try {
        // Obtener todos los profesionales activos
        // Modificar la consulta para ser más inclusiva (eliminar el filtro estado=1 si es problemático)
        $query = "SELECT idusuario, nombre FROM usuario WHERE rol = 2 ORDER BY nombre ASC";
        
        // Ejecutar consulta y verificar errores
        $result = mysqli_query($conexion, $query);
        
        if (!$result) {
            // Error en la consulta SQL
            error_log("Error en consulta SQL: " . mysqli_error($conexion));
            throw new Exception("Error al consultar profesionales: " . mysqli_error($conexion));
        }
        
        $profesionales = [];
        
        // Procesar resultados solo si hay datos
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $profesionales[] = [
                    'id' => $row['idusuario'],
                    'nombre' => $row['nombre']
                ];
            }
        }
        
        http_response_code(200);
        $response = [
            'status' => 'success',
            'message' => 'Lista de profesionales',
            'data' => $profesionales
        ];
        
    } catch (Exception $e) {
        http_response_code(400);
        $response = [
            'status' => 'error',
            'message' => $e->getMessage(),
            'data' => null
        ];
    }
}

// Devolver respuesta en formato JSON
echo json_encode($response);
