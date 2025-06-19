<?php
/* ==========================================
 * FORMULARIO DE TURNOS - Sistema de Profesionales DSS
 * ==========================================
 * Este archivo contiene el formulario para la carga de turnos
 */

session_start();
require_once "../conexion.php";

include_once "includes/header.php";

// Inicializar mensaje de alerta si existe
$alert = isset($_SESSION['alert']) ? $_SESSION['alert'] : '';
unset($_SESSION['alert']); // Limpiar después de mostrar
?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">Cargar Turno</h4>
                <div class="btn-group mt-2">
                    <a href="turnos.php" class="btn btn-sm btn-light">Formulario</a>
                    <a href="calendario_turnos.php" class="btn btn-sm btn-light">Calendario</a>
                </div>
            </div>
            
            <div class="card-body">
                <?php if (!empty($alert)): ?>
                <div class="alert alert-info"><?php echo $alert; ?></div>
                <?php endif; ?>
                
                <form action="cargar_turno.php" method="post" class="p-3" autocomplete="off" id="turnoForm">
                    <!-- Datos del paciente -->
                    <div class="form-group">
                        <label for="nombre">Nombre completo del paciente:</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" maxlength="100" autocomplete="off" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="dni">Nº DNI:</label>
                        <input type="text" id="dni" name="dni" class="form-control" maxlength="15" autocomplete="off" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono">Nº Teléfono:</label>
                        <input type="text" id="telefono" name="telefono" class="form-control" maxlength="20" 
                               placeholder="Solo números" autocomplete="off" required
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 20);">
                        <small class="form-text text-muted">Máximo 20 caracteres numéricos</small>
                    </div>
                    
                    <!-- Selección de profesional (solo para secretarios) -->
                    <?php if ($_SESSION['rol'] == 3): // Solo para secretarios ?>
                    <div class="form-group">
                        <label for="profesional">Profesional:</label>
                        <select id="profesional" name="profesional" class="form-control" required>
                            <option value="">Seleccione un profesional</option>
                            <?php
                            // Obtener lista de profesionales (rol=2)
                            $query_prof = mysqli_query($conexion, "SELECT idusuario, nombre FROM usuario WHERE rol = 2 ORDER BY nombre ASC");
                            while ($prof = mysqli_fetch_assoc($query_prof)) {
                                // Usar el nombre del profesional como valor
                                echo '<option value="'.$prof['nombre'].'">'.$prof['nombre'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Datos del turno -->
                    <div id="date-time-container">
                        <h5 class="mt-3">Datos de los turnos</h5>
                        <hr>
                        
                        <div class="date-time-group" id="turno-0">
                            <div class="form-group">
                                <label>Fecha:</label>
                                <input type="date" name="fecha[]" class="form-control" 
                                       autocomplete="off" required 
                                       value="<?php echo isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Hora:</label>
                                <input type="time" name="hora[]" class="form-control" 
                                       autocomplete="off" required 
                                       value="<?php echo isset($_GET['hora']) ? htmlspecialchars($_GET['hora']) : ''; ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botón para agregar más turnos -->
                    <div class="text-center mb-3">
                        <button type="button" id="btnAddTurno" class="btn btn-info btn-sm">
                            <i class="fas fa-plus-circle"></i> Agregar otro turno
                        </button>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-block">Cargar Turnos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validación del formulario antes de envío
document.getElementById('turnoForm').addEventListener('submit', function(e) {
    // Validar teléfono (solo números)
    const telefono = document.getElementById('telefono').value;
    if (!/^[0-9]+$/.test(telefono)) {
        e.preventDefault();
        alert('El número de teléfono solo debe contener dígitos numéricos.');
        return false;
    }
    
    // Validar DNI (solo números)
    const dni = document.getElementById('dni').value;
    if (!/^[0-9]+$/.test(dni)) {
        e.preventDefault();
        alert('El DNI solo debe contener dígitos numéricos.');
        return false;
    }
    
    return true;
});

// Contador para llevar un registro de cuántos grupos de fecha/hora hemos añadido
let turnoCounter = 0;

// Función para agregar un nuevo grupo de fecha/hora
document.getElementById('btnAddTurno').addEventListener('click', function() {
    turnoCounter++;
    
    // Crear un nuevo grupo de fecha/hora
    const newGroup = document.createElement('div');
    newGroup.className = 'date-time-group';
    newGroup.id = 'turno-' + turnoCounter;
    
    // Establecer el HTML del nuevo grupo con campos de fecha y hora
    newGroup.innerHTML = `
        <hr>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Turno adicional</h6>
            <button type="button" class="btn btn-danger btn-sm remove-turno" data-turno="${turnoCounter}">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </div>
        <div class="form-group">
            <label>Fecha:</label>
            <input type="date" name="fecha[]" class="form-control" autocomplete="off" required value="${new Date().toISOString().split('T')[0]}">
        </div>
        <div class="form-group">
            <label>Hora:</label>
            <input type="time" name="hora[]" class="form-control" autocomplete="off" required>
        </div>
    `;
    
    // Agregar el nuevo grupo al contenedor
    document.getElementById('date-time-container').appendChild(newGroup);
    
    // Agregar evento para el botón de eliminar
    newGroup.querySelector('.remove-turno').addEventListener('click', function() {
        const turnoId = this.getAttribute('data-turno');
        document.getElementById('turno-' + turnoId).remove();
    });
});
</script>

<?php include_once "includes/footer.php"; ?>