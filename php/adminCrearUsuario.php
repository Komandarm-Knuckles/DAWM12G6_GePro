<?php
require_once("database.php");
session_start();
$con = crearConexion();

#region Control de sesión
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] !== 0) 
{
    header("Location: index.php");
    exit();
}
#endregion

#region Recogida de datos e inserción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'], $_POST['pass'], $_POST['nombre'], $_POST['apellido'], $_POST['dni'], $_POST['email'], $_POST['telefono'], $_POST['tipo'])) 
{
    $usuario = $_POST['usuario'];
    $pass = $_POST['pass'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $tipo = $_POST['tipo'];

    if (empty($usuario) || empty($pass) || empty($nombre) || empty($apellido) || empty($dni) || empty($email) || empty($telefono) || $tipo === '') 
    {
        echo "<p style='color: red;'>Error: Todos los campos son obligatorios.</p>";
    } 
    else 
    {
        if (crear_usuario($con, $usuario, $pass, $nombre, $apellido, $dni, $email, $telefono, $tipo)) 
        {
            header("Location: administradores.php");
            exit();
        } 
        else 
        {
            echo "<p style='color: red;'>Error al crear usuario.</p>";
        }
    }
}
#endregion
?>

<?php #region HTML y Javascript para expresiones regulares ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="stylesheet" type="text/css" href="../css/admin-styles.css">
</head>
<body>
    <h1>Crear Nuevo Usuario</h1>
    <form id="userForm" method="POST" action="">
    <label>Usuario:</label>
    <input type="text" name="usuario" id="usuario" placeholder="Nombre de usuario" minlength="4" required><br>

    <label>Nombre:</label>
    <input type="text" name="nombre" id="nombre" placeholder="Nombre" minlength="3" required><br>

    <label>Apellido:</label>
    <input type="text" name="apellido" id="apellido" placeholder="Apellido" minlength="3" required><br>

    <label>DNI:</label>
    <input type="text" name="dni" id="dni" placeholder="DNI" required><br>

    <label>Email:</label>
    <input type="email" name="email" id="email" placeholder="Correo electrónico" required><br>

    <label>Teléfono:</label>
    <input type="number" name="telefono" id="telefono" placeholder="Teléfono" minlength="9" required><br>

    <label>Contraseña:</label>
    <input type="password" name="pass" id="pass" placeholder="Contraseña" required><br>

    <label>Tipo:</label>
    <select name="tipo" required>
        <option value="">Selecciona...</option>
        <option value="0">Administrador</option>
        <option value="1">Jefe de Equipo</option>
        <option value="2">Empleado</option>
    </select><br>

    <input type="submit" value="Crear Usuario">
</form>

<!-- Expresiones Regulares formulario Javascript -->
<script>
document.getElementById("userForm").addEventListener("submit", function(event) {
    let dni = document.getElementById("dni").value;
    let email = document.getElementById("email").value;
    let password = document.getElementById("pass").value;

    let dniRegex = /^\d{8}[A-Z]$/;
    let emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    let passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[A-Za-z\d]{8,}$/;


    if (!dniRegex.test(dni)) {
        alert("El DNI debe tener 8 números seguidos de una letra mayúscula.");
        event.preventDefault();
        return;
    }

    if (!emailRegex.test(email)) {
        alert("Por favor, introduce un correo electrónico válido.");
        event.preventDefault();
        return;
    }

    if (!passwordRegex.test(password)) {
        alert("La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.");
        event.preventDefault();
        return;
    }

    alert("Formulario enviado correctamente.");
});
</script>

</body>
</html>
<?php #endregion ?>