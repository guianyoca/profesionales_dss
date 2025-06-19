<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$nombre_user = $_SESSION['nombre'];
$rol_user = $_SESSION['rol'];

// Verificar que sea un profesional (rol=2)
if ($rol_user != 2) {
    header("Location: index.php");
    exit;
}

$alert = '';

// Procesar aprobación o rechazo de turno
if (isset($_POST['accion']) && isset($_POST['id_turno'])) {
    $id_turno = intval($_POST['id_turno']);
    $accion = $_POST['accion'];
    
    if ($accion == 'aprobar') {
        // Aprobar turno - Cambiar estado a 3 (Aprobado)
        $query = mysqli_query($conexion, "UPDATE turnos SET estado = 3, 
                                        observaciones = CONCAT(IFNULL(observaciones,''), ' | Aprobado por profesional el " . date('Y-m-d H:i:s') . "') 
                                        WHERE idturno = $id_turno AND usuario_carga = '$nombre_user'");
        
        if ($query) {
            $alert = '<div class="alert alert-success" role="alert">Turno aprobado correctamente</div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error al aprobar el turno: ' . mysqli_error($conexion) . '</div>';
        }
    } elseif ($accion == 'rechazar' && isset($_POST['observacion'])) {
        // Rechazar turno - Cambiar estado a 4 (Rechazado) y guardar observación
        $observacion = mysqli_real_escape_string($conexion, $_POST['observacion']);
        $query = mysqli_query($conexion, "UPDATE turnos SET estado = 4, 
                                        observaciones = CONCAT(IFNULL(observaciones,''), ' | Rechazado: $observacion (" . date('Y-m-d H:i:s') . ")') 
                                        WHERE idturno = $id_turno AND usuario_carga = '$nombre_user'");
        
        if ($query) {
            $alert = '<div class="alert alert-success" role="alert">Turno rechazado correctamente</div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error al rechazar el turno: ' . mysqli_error($conexion) . '</div>';
        }
    }
}

include_once "includes/header.php";
?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <h3>Turnos Pendientes de Aprobación</h3>
                <?php echo $alert; ?>
                
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Paciente</th>
                                <th>DNI</th>
                                <th>Teléfono</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Observaciones</th>
                                <th>Profesional</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Obtener turnos pendientes de aprobación (estado=0) para este profesional
                            // NOTA: Ahora el usuario_carga indica el profesional PARA quien es el turno, no quien lo creó
                            $query = mysqli_query($conexion, "SELECT t.idturno, t.nombre, t.dni, t.telefono, t.fecha, t.hora, t.observaciones, 
                                     (SELECT nombre FROM usuario WHERE usuario = t.usuario_carga) as profesional 
                                     FROM turnos t 
                                     WHERE t.estado = 0 AND t.usuario_carga = '$nombre_user' 
                                     ORDER BY t.fecha ASC, t.hora ASC");
                            
                            if (mysqli_num_rows($query) > 0) {
                                while ($dato = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $dato['idturno']; ?></td>
                                        <td><?php echo $dato['nombre']; ?></td>
                                        <td><?php echo $dato['dni']; ?></td>
                                        <td><?php echo $dato['telefono']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($dato['fecha'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($dato['hora'])); ?></td>
                                        <td><?php echo $dato['observaciones']; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="aprobarTurno(<?php echo $dato['idturno']; ?>)">
                                                Aprobar
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="mostrarModalRechazo(<?php echo $dato['idturno']; ?>)">
                                                Rechazar
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="8" class="text-center">No hay turnos pendientes de aprobación</td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para rechazo de turno -->
<div class="modal fade" id="modalRechazo" tabindex="-1" role="dialog" aria-labelledby="modalRechazoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRechazoLabel">Rechazar Turno</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="rechazar">
                    <input type="hidden" name="id_turno" id="id_turno_rechazar">
                    
                    <div class="form-group">
                        <label for="observacion">Motivo del rechazo:</label>
                        <textarea name="observacion" id="observacion" rows="3" class="form-control" required></textarea>
                        <small class="text-muted">Por favor, indique el motivo por el cual rechaza este turno.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Rechazo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulario para aprobación -->
<form id="formAprobar" method="post" style="display: none;">
    <input type="hidden" name="accion" value="aprobar">
    <input type="hidden" name="id_turno" id="id_turno_aprobar">
</form>

<script>
    function mostrarModalRechazo(idTurno) {
        document.getElementById('id_turno_rechazar').value = idTurno;
        $('#modalRechazo').modal('show');
    }
    
    function aprobarTurno(idTurno) {
        if (confirm('¿Está seguro de que desea aprobar este turno?')) {
            document.getElementById('id_turno_aprobar').value = idTurno;
            document.getElementById('formAprobar').submit();
        }
    }
</script>

<?php include_once "includes/footer.php"; ?>
