<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$nombre_user = $_SESSION['nombre'];
$rol_user = $_SESSION['rol'];
$hoy=date('Y-m-d');

$alert = '';

// Procesar cambio de estado
if (isset($_POST['accion']) && isset($_POST['id_turno'])) {
    $id_turno = intval($_POST['id_turno']);
    $accion = $_POST['accion'];
    
    if ($accion == 'cambiar_estado' && isset($_POST['estado'])) {
        $estado = intval($_POST['estado']);
        
        // Verificar que el estado sea válido (1=Presente, 2=Ausente)
        if ($estado == 1 || $estado == 2) {
            $query = mysqli_query($conexion, "UPDATE turnos SET estado = $estado, 
                                          observaciones = CONCAT(IFNULL(observaciones,''), ' | Marcado como " . ($estado == 1 ? "Presente" : "Ausente") . " el " . date('Y-m-d H:i:s') . "') 
                                          WHERE idturno = $id_turno");
            
            if ($query) {
                $alert = '<div class="alert alert-success" role="alert">Estado del turno actualizado correctamente</div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">Error al actualizar el estado del turno: ' . mysqli_error($conexion) . '</div>';
            }
        }
    } elseif ($accion == 'cancelar' && isset($_POST['observacion'])) {
        // Cancelar turno - Cambiar estado a 6 (Cancelado) y guardar observación
        $observacion = mysqli_real_escape_string($conexion, $_POST['observacion']);
        $query = mysqli_query($conexion, "UPDATE turnos SET estado = 6, 
                                        observaciones = CONCAT(IFNULL(observaciones,''), ' | Cancelado: $observacion (" . date('Y-m-d H:i:s') . ")') 
                                        WHERE idturno = $id_turno");
        
        if ($query) {
            $alert = '<div class="alert alert-success" role="alert">Turno cancelado correctamente</div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error al cancelar el turno: ' . mysqli_error($conexion) . '</div>';
        }
    }
}

include_once "includes/header.php";
?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
               <h3>Turnos</h3>
               <div class="btn-group mb-3">
                   <a href="todos_turnos.php" class="btn btn-primary">Vista de Lista</a>
                   <a href="calendario_turnos.php" class="btn btn-secondary">Vista de Calendario</a>
               </div>
               <?php if($rol_user == 3) { // Si es secretario, mostrar filtro de profesionales ?>
               <div class="mb-4">
                  <form method="GET" class="form-inline">
                     <div class="form-group mr-3">
                        <label class="mr-2">Filtrar por profesional:</label>
                        <select name="profesional" class="form-control" onchange="this.form.submit()">
                           <option value="">Todos los profesionales</option>
                           <?php
                           // Obtener lista de profesionales (rol=2)
                           $query_prof = mysqli_query($conexion, "SELECT idusuario, nombre FROM usuario WHERE rol = 2 ORDER BY nombre ASC");
                           while ($prof = mysqli_fetch_assoc($query_prof)) {
                              $selected = (isset($_GET['profesional']) && $_GET['profesional'] == $prof['nombre']) ? 'selected' : '';
                              echo '<option value="'.$prof['nombre'].'" '.$selected.'>'.$prof['nombre'].'</option>';
                           }
                           ?>
                        </select>
                     </div>
                  </form>
               </div>
               <?php } ?>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="tbl">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nombre</th>
                                <th>DNI</th>
                                <th>Telefono</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                                <?php if($rol_user == 3) { ?><th>Profesional</th><?php } ?>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "../conexion.php";
                            
                            // Obtener el primer y último día del mes actual
                            $primer_dia_mes = date('Y-m-01'); // Primer día del mes actual
                            $ultimo_dia_mes = date('Y-m-t'); // Último día del mes actual
                            
                            // Construir la consulta según el rol y los filtros
                            if ($rol_user == 3) { // Si es secretario
                                // Incluir todos los estados relevantes: presente(1), ausente(2), asignado(5) y cancelado(6)
                                $where = "fecha BETWEEN '$primer_dia_mes' AND '$ultimo_dia_mes' AND estado IN (1, 2, 5, 6)"; 
                                
                                // Si hay un filtro de profesional, aplicarlo
                                if (isset($_GET['profesional']) && !empty($_GET['profesional'])) {
                                    $profesional_filtro = $_GET['profesional'];
                                    $where .= " AND usuario_carga='$profesional_filtro'";
                                }
                                
                                $query = mysqli_query($conexion, "SELECT * FROM turnos WHERE $where ORDER BY fecha DESC, hora ASC");
                            } else { // Para usuarios normales (rol=2)
                                $query = mysqli_query($conexion, "SELECT * FROM turnos WHERE (usuario_carga='$id_user' OR usuario_carga='$nombre_user') AND fecha BETWEEN '$primer_dia_mes' AND '$ultimo_dia_mes' AND estado IN (1, 2, 5, 6)");
                            }
                            
                            $result = mysqli_num_rows($query);
                            if ($result > 0) {
                                while ($data = mysqli_fetch_assoc($query)) { ?>
                                    <tr>
                                        <td><?php echo $data['nombre']; ?></td>
                                        <td><?php echo $data['dni']; ?></td>
                                        <td><?php echo $data['telefono']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($data['fecha'])); ?></td>
                                        <td><?php echo $data['hora']; ?></td>
                                        <td>
                                        <?php
                                // Definir clase y texto para el estado
                                switch ($data['estado']) {
                                    case 0:
                                        $estado_clase = 'badge-warning';
                                        $estado_texto = 'Pendiente';
                                        break;
                                    case 1:
                                        $estado_clase = 'badge-success';
                                        $estado_texto = 'Presente';
                                        break;
                                    case 2:
                                        $estado_clase = 'badge-danger';
                                        $estado_texto = 'Ausente';
                                        break;
                                    case 3:
                                        $estado_clase = 'badge-primary';
                                        $estado_texto = 'Aprobado';
                                        break;
                                    case 4:
                                        $estado_clase = 'badge-secondary';
                                        $estado_texto = 'Rechazado';
                                        break;
                                    case 5:
                                        $estado_clase = 'badge-info';
                                        $estado_texto = 'Asignado';
                                        break;
                                    case 6:
                                        $estado_clase = 'badge-dark';
                                        $estado_texto = 'Cancelado';
                                        break;
                                    default:
                                        $estado_clase = 'badge-light';
                                        $estado_texto = 'Desconocido';
                                } ?>
                                        <span class="badge <?php echo $estado_clase; ?>"><?php echo $estado_texto; ?></span>
                                        </td>
                                        <td><?php echo $data['observaciones']; ?></td>
                                        <?php if($rol_user == 3) { ?><td><?php echo $data['usuario_carga']; ?></td><?php } ?>
                                        <td>
                                            <!-- Botones de acción según el estado del turno -->
                                            <?php if ($data['estado'] == 0 || $data['estado'] == 3 || $data['estado'] == 5) { ?>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-success" 
                                                            onclick="cambiarEstado(<?php echo $data['idturno']; ?>, 1)">
                                                        <i class="fas fa-check"></i> Presente
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" 
                                                            onclick="cambiarEstado(<?php echo $data['idturno']; ?>, 2)">
                                                        <i class="fas fa-times"></i> Ausente
                                                    </button>
                                                    <button class="btn btn-sm btn-secondary" 
                                                            onclick="mostrarModalCancelacion(<?php echo $data['idturno']; ?>)">
                                                        <i class="fas fa-ban"></i> Cancelar
                                                    </button>
                                                </div>
                                            <?php } ?>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cancelación de turno -->
<div class="modal fade" id="modalCancelacion" tabindex="-1" role="dialog" aria-labelledby="modalCancelacionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCancelacionLabel">Cancelar Turno</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="cancelar">
                    <input type="hidden" name="id_turno" id="id_turno_cancelar">
                    
                    <div class="form-group">
                        <label for="observacion">Motivo de la cancelación:</label>
                        <textarea name="observacion" id="observacion" rows="3" class="form-control" required></textarea>
                        <small class="text-muted">Por favor, indique el motivo por el cual se cancela este turno.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Volver</button>
                    <button type="submit" class="btn btn-danger">Confirmar Cancelación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulario para cambio de estado -->
<form id="formCambiarEstado" method="post" style="display: none;">
    <input type="hidden" name="accion" value="cambiar_estado">
    <input type="hidden" name="id_turno" id="id_turno_cambiar">
    <input type="hidden" name="estado" id="estado_cambiar">
</form>

<script>
    function cambiarEstado(idTurno, estado) {
        if (confirm('¿Está seguro de que desea cambiar el estado de este turno?')) {
            document.getElementById('id_turno_cambiar').value = idTurno;
            document.getElementById('estado_cambiar').value = estado;
            document.getElementById('formCambiarEstado').submit();
        }
    }
    
    function mostrarModalCancelacion(idTurno) {
        document.getElementById('id_turno_cancelar').value = idTurno;
        $('#modalCancelacion').modal('show');
    }
</script>

<?php include_once "includes/footer.php"; ?>