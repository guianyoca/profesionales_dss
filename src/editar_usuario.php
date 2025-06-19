<?php
session_start();
include "../conexion.php";

// Verificar que el usuario tiene rol de administrador (rol=1)
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header("Location: index.php");
    exit;
}

$alert = '';
$usuario_data = null;

// Verificar que se ha proporcionado un ID válido
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: gestion_usuarios.php");
    exit;
}

$id_usuario = intval($_GET['id']);

// Obtener datos del usuario
$query = mysqli_query($conexion, "SELECT * FROM usuario WHERE idusuario = $id_usuario");
if(mysqli_num_rows($query) == 0) {
    header("Location: gestion_usuarios.php");
    exit;
}

$usuario_data = mysqli_fetch_assoc($query);

// Procesar el formulario cuando se envía
if(isset($_POST['editar_usuario'])) {
    // Obtener y sanitizar los datos del formulario
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $rol = intval($_POST['rol']);
    
    // Verificar si se está cambiando la contraseña
    $cambiar_clave = !empty($_POST['clave']) && !empty($_POST['confirmar_clave']);
    
    // Validar datos
    if(empty($nombre) || empty($usuario)) {
        $alert = '<div class="alert alert-danger" role="alert">Los campos de nombre y usuario son obligatorios</div>';
    } else if($rol < 1 || $rol > 3) {
        $alert = '<div class="alert alert-danger" role="alert">El rol seleccionado no es válido</div>';
    } else if($cambiar_clave && $_POST['clave'] != $_POST['confirmar_clave']) {
        $alert = '<div class="alert alert-danger" role="alert">Las contraseñas no coinciden</div>';
    } else {
        // Verificar que el usuario no exista ya (excepto para el usuario actual)
        $query_check = mysqli_query($conexion, "SELECT * FROM usuario WHERE usuario = '$usuario' AND idusuario != $id_usuario");
        if(mysqli_num_rows($query_check) > 0) {
            $alert = '<div class="alert alert-danger" role="alert">El nombre de usuario ya está en uso por otro usuario</div>';
        } else {
            // Construir la consulta de actualización
            $query_update = "UPDATE usuario SET 
                            nombre = '$nombre', 
                            usuario = '$usuario', 
                            rol = $rol";
            
            // Si se está cambiando la contraseña, añadirla a la consulta
            if($cambiar_clave) {
                $clave_encriptada = md5($_POST['clave']);
                $query_update .= ", clave = '$clave_encriptada'";
            }
            
            // Finalizar la consulta
            $query_update .= " WHERE idusuario = $id_usuario";
            
            // Ejecutar la consulta
            if(mysqli_query($conexion, $query_update)) {
                $alert = '<div class="alert alert-success" role="alert">
                            Usuario actualizado correctamente. <a href="gestion_usuarios.php" class="alert-link">Volver a la lista</a>
                          </div>';
                
                // Actualizar los datos para mostrar los valores actualizados
                $usuario_data['nombre'] = $nombre;
                $usuario_data['usuario'] = $usuario;
                $usuario_data['rol'] = $rol;
            } else {
                $alert = '<div class="alert alert-danger" role="alert">
                            Error al actualizar el usuario: ' . mysqli_error($conexion) . '
                          </div>';
            }
        }
    }
}

include_once "includes/header.php";
?>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <h3>Editar Usuario</h3>
                <a href="gestion_usuarios.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        
        <?php echo $alert; ?>
        
        <div class="row">
            <div class="col-md-6 mx-auto">
                <form action="" method="post" autocomplete="off">
                    <div class="form-group">
                        <label for="nombre">Nombre Completo:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" 
                               value="<?php echo isset($usuario_data['nombre']) ? $usuario_data['nombre'] : ''; ?>" required>
                        <small class="text-muted">Nombre y apellido completos</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="usuario">Usuario:</label>
                        <input type="text" name="usuario" id="usuario" class="form-control" 
                               value="<?php echo isset($usuario_data['usuario']) ? $usuario_data['usuario'] : ''; ?>" required>
                        <small class="text-muted">Nombre de usuario para iniciar sesión</small>
                    </div>
                    
                    <!-- Se eliminó el campo de correo electrónico por solicitud del usuario -->
                    
                    <div class="form-group">
                        <label for="rol">Rol del Usuario:</label>
                        <select name="rol" id="rol" class="form-control" required>
                            <option value="1" <?php echo (isset($usuario_data['rol']) && $usuario_data['rol'] == 1) ? 'selected' : ''; ?>>Administrador</option>
                            <option value="2" <?php echo (isset($usuario_data['rol']) && $usuario_data['rol'] == 2) ? 'selected' : ''; ?>>Profesional</option>
                            <option value="3" <?php echo (isset($usuario_data['rol']) && $usuario_data['rol'] == 3) ? 'selected' : ''; ?>>Secretario</option>
                        </select>
                        <small class="text-muted">Tipo de usuario y sus permisos en el sistema</small>
                    </div>
                    
                    <!-- Se eliminó la opción de estado por solicitud del usuario -->
                    
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Cambiar Contraseña (opcional)</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="clave">Nueva Contraseña:</label>
                                <input type="password" name="clave" id="clave" class="form-control">
                                <small class="text-muted">Dejar en blanco para mantener la contraseña actual</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirmar_clave">Confirmar Nueva Contraseña:</label>
                                <input type="password" name="confirmar_clave" id="confirmar_clave" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group text-center mt-4">
                        <button type="submit" name="editar_usuario" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
