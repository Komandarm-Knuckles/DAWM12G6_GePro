<?php
require_once("../database.php");
$con = crearConexion();

#region Control de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: ../index.php");
    exit();
}
#endregion

#region Comprobación por si acaso no se ha seleccionado el usuario de alguna manera
if (!isset($_POST['editar_usuario'])) {
    die("Error: No se ha seleccionado un usuario para editar.");
}
#endregion

#region Comprobación de si el usuario existe (también por si acaso)
$usuario = $_POST['editar_usuario'];
$datos_usuario = obtener_usuario_por_nombre($con, $usuario);
if (!$datos_usuario) {
    die("Error: Usuario no encontrado.");
}
#endregion

#region Recogida de datos y modificación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $nuevo_pass = $_POST['pass'];
    $nuevo_nombre = $_POST['nombre'];
    $nuevo_apellido = $_POST['apellido'];
    $nuevo_dni = $_POST['dni'];
    $nuevo_email = $_POST['email'];
    $nuevo_telefono = $_POST['telefono'];
    $nuevo_tipo = $_POST['tipo'];

    #region Expresiones regulares - Comprobación en servidor
    $dniRegex = "/^\d{8}[A-Z]$/";
    $emailRegex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $passwordRegex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&.\-_])[A-Za-z\d@$!%*?&.\-_]{8,}$/";

    if (!preg_match($dniRegex, $nuevo_dni)) {
        echo "<p style='color: red;'>Error: El DNI debe tener 8 números seguidos de una letra mayúscula.</p>";
    } elseif (!preg_match($emailRegex, $nuevo_email)) {
        echo "<p style='color: red;'>Error: Introduce un correo electrónico válido.</p>";
    } elseif (!empty($nuevo_pass) && !preg_match($passwordRegex, $nuevo_pass)) {
        echo "<p style='color: red;'>Error: La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.</p>";
    } else {
        if (modificar_usuarios($con, $usuario, $nuevo_pass, $nuevo_nombre, $nuevo_apellido, $nuevo_dni, $nuevo_email, $nuevo_telefono, $nuevo_tipo)) {
            header("Location: adminUsuarios.php");
            exit();
        } else {
            echo '<script type="text/javascript">alert("No se ha podido realizar la modificación");window.location.href="index.php";</script>';
        }
    }
    #endregion
}
#endregion
?>

<?php #region HTML 
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario Admin</title>
    <!-- <link rel="stylesheet" type="text/css" href="../css/styles.css"> -->
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
        <div class="flex flex-col gap-10 p-5 md:max-w-[65%] max-w-[95%] rounded-xl bg-gray-300 justify-center items-center">
            <h1 class="font-bold text-lg bg-orange-400 md:text-5xl underline text-center p-2 rounded-lg">EDITAR USUARIO</h1>
        <form method="POST" action="" id="formEditarUsuarios" class="flex flex-col gap-6  items-center w-[40em]">
            <input type="hidden" name="editar_usuario" value="<?php echo $usuario; ?>">
            <div class="w-full">
            <p class="font-bold">Contraseña</p>
            <input type="password" id="pass" name="pass" placeholder="Nueva contraseña Opcional" class="border-1 rounded-md text-center p-2 focus:outline-none w-full">
            </div>
            <div class="w-full">
            <p class="font-bold">Nombre*</p>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($datos_usuario['nombre']); ?>" required class="border-1 rounded-md text-center p-2 focus:outline-none w-full">
            </div>
            <div class="w-full">
            <p class="font-bold">Apellido*</p>
            <input type="text" name="apellido" value="<?php echo htmlspecialchars($datos_usuario['apellido']); ?>" required class="border-1 rounded-md text-center p-2 focus:outline-none w-full">
            </div>
            <div class="w-full">
            <p class="font-bold">DNI*</p>
            <input type="text" id="dni" name="dni" value="<?php echo htmlspecialchars($datos_usuario['dni']); ?>" required class="border-1 rounded-md text-center p-2 focus:outline-none w-full">
            </div>
            <div class="w-full">
            <p class="font-bold">Email*</p>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($datos_usuario['email']); ?>" required class="border-1 rounded-md text-center p-2 focus:outline-none w-full">
            </div>
            <div class="w-full">
            <p class="font-bold">Teléfono*</p>
            <input type="text" name="telefono" value="<?php echo htmlspecialchars($datos_usuario['telefono']); ?>" required class="border-1 rounded-md text-center p-2 focus:outline-none w-full">
            </div>
            <div class="w-full">
            <p class="font-bold">Tipo de Usuario*</p>
            <select name="tipo" class="border-1 rounded-md text-center p-2 focus:outline-none w-full" required>
                <option value="0" <?php echo ($datos_usuario['tipo'] == 0) ? 'selected' : ''; ?>>Administrador</option>
                <option value="1" <?php echo ($datos_usuario['tipo'] == 1) ? 'selected' : ''; ?>>Jefe de Equipo</option>
                <option value="2" <?php echo ($datos_usuario['tipo'] == 2) ? 'selected' : ''; ?>>Empleado</option>
            </select>
            </div>
            <label for="campos">Los campos asignados con un <strong>(*)</strong> son <strong>Obligatorios</strong></label>
            <div class="flex gap-5 justify-center items-center">
                <button type="submit" name="update_user" class="bg-orange-400 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-md w-[10em]">Guardar Cambios</button>
                <button type="button" onclick="history.back()" class="bg-orange-400 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-md w-[10em]">Cancelar</button>
            </div>
        </form>
    </div>
    
</body>

</html>
<?php #endregion 
?>