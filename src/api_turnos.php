<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluir archivo de conexión a la base de datos
require_once "../conexion.php";

// Obtener el método de solicitud HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Procesar la solicitud según el método HTTP
switch ($method) {
    case 'GET':
        // Obtener todos los turnos o un turno específico
        getTurnos($conexion);
        break;
    case 'POST':
        // Crear un nuevo turno
        crearTurno($conexion);
        break;
    case 'PUT':
        // Actualizar un turno existente
        actualizarTurno($conexion);
        break;
    case 'DELETE':
        // Eliminar un turno
        eliminarTurno($conexion);
        break;
    default:
        // Método no permitido
        http_response_code(405);
        echo json_encode(array("mensaje" => "Método no permitido"));
        break;
}

// Función para obtener turnos
function getTurnos($conexion) {
    session_start();
    $id_user = isset($_SESSION['idUser']) ? $_SESSION['idUser'] : 0;
    $nombre_user = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';
    $rol_user = isset($_SESSION['rol']) ? $_SESSION['rol'] : 0;
    
    // Verificar si se solicita un turno específico por ID
    if (isset($_GET['id'])) {
        $idturno = mysqli_real_escape_string($conexion, $_GET['id']);
        $query = mysqli_query($conexion, "SELECT * FROM turnos WHERE idturno = '$idturno'");
        
        if (mysqli_num_rows($query) > 0) {
            $turno = mysqli_fetch_assoc($query);
            http_response_code(200);
            echo json_encode($turno);
        } else {
            http_response_code(404);
            echo json_encode(array("mensaje" => "Turno no encontrado"));
        }
    } else {
        // Construir consulta con filtros
        $where = [];
        $params = [];
        
        // Filtrar por rango de fechas para el calendario
        if (isset($_GET['start']) && !empty($_GET['start'])) {
            $start = mysqli_real_escape_string($conexion, $_GET['start']);
            $where[] = "fecha >= '$start'";
        }
        
        if (isset($_GET['end']) && !empty($_GET['end'])) {
            $end = mysqli_real_escape_string($conexion, $_GET['end']);
            $where[] = "fecha <= '$end'";
        }
        
        // Filtrar por profesional si se especifica
        if (isset($_GET['profesional']) && !empty($_GET['profesional'])) {
            $profesional = mysqli_real_escape_string($conexion, $_GET['profesional']);
            $where[] = "usuario_carga = '$profesional'";
        } else {
            // Si no es rol secretario, mostrar solo sus propios turnos
            if ($rol_user != 3) {
                $where[] = "(usuario_carga = '$id_user' OR usuario_carga = '$nombre_user')";
            }
        }
        
        // Construir la cláusula WHERE
        $whereClause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";
        
        // Construir y ejecutar la consulta
        $sql = "SELECT * FROM turnos $whereClause ORDER BY fecha ASC, hora ASC";
        $query = mysqli_query($conexion, $sql);
        
        // Preparar resultados en formato para FullCalendar
        $eventos = array();
        
        while ($row = mysqli_fetch_assoc($query)) {
            // Determinar color según estado
            $color = '';
            switch($row['estado']) {
                case '0': $color = '#3788d8'; break; // Azul para asignado
                case '1': $color = '#28a745'; break; // Verde para presente
                case '2': $color = '#dc3545'; break; // Rojo para ausente
            }
            
            // Formatear fecha y hora para FullCalendar
            $fecha = $row['fecha'];
            $hora = $row['hora'];
            $start = $fecha . 'T' . $hora;
            
            // Crear evento para FullCalendar
            $eventos[] = array(
                'id' => $row['idturno'],
                'title' => $row['nombre'],
                'start' => $start,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => array(
                    'idturno' => $row['idturno'],
                    'dni' => $row['dni'],
                    'telefono' => $row['telefono'],
                    'fecha' => $row['fecha'],
                    'hora' => $row['hora'],
                    'estado' => $row['estado'],
                    'profesional' => $row['usuario_carga']
                )
            );
        }
        
        http_response_code(200);
        echo json_encode($eventos);
    }
}

// Función para crear un nuevo turno
function crearTurno($conexion) {
    // Obtener los datos enviados en la solicitud
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Verificar que se han proporcionado todos los campos requeridos
    if (
        !empty($data['nombre']) &&
        !empty($data['dni']) &&
        !empty($data['telefono']) &&
        !empty($data['fecha']) &&
        !empty($data['hora']) &&
        !empty($data['usuario_carga'])
    ) {
        // Sanitizar los datos de entrada
        $nombre = mysqli_real_escape_string($conexion, $data['nombre']);
        $dni = mysqli_real_escape_string($conexion, $data['dni']);
        $telefono = mysqli_real_escape_string($conexion, $data['telefono']);
        $fecha = mysqli_real_escape_string($conexion, $data['fecha']);
        $hora = mysqli_real_escape_string($conexion, $data['hora']);
        $estado = isset($data['estado']) ? intval($data['estado']) : 5;
        $usuario_carga = mysqli_real_escape_string($conexion, $data['usuario_carga']);
        
        // Crear la consulta SQL para insertar el nuevo turno
        $query = "INSERT INTO turnos (nombre, dni, telefono, fecha, hora, estado, usuario_carga) 
                 VALUES ('$nombre', '$dni', '$telefono', '$fecha', '$hora', $estado, '$usuario_carga')";
        
        if (mysqli_query($conexion, $query)) {
            $idturno = mysqli_insert_id($conexion);
            http_response_code(201);
            echo json_encode(array(
                "mensaje" => "Turno creado con éxito",
                "idturno" => $idturno
            ));
        } else {
            http_response_code(503);
            echo json_encode(array("mensaje" => "No se pudo crear el turno: " . mysqli_error($conexion)));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("mensaje" => "No se puede crear el turno. Datos incompletos"));
    }
}

// Función para actualizar un turno existente
function actualizarTurno($conexion) {
    // Obtener los datos enviados en la solicitud
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Verificar que se ha proporcionado el ID del turno
    if (!empty($data['idturno'])) {
        $idturno = mysqli_real_escape_string($conexion, $data['idturno']);
        
        // Iniciar la construcción de la consulta de actualización
        $query = "UPDATE turnos SET ";
        $updateFields = array();
        
        // Agregar los campos a actualizar si se proporcionan
        if (isset($data['nombre'])) {
            $nombre = mysqli_real_escape_string($conexion, $data['nombre']);
            $updateFields[] = "nombre = '$nombre'";
        }
        
        if (isset($data['dni'])) {
            $dni = mysqli_real_escape_string($conexion, $data['dni']);
            $updateFields[] = "dni = '$dni'";
        }
        
        if (isset($data['telefono'])) {
            $telefono = mysqli_real_escape_string($conexion, $data['telefono']);
            $updateFields[] = "telefono = '$telefono'";
        }
        
        if (isset($data['fecha'])) {
            $fecha = mysqli_real_escape_string($conexion, $data['fecha']);
            $updateFields[] = "fecha = '$fecha'";
        }
        
        if (isset($data['hora'])) {
            $hora = mysqli_real_escape_string($conexion, $data['hora']);
            $updateFields[] = "hora = '$hora'";
        }
        
        if (isset($data['estado'])) {
            $estado = intval($data['estado']);
            $updateFields[] = "estado = $estado";
        }
        
        if (isset($data['usuario_carga'])) {
            $usuario_carga = mysqli_real_escape_string($conexion, $data['usuario_carga']);
            $updateFields[] = "usuario_carga = '$usuario_carga'";
        }
        
        // Verificar si hay campos para actualizar
        if (count($updateFields) > 0) {
            $query .= implode(", ", $updateFields);
            $query .= " WHERE idturno = '$idturno'";
            
            if (mysqli_query($conexion, $query)) {
                http_response_code(200);
                echo json_encode(array("mensaje" => "Turno actualizado con éxito"));
            } else {
                http_response_code(503);
                echo json_encode(array("mensaje" => "No se pudo actualizar el turno: " . mysqli_error($conexion)));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("mensaje" => "No hay campos para actualizar"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("mensaje" => "No se puede actualizar el turno. Falta el ID del turno"));
    }
}

// Función para eliminar un turno (ahora marca como cancelado en lugar de eliminar)
function eliminarTurno($conexion) {
    // Obtener los datos enviados en la solicitud
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Verificar si se proporciona un ID de turno en la URL o en el cuerpo
    $idturno = null;
    if (isset($_GET['id'])) {
        $idturno = mysqli_real_escape_string($conexion, $_GET['id']);
    } elseif (!empty($data['idturno'])) {
        $idturno = mysqli_real_escape_string($conexion, $data['idturno']);
    }
    
    if ($idturno) {
        // Verificar si el turno existe
        $checkQuery = mysqli_query($conexion, "SELECT idturno FROM turnos WHERE idturno = '$idturno'");
        
        if (mysqli_num_rows($checkQuery) > 0) {
            // Obtener motivo de cancelación si existe
            $observacion = "Cancelado por API";
            if (!empty($data['observacion'])) {
                $observacion = mysqli_real_escape_string($conexion, $data['observacion']);
            }
            
            // En lugar de eliminar, cambiar estado a 6 (Cancelado)
            $query = "UPDATE turnos SET estado = 6, 
                      observaciones = CONCAT(IFNULL(observaciones,''), ' | Cancelado: $observacion (" . date('Y-m-d H:i:s') . ")') 
                      WHERE idturno = '$idturno'";
            
            if (mysqli_query($conexion, $query)) {
                http_response_code(200);
                echo json_encode(array("mensaje" => "Turno marcado como cancelado correctamente"));
            } else {
                http_response_code(503);
                echo json_encode(array("mensaje" => "No se pudo cancelar el turno: " . mysqli_error($conexion)));
            }
        } else {
            http_response_code(404);
            echo json_encode(array("mensaje" => "Turno no encontrado"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("mensaje" => "No se puede cancelar el turno. Falta el ID del turno"));
    }
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?> 