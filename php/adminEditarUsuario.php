<?php
require_once("database.php");
session_start();
$con = crearConexion();

#region Comprobación por si acaso no se ha seleccionado el usuario de alguna manera
if (!isset($_POST['editar_usuario'])) 
{
    die("Error: No se ha seleccionado un usuario para editar.");
}
#endregion

#region Comprobación de si el usuario existe (también por si acaso)
$usuario = $_POST['editar_usuario'];
$datos_usuario = obtener_usuario_por_nombre($con, $usuario);
if (!$datos_usuario) 
{
    die("Error: Usuario no encontrado.");
}
#endregion

#region Recogida de datos y modificación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) 
{
    $nuevo_pass = $_POST['pass'];
    $nuevo_nombre = $_POST['nombre'];
    $nuevo_apellido = $_POST['apellido'];
    $nuevo_dni = $_POST['dni'];
    $nuevo_email = $_POST['email'];
    $nuevo_telefono = $_POST['telefono'];
    $nuevo_tipo = $_POST['tipo'];

    if (modificar_usuarios($con, $usuario, $nuevo_pass, $nuevo_nombre, $nuevo_apellido, $nuevo_dni, $nuevo_email, $nuevo_telefono, $nuevo_tipo)) 
    {
        header("Location: administradores.php");
        exit();
    } 
    else 
    {
        echo'<script type="text/javascript">alert("No se ha podido realizar la modificación");window.location.href="index.php";</script>';
    }
}
#endregion
?>

<?php #region HTML ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
</head>
<body>
    <h1>Editar Usuario</h1>
    <form method="POST" action="" id="admin-createUserForm">
        <input type="hidden" name="editar_usuario" value="<?php echo $usuario; ?>">
        <label>Contraseña:</label>
        <input type="password" name="pass" placeholder="Nueva contraseña - opcional"><br>
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($datos_usuario['nombre']); ?>" required><br>
        <label>Apellido:</label>
        <input type="text" name="apellido" value="<?php echo htmlspecialchars($datos_usuario['apellido']); ?>" required><br>
        <label>DNI:</label>
        <input type="text" name="dni" value="<?php echo htmlspecialchars($datos_usuario['dni']); ?>" required><br>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($datos_usuario['email']); ?>" required><br>
        <label>Teléfono:</label>
        <input type="text" name="telefono" value="<?php echo htmlspecialchars($datos_usuario['telefono']); ?>" required><br>
        <label>Tipo:</label>
        <select name="tipo" required>
            <option value="0" <?php echo ($datos_usuario['tipo'] == 0) ? 'selected' : ''; ?>>Administrador</option>
            <option value="1" <?php echo ($datos_usuario['tipo'] == 1) ? 'selected' : ''; ?>>Jefe de Equipo</option>
            <option value="2" <?php echo ($datos_usuario['tipo'] == 2) ? 'selected' : ''; ?>>Empleado</option>
        </select><br>
        <input type="submit" name="update_user" value="Actualizar Usuario">
    </form>
</body>
</html>
<?php #endregion ?>
