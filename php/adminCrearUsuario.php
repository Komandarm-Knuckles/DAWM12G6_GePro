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
        #region Expresiones regulares - Comprobación en servidor
        $dniRegex = "/^\d{8}[A-Z]$/";
        $emailRegex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
        $passwordRegex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&.\-_])[A-Za-z\d@$!%*?&.\-_]{8,}$/";

        if (!preg_match($dniRegex, $dni)) 
        {
            echo "<p style='color: red;'>Error: El DNI debe tener 8 números seguidos de una letra mayúscula.</p>";
        } 
        elseif (!preg_match($emailRegex, $email)) 
        {
            echo "<p style='color: red;'>Error: Introduce un correo electrónico válido.</p>";
        } 
        elseif (!preg_match($passwordRegex, $pass)) 
        {
            echo "<p style='color: red;'>Error: La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.</p>";
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
        #endregion 
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
    <!-- <link rel="stylesheet" type="text/css" href="../css/admin-styles.css"> -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="../js/regExp.js"></script>
</head>
<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../img/pixels14.jpg')]">
    <div class="max-w-[90%] w-[40em] bg-white p-8 rounded shadow-xl">
        <h1 class="text-2xl font-bold text-center underline p-2">Crear Nuevo Usuario</h1>
        <form id="formUsuarios" method="POST" action="" class="flex flex-col gap-3 justify-center items-center ">
                Usuario:
                <input type="text" name="usuario" id="usuario" placeholder="Nombre de usuario" minlength="4" required class="mt-1 p-2 border rounded w-full" />
                  
                Nombre:
                <input type="text" name="nombre" id="nombre" placeholder="Nombre" minlength="3" required class="mt-1 p-2 border rounded w-full" />
            
                Apellido:
                <input type="text" name="apellido" id="apellido" placeholder="Apellido" minlength="3" required class="mt-1 p-2 border rounded w-full" />

                DNI:
                <input type="text" name="dni" id="dni" placeholder="DNI" required class="mt-1 p-2 border rounded w-full" />
        
                Email:
                <input type="email" name="email" id="email" placeholder="Correo electrónico" required class="mt-1 p-2 border rounded w-full" />
            
                Teléfono:
                <input type="number" name="telefono" id="telefono" placeholder="Teléfono" minlength="9" required class="mt-1 p-2 border rounded w-full" />
            
                Contraseña:
                <input type="text" name="pass" id="pass" placeholder="Contraseña" required class="mt-1 p-2 border rounded w-full" />
            
                 Tipo:
                <select name="tipo" required class="mt-1 p-2 border rounded w-full">
                    <option value="">Selecciona...</option>
                    <option value="0">Administrador</option>
                    <option value="1">Jefe de Equipo</option>
                    <option value="2">Empleado</option>
                </select>
            <div class="flex gap-5">
                <input type="submit" value="Crear Usuario" class="bg-orange-400 text-white p-2 rounded w-50 items-center cursor-pointer hover:bg-orange-700 font-bold" />
                <button type="button" onclick="history.back()" class="bg-orange-400 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-md w-[10em]">Volver</button>
            </div>
        </form>
    </div>
    
</body>
</html>
<?php #endregion ?>