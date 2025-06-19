<?php
session_start();
include "../conexion.php";

// Verificar que el usuario tiene rol de administrador (rol=1)
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header("Location: index.php");
    exit;
}

$alert = '';

// Si se envía el formulario, actualizar la configuración
if(isset($_POST['actualizar_config'])) {
    $duracion_turno = mysqli_real_escape_string($conexion, $_POST['duracion_turno']);
    $pacientes_por_turno = mysqli_real_escape_string($conexion, $_POST['pacientes_por_turno']);
    $hora_inicio = mysqli_real_escape_string($conexion, $_POST['hora_inicio']);
    $hora_fin = mysqli_real_escape_string($conexion, $_POST['hora_fin']);
    
    // Convertir array de días a string
    $dias_laborables = isset($_POST['dias_laborables']) ? implode(',', $_POST['dias_laborables']) : '1,2,3,4,5';
    
    // Verificar si ya existe una configuración
    $query_check = mysqli_query($conexion, "SELECT id FROM configuracion_sistema LIMIT 1");
    
    if(mysqli_num_rows($query_check) > 0) {
        $row = mysqli_fetch_assoc($query_check);
        $id_config = $row['id'];
        
        // Actualizar configuración existente
        $query_update = mysqli_query($conexion, "UPDATE configuracion_sistema SET 
            duracion_turno_default = '$duracion_turno',
            pacientes_por_turno_default = '$pacientes_por_turno',
            hora_inicio_default = '$hora_inicio',
            hora_fin_default = '$hora_fin',
            dias_laborables = '$dias_laborables'
            WHERE id = '$id_config'");
            
        if($query_update) {
            $alert = '<div class="alert alert-success" role="alert">
                        Configuración actualizada correctamente
                      </div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">
                        Error al actualizar configuración: ' . mysqli_error($conexion) . '
                      </div>';
        }
    } else {
        // Insertar nueva configuración
        $query_insert = mysqli_query($conexion, "INSERT INTO configuracion_sistema(
            duracion_turno_default, pacientes_por_turno_default, hora_inicio_default, hora_fin_default, dias_laborables)
            VALUES ('$duracion_turno', '$pacientes_por_turno', '$hora_inicio', '$hora_fin', '$dias_laborables')");
            
        if($query_insert) {
            $alert = '<div class="alert alert-success" role="alert">
                        Configuración guardada correctamente
                      </div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">
                        Error al guardar configuración: ' . mysqli_error($conexion) . '
                      </div>';
        }
    }
}

// Obtener configuración actual
$config = array(
    'duracion_turno_default' => 30,
    'pacientes_por_turno_default' => 1,
    'hora_inicio_default' => '08:00:00',
    'hora_fin_default' => '18:00:00',
    'dias_laborables' => '1,2,3,4,5'
);

$query_config = mysqli_query($conexion, "SELECT * FROM configuracion_sistema LIMIT 1");
if(mysqli_num_rows($query_config) > 0) {
    $config = mysqli_fetch_assoc($query_config);
}

// Array de días de la semana para mostrar en el formulario
$dias_semana = array(
    0 => 'Domingo',
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado'
);

// Convertir string de días a array
$dias_seleccionados = explode(',', $config['dias_laborables']);

include_once "includes/header.php";
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Configuración del Sistema</h4>
                    <div>
                        <a href="configuracion_horarios.php" class="btn btn-info">Configurar Horarios por Profesional</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php echo $alert; ?>
                
                <form method="post" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Duración de Turno Predeterminada (minutos):</label>
                                <input type="number" name="duracion_turno" class="form-control" value="<?php echo $config['duracion_turno_default']; ?>" required min="5" max="120">
                                <small class="text-muted">Duración estándar de un turno en minutos</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pacientes por Turno Predeterminado:</label>
                                <input type="number" name="pacientes_por_turno" class="form-control" value="<?php echo $config['pacientes_por_turno_default']; ?>" required min="1" max="10">
                                <small class="text-muted">Cantidad de pacientes que puede atender un profesional en un mismo horario</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hora de Inicio Predeterminada:</label>
                                <input type="time" name="hora_inicio" class="form-control" value="<?php echo $config['hora_inicio_default']; ?>" required>
                                <small class="text-muted">Hora a la que comienza la jornada laboral</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hora de Fin Predeterminada:</label>
                                <input type="time" name="hora_fin" class="form-control" value="<?php echo $config['hora_fin_default']; ?>" required>
                                <small class="text-muted">Hora a la que finaliza la jornada laboral</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Días Laborables Predeterminados:</label>
                                <div class="d-flex flex-wrap">
                                    <?php foreach($dias_semana as $num => $dia): ?>
                                    <div class="form-check mr-4">
                                        <input class="form-check-input" type="checkbox" name="dias_laborables[]" value="<?php echo $num; ?>" id="dia_<?php echo $num; ?>" <?php echo in_array($num, $dias_seleccionados) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="dia_<?php echo $num; ?>">
                                            <?php echo $dia; ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <small class="text-muted">Días de la semana en que se atiende por defecto</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" name="actualizar_config" class="btn btn-primary">Guardar Configuración</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
