<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];


include_once "includes/header.php";
?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <?php echo (isset($alert)) ? $alert : '' ; ?>
                <form action="marcar_ingreso.php" method="post" autocomplete="off" id="formulario">
                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <input type="submit" value="Ingreso" class="btn btn-success" id="btnAccion">
                        </div>
                    </div>
                </form>
                <form action="marcar_salida.php" method="post" autocomplete="off" id="formulario">
                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <input type="submit" value="Salida" class="btn btn-danger" id="btnAccion">
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="tbl">
                        <thead class="thead-dark">
                            <tr>
                                <th>Fecha Ingreso</th>
                                <th>Hora Ingreso</th>
                                <th>Fecha Salida</th>
                                <th>Hora Salida</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "../conexion.php";
                            $hoy=date('Y-m-d');
                            $query = mysqli_query($conexion, "SELECT * FROM asistencias WHERE idusuario='$id_user' AND fecha_ingreso='$hoy' ORDER BY hora_ingreso ASC LIMIT 1");
                            $result = mysqli_num_rows($query);
                            if ($result > 0) {
                                while ($data = mysqli_fetch_assoc($query)) { ?>
                                    <tr>
                                        <td><?php echo $data['fecha_ingreso']; ?></td>
                                        <td><?php echo $data['hora_ingreso']; ?></td>
                                        <td><?php echo $data['fecha_salida']; ?></td>
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