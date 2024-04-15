<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$profesional = isset($_POST['profesional']) ? $_POST['profesional'] : null;
$fecha_recibida = isset($_POST['fecha']) ? $_POST['fecha'] : null;
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
                                <th>Fecha</th>
                                <th>Hora Entrada</th>
                                <th>Hora Salida</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "../conexion.php";
                            $sql = "SELECT a.*, b.* FROM asistencias AS a INNER JOIN usuario AS b ON a.idusuario=b.idusuario WHERE 1"; 
                                                          
                                if (!empty($fecha_recibida)) {
                                    $sql .= " AND a.fecha_ingreso = '$fecha_recibida'";
                                }
                               
                                if (!empty($profesional)) {
                                    if($profesional=='TODOS'){
                                        $sql .= "";  
                                    }else $sql .= " AND a.idusuario = '$profesional'";
                                }
                                                                                         
                                // var_dump($sql);
                                // die();
                            $query = mysqli_query($conexion,$sql);
                            $result = mysqli_num_rows($query);
                            if ($result > 0) {
                                while ($data = mysqli_fetch_assoc($query)) { ?>
                                    <tr>
                                        <td><?php echo $data['nombre']; ?></td>
                                        <td><?php echo $data['fecha_ingreso']; ?></td>
                                        <td><?php echo $data['hora_ingreso']; ?></td>
                                        <td><?php echo $data['hora_salida']; ?></td>
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