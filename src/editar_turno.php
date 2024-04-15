<?php
session_start();
require_once "../conexion.php";

include_once "includes/header.php";
$id_turno=$_GET['id_turno'];
$sql = "SELECT * FROM turnos WHERE idturno='$id_turno'"; 
$query = mysqli_query($conexion,$sql);
?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">Cargar Turno</h4>
            </div>
            <div class="card-body">
                <?php echo isset($alert) ? $alert : ''; 
                while ($data = mysqli_fetch_assoc($query)) { 
                ?>
                
                <form action="turno_editado.php" method="post" class="p-3">
                    
                    <div class="form-group">
                    <input type="hidden" name="id_turno" class="form-control" value="<?php echo $data['idturno']; ?>">
                        <label>Nombre:</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo $data['nombre']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Nº DNI:</label>
                        <input type="number" name="dni" class="form-control" value="<?php echo $data['dni']; ?>">
                    </div>

                    <div class="form-group">
                        <label>Fecha:</label>
                        <input type="date" name="fecha" class="form-control" value="<?php echo $data['fecha']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Hora:</label>
                        <input type="time" name="hora" class="form-control" value="<?php echo $data['hora']; ?>">
                    </div>
                    <?php } ?>
                    <div>
                        <button type="submit" class="btn btn-primary"> Cargar</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>