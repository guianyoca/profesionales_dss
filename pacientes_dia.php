<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$nombre_user = $_SESSION['nombre'];
$rol_user = $_SESSION['rol'];
$hoy=date('Y-m-d');


include_once "includes/header.php";
?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
               <h3>Pacientes del dia <?php echo $hoy;?></h3>
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
                                <th>Nº DNI</th>
                                <th>Nº Telefono</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <?php if($rol_user == 3) { ?><th>Profesional</th><?php } ?>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "../conexion.php";
                            
                            // Consulta según el rol del usuario
                            if ($rol_user == 3) { // Si es secretario
                                $where = "fecha='$hoy'"; // Condición base: solo turnos de hoy
                                
                                // Si hay un filtro de profesional, aplicarlo
                                if (isset($_GET['profesional']) && !empty($_GET['profesional'])) {
                                    $profesional_filtro = $_GET['profesional'];
                                    $where .= " AND usuario_carga='$profesional_filtro'";
                                }
                                
                                $query = mysqli_query($conexion, "SELECT * FROM turnos WHERE $where ORDER BY hora ASC");
                            } else { // Para usuarios normales (rol=2)
                                $query = mysqli_query($conexion, "SELECT * FROM turnos WHERE (usuario_carga='$id_user' OR usuario_carga='$nombre_user') AND fecha='$hoy'");
                            }
                            
                            $result = mysqli_num_rows($query);
                            if ($result > 0) {
                                while ($data = mysqli_fetch_assoc($query)) { ?>
                                    <tr>
                                        <td><?php echo $data['nombre']; ?></td>
                                        <td><?php echo $data['dni']; ?></td>
                                        <td><?php echo $data['telefono']; ?></td>
                                        <td><?php echo $data['fecha']; ?></td>
                                        <td><?php echo $data['hora']; ?></td>
                                        <?php if($rol_user == 3) { ?><td><?php echo $data['usuario_carga']; ?></td><?php } ?>
                                        <td>
                                              
                                        <?php  
                                        // Mostrar botones para estados: Pendiente(0), Aprobado(3) y Asignado(5)
                                        if($data['estado']==0 || $data['estado']==3 || $data['estado']==5){?> 
                                            <button  type="button" class="btn btn-success" onclick="alertaPresente(<?php echo $data['idturno']; ?>)">Presente</button>
                                            <button  type="button" class="btn btn-danger" onclick="alertaAusente(<?php echo $data['idturno']; ?>)">Ausente</button>

                                        <?php } 
                                        if($data['estado']==1){
                                            echo "<strong style='color:green;'>PRESENTE</strong>";
                                        } elseif($data['estado']==2){
                                            echo "<strong style='color:red;'>AUSENTE</strong>";
                                        } elseif($data['estado']==3){
                                            echo "<strong style='color:blue;'>APROBADO</strong>";
                                        } elseif($data['estado']==4){
                                            echo "<strong style='color:gray;'>RECHAZADO</strong>";
                                        } elseif($data['estado']==5){
                                            echo "<strong style='color:teal;'>ASIGNADO</strong>";
                                        } elseif($data['estado']==6){
                                            echo "<strong style='color:black;'>CANCELADO</strong>";
                                        }
                                        ?>   
                                            
                                        </td>
                                    </tr>
                                           
                            <?php }
                            } ?>

                            
                        </tbody>

                    </table>
                    <script>
                                                function alertaPresente(id){
                                                let id_turno=id;
                                                Swal.fire({
                                                title: '¿Desea dar presente a este paciente',
                                                icon: 'success',
                                                showCancelButton: true,
                                                confirmButtonColor: '#3085d6',
                                                cancelButtonColor: '#d33',
                                                confirmButtonText: 'SI',
                                                cancelButtonText: 'NO',
                                            }).then((result) => {
                                                    if (result.isConfirmed) {
                                                    window.location='dar_presente.php?id_turno='+id_turno;
                                                    }
                                                   

                                            });
                                        }
                                            </script>  
                                            <script>
                                                function alertaAusente(id){
                                                let id_turno=id;
                                                Swal.fire({
                                                title: '¿Desea dar ausente a este paciente',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#3085d6',
                                                cancelButtonColor: '#d33',
                                                confirmButtonText: 'SI',
                                                cancelButtonText: 'NO',
                                            }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location='dar_ausente.php?id_turno='+ id_turno;
                                                    }
                                                    });
                                                }

                                            </script>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>