<?php
session_start();
include "../conexion.php";

// Verificar que el usuario tiene rol de administrador (rol=1)
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header("Location: index.php");
    exit;
}

$alert = '';
$profesional_seleccionado = isset($_GET['profesional']) ? intval($_GET['profesional']) : 0;

// Procesar formulario de agregar/editar horario
if(isset($_POST['guardar_horario'])) {
    $id_horario = isset($_POST['id_horario']) ? intval($_POST['id_horario']) : 0;
    $id_profesional = intval($_POST['id_profesional']);
    $dia_semana = intval($_POST['dia_semana']);
    $hora_inicio = mysqli_real_escape_string($conexion, $_POST['hora_inicio']);
    $hora_fin = mysqli_real_escape_string($conexion, $_POST['hora_fin']);
    $duracion_turno = intval($_POST['duracion_turno']);
    $pacientes_por_turno = intval($_POST['pacientes_por_turno']);
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Validar que la hora de fin sea posterior a la hora de inicio
    if($hora_fin <= $hora_inicio) {
        $alert = '<div class="alert alert-danger" role="alert">
                   La hora de fin debe ser posterior a la hora de inicio
                  </div>';
    } else {
        if($id_horario > 0) {
            // Actualizar horario existente
            $query = mysqli_query($conexion, "UPDATE horarios_profesionales SET 
                dia_semana = '$dia_semana',
                hora_inicio = '$hora_inicio',
                hora_fin = '$hora_fin',
                duracion_turno = '$duracion_turno',
                pacientes_por_turno = '$pacientes_por_turno',
                activo = '$activo'
                WHERE id = '$id_horario' AND id_profesional = '$id_profesional'");
                
            if($query) {
                $alert = '<div class="alert alert-success" role="alert">
                           Horario actualizado correctamente
                          </div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">
                           Error al actualizar horario: ' . mysqli_error($conexion) . '
                          </div>';
            }
        } else {
            // Verificar si ya existe un horario para ese profesional, día y hora
            $query_check = mysqli_query($conexion, "SELECT id FROM horarios_profesionales 
                WHERE id_profesional = '$id_profesional' AND dia_semana = '$dia_semana' 
                AND ((hora_inicio <= '$hora_inicio' AND hora_fin > '$hora_inicio') 
                OR (hora_inicio < '$hora_fin' AND hora_fin >= '$hora_fin')
                OR (hora_inicio >= '$hora_inicio' AND hora_fin <= '$hora_fin'))");
                
            if(mysqli_num_rows($query_check) > 0) {
                $alert = '<div class="alert alert-warning" role="alert">
                           Ya existe un horario configurado para este profesional en ese día y rango de horas
                          </div>';
            } else {
                // Insertar nuevo horario
                $query = mysqli_query($conexion, "INSERT INTO horarios_profesionales(
                    id_profesional, dia_semana, hora_inicio, hora_fin, duracion_turno, pacientes_por_turno, activo)
                    VALUES ('$id_profesional', '$dia_semana', '$hora_inicio', '$hora_fin', 
                    '$duracion_turno', '$pacientes_por_turno', '$activo')");
                    
                if($query) {
                    $alert = '<div class="alert alert-success" role="alert">
                               Horario guardado correctamente
                              </div>';
                } else {
                    $alert = '<div class="alert alert-danger" role="alert">
                               Error al guardar horario: ' . mysqli_error($conexion) . '
                              </div>';
                }
            }
        }
    }
    
    // Redirigir para evitar reenvío de formulario
    header("Location: configuracion_horarios.php?profesional=$id_profesional");
    exit;
}

// Eliminar horario
if(isset($_GET['eliminar']) && isset($_GET['profesional'])) {
    $id_horario = intval($_GET['eliminar']);
    $id_profesional = intval($_GET['profesional']);
    
    $query = mysqli_query($conexion, "DELETE FROM horarios_profesionales 
        WHERE id = '$id_horario' AND id_profesional = '$id_profesional'");
        
    if($query) {
        $alert = '<div class="alert alert-success" role="alert">
                   Horario eliminado correctamente
                  </div>';
    } else {
        $alert = '<div class="alert alert-danger" role="alert">
                   Error al eliminar horario: ' . mysqli_error($conexion) . '
                  </div>';
    }
    
    // Redirigir para evitar recargar y eliminar de nuevo
    header("Location: configuracion_horarios.php?profesional=$id_profesional");
    exit;
}

// Obtener datos de horario para editar
$horario_editar = null;
if(isset($_GET['editar']) && isset($_GET['profesional'])) {
    $id_horario = intval($_GET['editar']);
    $id_profesional = intval($_GET['profesional']);
    
    $query = mysqli_query($conexion, "SELECT * FROM horarios_profesionales 
        WHERE id = '$id_horario' AND id_profesional = '$id_profesional'");
        
    if(mysqli_num_rows($query) > 0) {
        $horario_editar = mysqli_fetch_assoc($query);
        $profesional_seleccionado = $id_profesional;
    }
}

// Obtener configuración predeterminada del sistema
$config_default = array(
    'duracion_turno_default' => 30,
    'pacientes_por_turno_default' => 1,
    'hora_inicio_default' => '08:00:00',
    'hora_fin_default' => '18:00:00',
    'dias_laborables' => '1,2,3,4,5'
);

$query_config = mysqli_query($conexion, "SELECT * FROM configuracion_sistema LIMIT 1");
if(mysqli_num_rows($query_config) > 0) {
    $config_default = mysqli_fetch_assoc($query_config);
}

// Array de días de la semana
$dias_semana = array(
    0 => 'Domingo',
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado'
);

// Obtener lista de profesionales (rol=2)
$profesionales = array();
$query_prof = mysqli_query($conexion, "SELECT idusuario, nombre FROM usuario WHERE rol = 2 ORDER BY nombre ASC");
while($prof = mysqli_fetch_assoc($query_prof)) {
    $profesionales[$prof['idusuario']] = $prof['nombre'];
}

include_once "includes/header.php";
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Configuración de Horarios por Profesional</h4>
                    <div>
                        <a href="configuracion_sistema.php" class="btn btn-info">Configuración General</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php echo $alert; ?>
                
                <!-- Selector de profesional -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form action="" method="get" class="form-inline">
                            <div class="form-group mr-2">
                                <label class="mr-2">Seleccionar Profesional:</label>
                                <select name="profesional" class="form-control" onchange="this.form.submit()">
                                    <option value="">-- Seleccione un profesional --</option>
                                    <?php foreach($profesionales as $id => $nombre): ?>
                                        <option value="<?php echo $id; ?>" <?php echo ($profesional_seleccionado == $id) ? 'selected' : ''; ?>>
                                            <?php echo $nombre; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
                
                <?php if($profesional_seleccionado > 0): ?>
                
                <!-- Formulario para agregar/editar horario -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header card-header-info">
                                <h5 class="card-title">
                                    <?php echo $horario_editar ? 'Editar Horario' : 'Agregar Nuevo Horario'; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="" method="post">
                                    <input type="hidden" name="id_horario" value="<?php echo $horario_editar ? $horario_editar['id'] : 0; ?>">
                                    <input type="hidden" name="id_profesional" value="<?php echo $profesional_seleccionado; ?>">
                                    
                                    <div class="form-group">
                                        <label>Día de la Semana:</label>
                                        <select name="dia_semana" class="form-control" required>
                                            <?php foreach($dias_semana as $num => $dia): ?>
                                                <option value="<?php echo $num; ?>" <?php echo ($horario_editar && $horario_editar['dia_semana'] == $num) ? 'selected' : ''; ?>>
                                                    <?php echo $dia; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Hora de Inicio:</label>
                                        <input type="time" name="hora_inicio" class="form-control" required 
                                            value="<?php echo $horario_editar ? $horario_editar['hora_inicio'] : $config_default['hora_inicio_default']; ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Hora de Fin:</label>
                                        <input type="time" name="hora_fin" class="form-control" required 
                                            value="<?php echo $horario_editar ? $horario_editar['hora_fin'] : $config_default['hora_fin_default']; ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Duración del Turno (minutos):</label>
                                        <input type="number" name="duracion_turno" class="form-control" required min="5" max="120"
                                            value="<?php echo $horario_editar ? $horario_editar['duracion_turno'] : $config_default['duracion_turno_default']; ?>">
                                        <small class="text-muted">Tiempo que dura cada turno</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Pacientes por Turno:</label>
                                        <input type="number" name="pacientes_por_turno" class="form-control" required min="1" max="10"
                                            value="<?php echo $horario_editar ? $horario_editar['pacientes_por_turno'] : $config_default['pacientes_por_turno_default']; ?>">
                                        <small class="text-muted">Cantidad de pacientes que puede atender en un mismo horario</small>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="activo" id="checkActivo" 
                                            <?php echo (!$horario_editar || $horario_editar['activo'] == 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="checkActivo">
                                            Horario Activo
                                        </label>
                                    </div>
                                    
                                    <div class="text-center">
                                        <button type="submit" name="guardar_horario" class="btn btn-primary">
                                            <?php echo $horario_editar ? 'Actualizar Horario' : 'Guardar Horario'; ?>
                                        </button>
                                        
                                        <?php if($horario_editar): ?>
                                            <a href="configuracion_horarios.php?profesional=<?php echo $profesional_seleccionado; ?>" class="btn btn-secondary">
                                                Cancelar
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Tabla de horarios del profesional -->
                        <div class="card">
                            <div class="card-header card-header-info">
                                <h5 class="card-title">Horarios Configurados</h5>
                                <p class="card-category">
                                    Profesional: <?php echo $profesionales[$profesional_seleccionado]; ?>
                                </p>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>Día</th>
                                                <th>Horario</th>
                                                <th>Duración</th>
                                                <th>Pacientes</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query_horarios = mysqli_query($conexion, "SELECT * FROM horarios_profesionales 
                                                WHERE id_profesional = '$profesional_seleccionado' 
                                                ORDER BY dia_semana ASC, hora_inicio ASC");
                                                
                                            if(mysqli_num_rows($query_horarios) > 0):
                                                while($horario = mysqli_fetch_assoc($query_horarios)):
                                            ?>
                                            <tr>
                                                <td><?php echo $dias_semana[$horario['dia_semana']]; ?></td>
                                                <td>
                                                    <?php 
                                                    echo date('H:i', strtotime($horario['hora_inicio'])) . ' a ' . 
                                                         date('H:i', strtotime($horario['hora_fin'])); 
                                                    ?>
                                                </td>
                                                <td><?php echo $horario['duracion_turno']; ?> min</td>
                                                <td><?php echo $horario['pacientes_por_turno']; ?></td>
                                                <td>
                                                    <span class="badge <?php echo $horario['activo'] ? 'badge-success' : 'badge-danger'; ?>">
                                                        <?php echo $horario['activo'] ? 'Activo' : 'Inactivo'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="configuracion_horarios.php?profesional=<?php echo $profesional_seleccionado; ?>&editar=<?php echo $horario['id']; ?>" 
                                                        class="btn btn-sm btn-warning">
                                                        Editar
                                                    </a>
                                                    <a href="configuracion_horarios.php?profesional=<?php echo $profesional_seleccionado; ?>&eliminar=<?php echo $horario['id']; ?>" 
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('¿Está seguro de que desea eliminar este horario?')">
                                                        Eliminar
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php 
                                                endwhile;
                                            else:
                                            ?>
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    No hay horarios configurados para este profesional
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php else: ?>
                
                <div class="alert alert-info">
                    Por favor, seleccione un profesional para configurar sus horarios
                </div>
                
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
