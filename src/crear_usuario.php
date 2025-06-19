<?php
session_start();
include "../conexion.php";

// Verificar que el usuario tiene rol de administrador (rol=1)
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header("Location: index.php");
    exit;
}

$alert = '';

// Procesar el formulario cuando se envía
if(isset($_POST['crear_usuario'])) {
    // Obtener y sanitizar los datos del formulario
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $clave = mysqli_real_escape_string($conexion, $_POST['clave']);
    $confirmar_clave = mysqli_real_escape_string($conexion, $_POST['confirmar_clave']);
    $rol = intval($_POST['rol']);
    
    // Validar datos
    if(empty($nombre) || empty($usuario) || empty($clave) || empty($confirmar_clave)) {
        $alert = '<div class="alert alert-danger" role="alert">Todos los campos son obligatorios</div>';
    } else if($clave != $confirmar_clave) {
        $alert = '<div class="alert alert-danger" role="alert">Las contraseñas no coinciden</div>';
    } else if($rol < 1 || $rol > 3) {
        $alert = '<div class="alert alert-danger" role="alert">El rol seleccionado no es válido</div>';
    } else {
        // Verificar que el usuario no exista ya
        $query_check = mysqli_query($conexion, "SELECT * FROM usuario WHERE usuario = '$usuario'");
        if(mysqli_num_rows($query_check) > 0) {
            $alert = '<div class="alert alert-warning" role="alert">El nombre de usuario ya existe</div>';
        } else {
            // Encriptar la contraseña
            $clave_encriptada = md5($clave);
            
            // Insertar el nuevo usuario
            $query_insert = mysqli_query($conexion, "INSERT INTO usuario (nombre, usuario, clave, rol) 
                                                  VALUES ('$nombre', '$usuario', '$clave_encriptada', $rol)");
            
            if($query_insert) {
                $alert = '<div class="alert alert-success" role="alert">
                            Usuario creado correctamente. <a href="gestion_usuarios.php" class="alert-link">Volver a la lista</a>
                          </div>';
                
                // Limpiar formulario
                $nombre = '';
                $usuario = '';
                $rol = 2; // Valor por defecto: Profesional
            } else {
                $alert = '<div class="alert alert-danger" role="alert">
                            Error al crear el usuario: ' . mysqli_error($conexion) . '
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
                <h3>Crear Nuevo Usuario</h3>
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
                               value="<?php echo isset($nombre) ? $nombre : ''; ?>" required>
                        <small class="text-muted">Nombre y apellido completos</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="usuario">Usuario:</label>
                        <input type="text" name="usuario" id="usuario" class="form-control" 
                               value="<?php echo isset($usuario) ? $usuario : ''; ?>" required>
                        <small class="text-muted">Nombre de usuario para iniciar sesión</small>
                    </div>
                    
                    <!-- Se eliminó el campo de correo electrónico por solicitud del usuario -->
                    
                    <div class="form-group">
                        <label for="rol">Rol del Usuario:</label>
                        <select name="rol" id="rol" class="form-control" required>
                            <option value="1" <?php echo (isset($rol) && $rol == 1) ? 'selected' : ''; ?>>Administrador</option>
                            <option value="2" <?php echo (!isset($rol) || $rol == 2) ? 'selected' : ''; ?>>Profesional</option>
                            <option value="3" <?php echo (isset($rol) && $rol == 3) ? 'selected' : ''; ?>>Secretario</option>
                        </select>
                        <small class="text-muted">Tipo de usuario y sus permisos en el sistema</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="clave">Contraseña:</label>
                        <input type="password" name="clave" id="clave" class="form-control" required>
                        <small class="text-muted">Contraseña segura</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmar_clave">Confirmar Contraseña:</label>
                        <input type="password" name="confirmar_clave" id="confirmar_clave" class="form-control" required>
                        <small class="text-muted">Repita la contraseña para confirmar</small>
                    </div>
                    
                    <div class="form-group text-center mt-4">
                        <button type="submit" name="crear_usuario" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>
