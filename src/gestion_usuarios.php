<?php
session_start();
include "../conexion.php";

// Verificar que el usuario tiene rol de administrador (rol=1)
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header("Location: index.php");
    exit;
}

$alert = '';

// Procesar eliminación de usuario
if(isset($_GET['accion']) && $_GET['accion'] == 'eliminar' && isset($_GET['id'])) {
    $id_usuario = intval($_GET['id']);
    
    // No permitir eliminar el propio usuario
    if($id_usuario == $_SESSION['idUser']) {
        $alert = '<div class="alert alert-danger" role="alert">No puedes eliminar tu propio usuario</div>';
    } else {
        // Verificar si hay turnos asociados al usuario
        $query_check = mysqli_query($conexion, "SELECT COUNT(*) as total FROM turnos WHERE usuario_carga IN (SELECT nombre FROM usuario WHERE idusuario = $id_usuario)");
        $row_check = mysqli_fetch_assoc($query_check);
        
        if($row_check['total'] > 0) {
            // Si hay turnos, desactivar el usuario en lugar de eliminarlo
            $query = mysqli_query($conexion, "UPDATE usuario SET estado = 0 WHERE idusuario = $id_usuario");
            if($query) {
                $alert = '<div class="alert alert-success" role="alert">Usuario desactivado correctamente</div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">Error al desactivar usuario: ' . mysqli_error($conexion) . '</div>';
            }
        } else {
            // Si no hay turnos, eliminar el usuario
            $query = mysqli_query($conexion, "DELETE FROM usuario WHERE idusuario = $id_usuario");
            if($query) {
                $alert = '<div class="alert alert-success" role="alert">Usuario eliminado correctamente</div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">Error al eliminar usuario: ' . mysqli_error($conexion) . '</div>';
            }
        }
    }
}

// Procesar activación/desactivación de usuario
if(isset($_GET['accion']) && ($_GET['accion'] == 'activar' || $_GET['accion'] == 'desactivar') && isset($_GET['id'])) {
    $id_usuario = intval($_GET['id']);
    $nuevo_estado = ($_GET['accion'] == 'activar') ? 1 : 0;
    
    // No permitir desactivar el propio usuario
    if($id_usuario == $_SESSION['idUser'] && $nuevo_estado == 0) {
        $alert = '<div class="alert alert-danger" role="alert">No puedes desactivar tu propio usuario</div>';
    } else {
        $query = mysqli_query($conexion, "UPDATE usuario SET estado = $nuevo_estado WHERE idusuario = $id_usuario");
        if($query) {
            $mensaje = ($nuevo_estado == 1) ? 'activado' : 'desactivado';
            $alert = '<div class="alert alert-success" role="alert">Usuario ' . $mensaje . ' correctamente</div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error al actualizar estado del usuario: ' . mysqli_error($conexion) . '</div>';
        }
    }
}

include_once "includes/header.php";
?>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h3>Gestión de Usuarios</h3>
                <a href="crear_usuario.php" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Nuevo Usuario
                </a>
            </div>
        </div>
        
        <?php echo $alert; ?>
        
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($conexion, "SELECT * FROM usuario ORDER BY rol ASC, nombre ASC");
                    
                    if(mysqli_num_rows($query) > 0) {
                        while($usuario = mysqli_fetch_assoc($query)) {
                            // Definir el nombre del rol
                            $rol_texto = '';
                            switch($usuario['rol']) {
                                case 1: $rol_texto = 'Administrador'; break;
                                case 2: $rol_texto = 'Profesional'; break;
                                case 3: $rol_texto = 'Secretario'; break;
                                default: $rol_texto = 'Desconocido'; break;
                            }
                            
                            // Definir clase y texto para el estado
                            $estado = isset($usuario['estado']) ? $usuario['estado'] : 0;
                            $estado_clase = ($estado == 1) ? 'badge-success' : 'badge-danger';
                            $estado_texto = ($estado == 1) ? 'Activo' : 'Inactivo';
                            ?>
                            <tr>
                                <td><?php echo $usuario['idusuario']; ?></td>
                                <td><?php echo $usuario['nombre']; ?></td>
                                <td><?php echo $usuario['usuario']; ?></td>
                                <td><?php echo $rol_texto; ?></td>
                                <td>
                                    <a href="editar_usuario.php?id=<?php echo $usuario['idusuario']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    
                                    <a href="gestion_usuarios.php?accion=eliminar&id=<?php echo $usuario['idusuario']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('ADVERTENCIA: Esta acción puede ser irreversible. ¿Está seguro de que desea eliminar este usuario?')">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay usuarios registrados</td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
