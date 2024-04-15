<?php
session_start();
require_once "../conexion.php";

include_once "includes/header.php";
?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">Cargar Turno</h4>
            </div>
            <div class="card-body">
                <?php echo isset($alert) ? $alert : ''; ?>
                
                <form action="cargar_turno.php" method="post" class="p-3">
                    
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nº DNI:</label>
                        <input type="number" name="dni" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Fecha:</label>
                        <input type="date" name="fecha" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Hora:</label>
                        <input type="time" name="hora" class="form-control" required>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary"> Cargar</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>