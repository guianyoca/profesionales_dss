<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$nombre_user = $_SESSION['nombre'];
$hoy=date('Y-m-d');


include_once "includes/header.php";
?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
               <h3>Pacientes del dia <?php echo $hoy;?></h3>
            </div>
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
                            
                            $query = mysqli_query($conexion, "SELECT * FROM turnos WHERE (usuario_carga='$id_user' OR usuario_carga='$nombre_user') AND fecha='$hoy'");
                            $result = mysqli_num_rows($query);
                            if ($result > 0) {
                                while ($data = mysqli_fetch_assoc($query)) { ?>
                                    <tr>
                                        <td><?php echo $data['nombre']; ?></td>
                                        <td><?php echo $data['dni']; ?></td>
                                        <td><?php echo $data['fecha']; ?></td>
                                        <td><?php echo $data['hora']; ?></td>
                                        <td>
                                              
                                        <?php  
                                        
                                        
                                        if($data['estado']==0){?> 
                                            <button  type="button" class="btn btn-success" onclick="alertaPresente(<?php echo $data['idturno']; ?>)">Presente</button>
                                            <button  type="button" class="btn btn-danger" onclick="alertaAusente(<?php echo $data['idturno']; ?>)">Ausente</button>

                                        <?php } 
                                        if($data['estado']==1){
                                            echo "<strong style='color:green;'>PRESENTE</strong>";
                                        }elseif($data['estado']==2){
                                            echo "<strong style='color:red;'>AUSENTE</strong>";
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