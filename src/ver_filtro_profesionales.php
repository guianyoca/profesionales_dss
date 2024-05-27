<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$hoy=date('Y-m-d');
$profesional = isset($_POST['profesional']) ? $_POST['profesional'] : null;
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
$dni = isset($_POST['dni']) ? $_POST['dni'] : null;
$fecha_recibida = isset($_POST['fecha']) ? $_POST['fecha'] : null;
if($profesional!='TODOS'){
$query = mysqli_query($conexion, "SELECT nombre FROM usuario WHERE idusuario='$profesional'");
$nombre_array = mysqli_fetch_assoc($query);
$nombre_final =$nombre_array['nombre'];}



    
// $fecha=$fecha_recibida->format('Y-m-d');
include_once "includes/header.php";



?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="tbl">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nombre</th>
                                <th>Nº DNI</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "../conexion.php";
                            $sql = "SELECT * FROM turnos WHERE 1"; 
                                                          
                                if (!empty($fecha_recibida)) {
                                    $sql .= " AND fecha = '$fecha_recibida'";
                                }
                                if (!empty($nombre)) {
                                    $sql .= " AND nombre LIKE '%$nombre%'";
                                }
                                if (!empty($dni)) {
                                    $sql .= " AND dni = '$dni'";
                                }
                                if (!empty($profesional)) {
                                    if($profesional=='TODOS'){
                                        $sql .= "";  
                                    }else $sql .= " AND usuario_carga = '$nombre_final'";
                                }
                                                                                         
                                // var_dump($sql);
                                // die();
                            $query = mysqli_query($conexion,$sql);
                            $result = mysqli_num_rows($query);
                            if ($result > 0) {
                                while ($data = mysqli_fetch_assoc($query)) { ?>
                                    <tr>
                                        <td><?php echo $data['nombre']; ?></td>
                                        <td><?php echo $data['dni']; ?></td>
                                        <td><?php echo $data['fecha']; ?></td>
                                        <td><?php echo $data['hora']; ?></td>
                                        <td><?php  if($data['estado']==1){
                                            echo "<strong style='color:green;'>PRESENTE</strong>";
                                        }
                                        if($data['estado']==2){
                                            echo "<strong style='color:red;'>AUSENTE</strong>";
                                        }
                                        if($data['estado']==0){
                                            echo "<strong style='color:blue;'>ASIGNADO</strong>";
                                        } ?></td>
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
<?php include_once "includes/footer.php"; ?>