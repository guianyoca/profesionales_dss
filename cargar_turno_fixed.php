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

// ---- OBTENCIÓN DE DATOS DE SESIÓN ----
$id_user = $_SESSION['idUser'];
$nombre_user = $_SESSION['nombre'];
$rol_user = $_SESSION['rol'];

// ---- PROCESAMIENTO DE DATOS DEL FORMULARIO ----
// Sanitizar y validar datos
$nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
$dni = mysqli_real_escape_string($conexion, trim($_POST['dni']));

// Sanitizar teléfono (solo guardar dígitos y limitado a 20 caracteres según DB)
$telefono = preg_replace('/[^0-9]/', '', $_POST['telefono']);
$telefono = substr($telefono, 0, 20); // Límite de varchar(20) en la DB

// Fecha y hora
$fecha = mysqli_real_escape_string($conexion, trim($_POST['fecha']));
$hora = mysqli_real_escape_string($conexion, trim($_POST['hora']));

// ---- DETERMINACIÓN DE PROFESIONAL PARA EL TURNO ----
$profesional_turno = $nombre_user; // Por defecto, el usuario actual

// Si es secretario (rol 3), usar el profesional seleccionado
if ($rol_user == 3 && isset($_POST['profesional']) && !empty($_POST['profesional'])) {
    $profesional_turno = mysqli_real_escape_string($conexion, trim($_POST['profesional']));
}

// Variables para seguimiento
$errors = [];
$success = false;
$debug = []; // Para almacenar mensajes de depuración

// ---- VERIFICACIÓN DE TURNOS EXISTENTES ----
$debug[] = "Verificando si ya existe un turno para: $profesional_turno en $fecha a las $hora";
$query = mysqli_query($conexion, "SELECT * FROM turnos WHERE usuario_carga='$profesional_turno' AND fecha='$fecha' AND hora='$hora'");

if (!$query) {
    $errors[] = "Error en consulta de verificación: " . mysqli_error($conexion);
    $debug[] = "Error SQL: " . mysqli_error($conexion);
} else {
    $result = mysqli_num_rows($query);
    
    if ($result > 0) {
        $errors[] = "Ya existe un turno asignado para la fecha $fecha a las $hora.";
    } else {
        $debug[] = "No hay turnos existentes, procediendo a verificar disponibilidad según configuración.";
        
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
                    // Verificar si la hora está dentro del rango permitido
                    if ($hora >= $horario['hora_inicio'] && $hora <= $horario['hora_fin']) {
                        // Verificar si la hora corresponde a un intervalo válido según la duración configurada
                        $hora_inicio = strtotime($horario['hora_inicio']);
                        $hora_turno = strtotime($hora);
                        $duracion_minutos = $horario['duracion_turno'];
                        
                        // Calcular si la hora del turno cae en un intervalo válido
                        $minutos_desde_inicio = ($hora_turno - $hora_inicio) / 60;
                        
                        if ($minutos_desde_inicio % $duracion_minutos == 0) {
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
                        $errors[] = "La hora seleccionada no corresponde a un horario válido para este profesional.";
                    } else {
                        $errors[] = $mensaje_error_horario;
                    }
                    $continuar_insercion = false;
                } else {
                    $debug[] = "Horario válido encontrado para el profesional.";
                }
            } else {
                $debug[] = "No se encontraron configuraciones de horario para este profesional y día - se permite el turno";
            }
        } else {
            $debug[] = "No se encontró ID del profesional - se permite el turno sin validar la configuración";
        }
        
        // Solo continuamos con la inserción si no hay errores
        if ($continuar_insercion && empty($errors)) {
            // Estado 5 = Asignado por defecto
            $estado = 5;
            $observacion = "Turno asignado directamente por el profesional";
            
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
                    $success = true;
                    $debug[] = "Turno insertado correctamente con ID: " . mysqli_insert_id($conexion);
                } else {
                    $errors[] = "Error al insertar el turno: " . mysqli_error($conexion); 
                    $debug[] = "Error en INSERT: " . mysqli_error($conexion);
                }
            } else {
                $errors[] = "Error al preparar la consulta: " . mysqli_error($conexion);
                $debug[] = "Error preparando consulta: " . mysqli_error($conexion);
            }
        }
    }
}

// ---- MOSTRAR MENSAJES Y REDIRECCIONAR ----
// Mensajes de error
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<script>console.error('$error');</script>";
        echo "<script>alert('$error');</script>";
    }
}

// Mensaje de éxito
if ($success) {
    echo "<script>console.log('Turno guardado correctamente!');</script>";
    echo "<script>alert('Turno guardado correctamente!');</script>";
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

// Pequeña pausa para que se muestre el mensaje antes de redireccionar
echo "<script>
setTimeout(function() {
    window.location = 'pacientes_dia.php';
}, 500);
</script>";
?>
