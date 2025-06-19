<?php
/* ==========================================
 * CARGA DE TURNOS - Sistema de Profesionales DSS
 * ==========================================
 * Este script procesa la creación de nuevos turnos
 * desde el formulario de turnos.php
 */

// Iniciar sesión y conexión a la base de datos
session_start();
include "../conexion.php";

// ---- VERIFICACIÓN DE DATOS RECIBIDOS ----
// Verificar si el formulario fue enviado
if (!isset($_POST['nombre']) || !isset($_POST['dni']) || !isset($_POST['telefono']) || !isset($_POST['fecha']) || !isset($_POST['hora'])) {
    die("<script>alert('Error: Faltan datos del formulario'); window.location='turnos.php';</script>");
}

// Verificar si fecha y hora son arrays (múltiples turnos)
$multipleTurnos = is_array($_POST['fecha']) && is_array($_POST['hora']) && count($_POST['fecha']) === count($_POST['hora']);

// Si los arrays no tienen la misma longitud, hay un error en el formulario
if (is_array($_POST['fecha']) && is_array($_POST['hora']) && count($_POST['fecha']) !== count($_POST['hora'])) {
    die("<script>alert('Error: Las fechas y horas no coinciden'); window.location='turnos.php';</script>");
}

// ---- OBTENCIÓN DE DATOS DE SESIÓN ----
$id_user = $_SESSION['idUser'];
$nombre_user = $_SESSION['nombre'];
$rol_user = $_SESSION['rol'];

// ---- PROCESAMIENTO DE DATOS DEL FORMULARIO ----
// Sanitizar y validar datos
$nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
$dni = mysqli_real_escape_string($conexion, trim($_POST['dni']));

// Función para verificar si el DNI está habilitado en la API
function verificarDniEnApi($dni) {
    // URL de la API (ajustar según corresponda para acceso externo)
    $url = "http://192.168.1.250/servicios_sociales/api/afiliado/padron/{$dni}";
    
    // Inicializar cURL
    $ch = curl_init();
    
    // Configurar opciones de cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Timeout de 5 segundos
    
    // Ejecutar la solicitud
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Verificar si hubo error en la solicitud
    if ($httpCode != 200) {
        return ["error" => "Error al consultar el estado del DNI (HTTP $httpCode)", "habilitado" => false];
    }
    
    // Decodificar la respuesta JSON
    $datos = json_decode($response, true);
    
    // Verificar si se pudo decodificar y si hay datos
    if ($datos === null) {
        return ["error" => "Error al procesar la respuesta de la API", "habilitado" => false];
    }
    
    // Verificar si hay datos y si el afiliado está habilitado
    if (empty($datos)) {
        return ["error" => "El DNI no se encuentra registrado en el padrón", "habilitado" => false];
    }
    
    // Verificar si el afiliado está habilitado
    $habilitado = isset($datos[0]['habilitado']) && $datos[0]['habilitado'] == "1";
    
    return [
        "error" => $habilitado ? "" : "El afiliado no está habilitado para recibir atención",
        "habilitado" => $habilitado,
        "datos" => $datos[0]
    ];
}

// Verificar si el DNI está habilitado
$verificacion = verificarDniEnApi($dni);

// Si no está habilitado y la verificación está activa, mostrar error y salir
$verificar_habilitacion = true; // Cambiar a false para deshabilitar temporalmente la verificación

if ($verificar_habilitacion && !$verificacion['habilitado']) {
    die("<script>alert('No se puede agendar el turno: " . $verificacion['error'] . "'); window.location='turnos.php';</script>");
}

// Sanitizar teléfono (solo guardar dígitos y limitado a 20 caracteres según DB)
$telefono = preg_replace('/[^0-9]/', '', $_POST['telefono']);
$telefono = substr($telefono, 0, 20); // Límite de varchar(20) en la DB

// ---- DETERMINACIÓN DE PROFESIONAL PARA EL TURNO ----
$profesional_turno = $nombre_user; // Por defecto, el usuario actual

// Si es secretario (rol 3), usar el profesional seleccionado
if ($rol_user == 3 && isset($_POST['profesional']) && !empty($_POST['profesional'])) {
    $profesional_turno = mysqli_real_escape_string($conexion, trim($_POST['profesional']));
}

// Variables para seguimiento
$errors = [];
$successCount = 0;
$turnosProcesados = 0;
$debug = []; // Para almacenar mensajes de depuración

// Si tenemos múltiples turnos, procesaremos cada uno individualmente
$fechas = $multipleTurnos ? $_POST['fecha'] : array($_POST['fecha']);
$horas = $multipleTurnos ? $_POST['hora'] : array($_POST['hora']);

// Procesar cada turno individualmente
for ($i = 0; $i < count($fechas); $i++) {
    $turnosProcesados++;
    $fecha = mysqli_real_escape_string($conexion, trim($fechas[$i]));
    $hora = mysqli_real_escape_string($conexion, trim($horas[$i]));
    
    $debug[] = "Procesando turno #" . ($i+1) . " - Fecha: $fecha, Hora: $hora";
    
    // ---- VERIFICACIÓN DE TURNOS EXISTENTES ----
    $debug[] = "Verificando si ya existe un turno para: $profesional_turno en $fecha a las $hora";
    $query = mysqli_query($conexion, "SELECT * FROM turnos WHERE usuario_carga='$profesional_turno' AND fecha='$fecha' AND hora='$hora'");

    if (!$query) {
        $errors[] = "Error en consulta de verificación para turno #" . ($i+1) . ": " . mysqli_error($conexion);
        $debug[] = "Error SQL: " . mysqli_error($conexion);
        continue; // Pasar al siguiente turno
    } else {
        $result = mysqli_num_rows($query);
        
        if ($result > 0) {
            $errors[] = "Ya existe un turno asignado para la fecha $fecha a las $hora (turno #" . ($i+1) . ").";
            continue; // Pasar al siguiente turno
        } else {
            $debug[] = "No hay turnos existentes para turno #" . ($i+1) . ", procediendo a verificar disponibilidad según configuración.";
            
            // Determinar el día de la semana (0=domingo, 1=lunes, ..., 6=sábado)
            $dia_semana = date('w', strtotime($fecha));
            
            // Verificar si existe configuración de horarios para este profesional y día
            // Primero buscamos por id o nombre de profesional
            $id_profesional = 0;
            
            // Si el valor de profesional_turno es numérico, asumimos que es un ID
            if (is_numeric($profesional_turno)) {
                $id_profesional = intval($profesional_turno);
            } else {
                // Si no es numérico, buscamos el ID del profesional por su nombre
                $query_prof = mysqli_query($conexion, "SELECT idusuario FROM usuario WHERE nombre = '$profesional_turno' LIMIT 1");
                if ($row_prof = mysqli_fetch_assoc($query_prof)) {
                    $id_profesional = $row_prof['idusuario'];
                }
            }
            
            $debug[] = "ID del profesional identificado: $id_profesional";
            $continuar_insercion = true; // Variable para controlar si continuamos con la inserción
            
            // Si hay configuración de horarios, verificamos que el horario sea válido
            if ($id_profesional > 0) {
                // Verificar si hay configuración de horarios para este profesional y día
                $query_horario = mysqli_query($conexion, "SELECT * FROM horarios_profesionales 
                    WHERE id_profesional = '$id_profesional' 
                    AND dia_semana = '$dia_semana' 
                    AND activo = 1");

                if (mysqli_num_rows($query_horario) > 0) {
                    $debug[] = "Encontradas configuraciones de horario para este profesional y día";
                    $horario_valido = false;
                    $mensaje_error_horario = "";
                    
                    while ($horario = mysqli_fetch_assoc($query_horario)) {
                        $debug[] = "Validando horario: Hora turno=$hora, Inicio=".$horario['hora_inicio'].", Fin=".$horario['hora_fin'];
                        
                        // Normalizar formatos de hora para asegurar comparación correcta
                        $hora_norm = date('H:i:s', strtotime($hora));
                        $hora_inicio_norm = date('H:i:s', strtotime($horario['hora_inicio']));
                        $hora_fin_norm = date('H:i:s', strtotime($horario['hora_fin']));
                        
                        $debug[] = "Horas normalizadas: Turno=$hora_norm, Inicio=$hora_inicio_norm, Fin=$hora_fin_norm";
                        
                        // Verificar si la hora está dentro del rango permitido
                        if ($hora_norm >= $hora_inicio_norm && $hora_norm <= $hora_fin_norm) {
                            $debug[] = "La hora está dentro del rango permitido";
                            
                            // Verificar si la hora corresponde a un intervalo válido según la duración configurada
                            $hora_inicio = strtotime($horario['hora_inicio']);
                            $hora_turno = strtotime($hora);
                            $duracion_minutos = $horario['duracion_turno'];
                            
                            // Calcular si la hora del turno cae en un intervalo válido
                            $minutos_desde_inicio = ($hora_turno - $hora_inicio) / 60;
                            $debug[] = "Minutos desde inicio: $minutos_desde_inicio, Duración: $duracion_minutos, Módulo: " . ($minutos_desde_inicio % $duracion_minutos);
                            
                            if ($minutos_desde_inicio % $duracion_minutos == 0) {
                                $debug[] = "La hora corresponde a un intervalo válido";
                                // Es un horario válido
                                $horario_valido = true;
                                
                                // Verificar si hay espacio disponible (pacientes por turno)
                                $query_ocupacion = mysqli_query($conexion, "SELECT COUNT(*) as ocupados 
                                                                  FROM turnos 
                                                                  WHERE fecha = '$fecha' 
                                                                  AND hora = '$hora' 
                                                                  AND usuario_carga = '$profesional_turno'");
                                $ocupacion = mysqli_fetch_assoc($query_ocupacion);
                                
                                if ($ocupacion['ocupados'] >= $horario['pacientes_por_turno']) {
                                    $horario_valido = false;
                                    $mensaje_error_horario = "Se alcanzó el límite de pacientes para este horario.";
                                }
                                
                                break; // Ya encontramos un horario válido
                            }
                        }
                    }
                    
                    if (!$horario_valido) {
                        if (empty($mensaje_error_horario)) {
                            $errors[] = "Turno #" . ($i+1) . ": La hora seleccionada no corresponde a un horario válido para este profesional.";
                        } else {
                            $errors[] = "Turno #" . ($i+1) . ": " . $mensaje_error_horario;
                        }
                        $continuar_insercion = false;
                    } else {
                        $debug[] = "Turno #" . ($i+1) . ": Horario válido encontrado para el profesional.";
                    }
                } else {
                    $debug[] = "No se encontraron configuraciones de horario para este profesional y día - se permite el turno #" . ($i+1) . ".";
                }
            } else {
                $debug[] = "No se encontró ID del profesional - se permite el turno #" . ($i+1) . " sin validar la configuración";
            }
            
            // Solo continuamos con la inserción si no hay errores para este turno
            if ($continuar_insercion) {
                // Estado 5 = Asignado por defecto
                $estado = 5;
                $observacion = "Turno asignado directamente" . ($multipleTurnos ? " (múltiple #" . ($i+1) . ")" : "");
                
                // ---- INSERCIÓN DEL NUEVO TURNO ----
                // Preparar consulta
                $stmt = mysqli_prepare($conexion, "INSERT INTO turnos(nombre, dni, telefono, fecha, hora, usuario_carga, estado, observaciones) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                
                if ($stmt) {
                    // 'ssssssis' = 8 parámetros: string, string, string, string, string, string, int, string
                    mysqli_stmt_bind_param($stmt, "ssssssis", 
                                        $nombre, 
                                        $dni, 
                                        $telefono, 
                                        $fecha, 
                                        $hora, 
                                        $profesional_turno, 
                                        $estado, 
                                        $observacion);
                    
                    // Ejecutar la consulta
                    $resultado = mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    
                    if ($resultado) {
                        $successCount++;
                        $debug[] = "Turno #" . ($i+1) . ": Insertado correctamente con ID: " . mysqli_insert_id($conexion);
                    } else {
                        $errors[] = "Turno #" . ($i+1) . ": Error al insertar el turno: " . mysqli_error($conexion); 
                        $debug[] = "Turno #" . ($i+1) . ": Error en INSERT: " . mysqli_error($conexion);
                    }
                } else {
                    $errors[] = "Turno #" . ($i+1) . ": Error al preparar la consulta: " . mysqli_error($conexion);
                    $debug[] = "Turno #" . ($i+1) . ": Error preparando consulta: " . mysqli_error($conexion);
                }
            }
        }
    }
}
// ---- MOSTRAR MENSAJES Y REDIRECCIONAR ----
// Mensajes de error
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<script>console.error('$error');</script>";
    }
    
    // Consolidar todos los errores en un solo mensaje de alerta
    $errorMsg = count($errors) > 1 
        ? "Se encontraron " . count($errors) . " errores al procesar los turnos:\n\n" . implode("\n", $errors) 
        : $errors[0];
    echo "<script>alert('$errorMsg');</script>";
}

// Mensaje de éxito
if ($successCount > 0) {
    $mensaje = $turnosProcesados === 1 
        ? "Turno guardado correctamente!" 
        : "$successCount de $turnosProcesados turnos guardados correctamente!";
    
    echo "<script>console.log('$mensaje');</script>";
    echo "<script>alert('$mensaje');</script>";
}

// Para depuración (comentar en producción)
/*
echo "<h3>Información de depuración:</h3>";
echo "<pre>";
echo "POST: ";
print_r($_POST);
echo "\n\nDEBUG: ";
print_r($debug);
echo "</pre>";
*/

// Tiempo normal de redirección
$redirect_timeout = 500;

// Pequeña pausa para que se muestre el mensaje antes de redireccionar
echo "<script>
setTimeout(function() {
    window.location = 'pacientes_dia.php';
}, $redirect_timeout);
</script>";

echo "<p><strong>NOTA:</strong> Esta página se redireccionará automáticamente en $redirect_timeout/1000 segundos.</p>";
?>