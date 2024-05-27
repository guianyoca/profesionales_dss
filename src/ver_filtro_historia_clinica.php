<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$nombre_user = $_SESSION['nombre'];
$hoy=date('Y-m-d');
$profesional = isset($_POST['profesional']) ? $_POST['profesional'] : null;
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
$dni = isset($_POST['dni']) ? $_POST['dni'] : null;
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
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "../conexion.php";
                            $sql = "SELECT * FROM historias_clinicas WHERE 1"; 
                                                          
            
                                if (!empty($nombre)) {
                                    $sql .= " AND nombre LIKE '%$nombre%'";
                                }
                                if (!empty($dni)) {
                                    $sql .= " AND dni = '$dni'";
                                }if($nombre_user=='JEFE'){
                                if (!empty($profesional)) {
                                    if($profesional=='TODOS'){
                                        $sql .= "";  
                                    }else $sql .= " AND usuario_carga = '$profesional'";
                                }}else $sql .= " AND usuario_carga = '$id_user'";
                                 
                                    
                
                                
                                                                                         
                                // var_dump($sql);
                                // die();
                            $query = mysqli_query($conexion,$sql);
                            $result = mysqli_num_rows($query);
                            if ($result > 0) {
                                while ($data = mysqli_fetch_assoc($query)) { ?>
                                    <tr>
                                        <td><?php echo $data['nombre']; ?></td>
                                        <td><?php echo $data['dni']; ?></td>
                                        <td><a href="ver_historia_elegida.php?id_historia=<?php echo $data['idhistoria'];?>" class="btn btn-primary" style="color:white">Ver</a></td>
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