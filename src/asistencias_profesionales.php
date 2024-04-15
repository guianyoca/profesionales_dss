<?php
session_start();
require_once "../conexion.php";

include_once "includes/header.php";
?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">Filtrar Asistencia de Profesionales</h4>
            </div>
            <div class="card-body">
                <?php echo isset($alert) ? $alert : ''; ?>
                
                <form action="ver_filtro_asistencias.php" method="post" class="p-3">
                    
                    <div class="form-group">
                        <label>Profesional:</label>
                        <select class="form-control" name='profesional'>
                        <option value="TODOS" class="form-control">TODOS</option>
                        <?php 
                    $query4 = mysqli_query($conexion, "SELECT * FROM usuario WHERE nombre<>'JEFE'");
                    while ($row4 = mysqli_fetch_assoc($query4)) {?>
                            <option value="<?php echo $row4['idusuario'];?>" class="form-control"><?php echo $row4['nombre'];?></option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Fecha:</label>
                        <input type="date" name="fecha" class="form-control">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary"> Buscar</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>