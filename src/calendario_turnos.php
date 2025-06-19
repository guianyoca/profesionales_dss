<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$nombre_user = $_SESSION['nombre'];
$rol_user = $_SESSION['rol'];

include_once "includes/header.php";
?>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-12">
                <h3>Calendario de Turnos</h3>
                <div class="btn-group mb-3">
                    <a href="todos_turnos.php" class="btn btn-secondary">Vista de Lista</a>
                    <a href="calendario_turnos.php" class="btn btn-primary">Vista de Calendario</a>
                </div>
                
                <?php if($rol_user == 3) { // Si es secretario, mostrar filtro de profesionales ?>
                <div class="mb-3">
                   <form id="profesionalForm" class="form-inline">
                      <div class="form-group mr-3">
                         <label class="mr-2">Filtrar por profesional:</label>
                         <select id="profesional" name="profesional" class="form-control" onchange="filtrarProfesional()">
                            <option value="">Todos los profesionales</option>
                            <?php
                            // Obtener lista de profesionales (rol=2)
                            $query_prof = mysqli_query($conexion, "SELECT idusuario, nombre FROM usuario WHERE rol = 2 ORDER BY nombre ASC");
                            while ($prof = mysqli_fetch_assoc($query_prof)) {
                               echo '<option value="'.$prof['nombre'].'">'.$prof['nombre'].'</option>';
                            }
                            ?>
                         </select>
                      </div>
                   </form>
                </div>
                <?php } ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalle de turno -->
<div class="modal fade" id="modalTurno" tabindex="-1" role="dialog" aria-labelledby="modalTurnoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTurnoLabel">Detalle del Turno</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><strong>Paciente:</strong> <span id="modal-paciente"></span></p>
        <p><strong>DNI:</strong> <span id="modal-dni"></span></p>
        <p><strong>Teléfono:</strong> <span id="modal-telefono"></span></p>
        <p><strong>Fecha:</strong> <span id="modal-fecha"></span></p>
        <p><strong>Hora:</strong> <span id="modal-hora"></span></p>
        <p><strong>Estado:</strong> <span id="modal-estado"></span></p>
        <?php if($rol_user == 3) { ?>
        <p><strong>Profesional:</strong> <span id="modal-profesional"></span></p>
        <?php } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <a id="btn-editar" href="#" class="btn btn-primary">Editar</a>
        <button id="btn-presente" type="button" class="btn btn-success">Marcar Presente</button>
        <button id="btn-ausente" type="button" class="btn btn-danger">Marcar Ausente</button>
      </div>
    </div>
  </div>
</div>

<!-- Añadir FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">

<!-- Scripts necesarios -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales-all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var profesionalSeleccionado = document.getElementById('profesional') ? document.getElementById('profesional').value : '';
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        locale: 'es',
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        events: function(info, successCallback, failureCallback) {
            // Construir URL para obtener eventos según el rol y filtros
            var url = 'api_turnos.php?start=' + info.startStr + '&end=' + info.endStr;
            
            if (profesionalSeleccionado) {
                url += '&profesional=' + encodeURIComponent(profesionalSeleccionado);
            }
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Error al cargar los turnos:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            // Mostrar modal con detalles del evento
            var event = info.event;
            var extendedProps = event.extendedProps;
            
            document.getElementById('modal-paciente').textContent = event.title;
            document.getElementById('modal-dni').textContent = extendedProps.dni;
            document.getElementById('modal-telefono').textContent = extendedProps.telefono;
            document.getElementById('modal-fecha').textContent = extendedProps.fecha;
            document.getElementById('modal-hora').textContent = extendedProps.hora;
            
            var estadoTexto = '';
            var estadoClass = '';
            
            switch(extendedProps.estado) {
                case '0':
                    estadoTexto = 'ASIGNADO';
                    estadoClass = 'text-primary';
                    document.getElementById('btn-editar').style.display = 'inline-block';
                    document.getElementById('btn-presente').style.display = 'inline-block';
                    document.getElementById('btn-ausente').style.display = 'inline-block';
                    break;
                case '1':
                    estadoTexto = 'PRESENTE';
                    estadoClass = 'text-success';
                    document.getElementById('btn-editar').style.display = 'none';
                    document.getElementById('btn-presente').style.display = 'none';
                    document.getElementById('btn-ausente').style.display = 'none';
                    break;
                case '2':
                    estadoTexto = 'AUSENTE';
                    estadoClass = 'text-danger';
                    document.getElementById('btn-editar').style.display = 'none';
                    document.getElementById('btn-presente').style.display = 'none';
                    document.getElementById('btn-ausente').style.display = 'none';
                    break;
            }
            
            document.getElementById('modal-estado').textContent = estadoTexto;
            document.getElementById('modal-estado').className = estadoClass;
            
            <?php if($rol_user == 3) { ?>
            document.getElementById('modal-profesional').textContent = extendedProps.profesional;
            <?php } ?>
            
            // Configurar botones de acción
            document.getElementById('btn-editar').href = 'editar_turno.php?id_turno=' + extendedProps.idturno;
            
            document.getElementById('btn-presente').onclick = function() {
                window.location.href = 'dar_presente.php?id_turno=' + extendedProps.idturno;
            };
            
            document.getElementById('btn-ausente').onclick = function() {
                window.location.href = 'dar_ausente.php?id_turno=' + extendedProps.idturno;
            };
            
            $('#modalTurno').modal('show');
        },
        dateClick: function(info) {
            // Redirigir a la página de creación de turno con la fecha preseleccionada
            window.location.href = 'turnos.php?fecha=' + info.dateStr;
        }
    });
    
    calendar.render();
    
    // Función para filtrar por profesional
    window.filtrarProfesional = function() {
        profesionalSeleccionado = document.getElementById('profesional').value;
        calendar.refetchEvents();
    };
});
</script>

<?php include_once "includes/footer.php"; ?>
