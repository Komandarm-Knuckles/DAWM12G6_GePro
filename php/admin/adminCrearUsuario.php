<?php
require_once("../database.php");
session_start();
$con = crearConexion();

#region Control de sesión
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: ../index.php");
    exit();
}
#endregion
#region Recogida de datos e inserción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'], $_POST['pass'], $_POST['nombre'], $_POST['apellido'], $_POST['dni'], $_POST['email'], $_POST['telefono'], $_POST['tipo'])) {
    $usuario = $_POST['usuario'];
    $pass = $_POST['pass'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $tipo = $_POST['tipo'];

    if (empty($usuario) || empty($pass) || empty($nombre) || empty($apellido) || empty($dni) || empty($email) || empty($telefono) || $tipo === '') {
        echo "<p style='color: red;'>Error: Todos los campos son obligatorios.</p>";
    } else {
        #region Expresiones regulares - Comprobación en servidor
        $dniRegex = "/^\d{8}[A-Z]$/";
        $emailRegex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
        $passwordRegex = '/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#\$%\^&\*\(\)_\-\+=\{\}\[\]:;"\'<>,\.?\/\\\\|~`€])[A-Za-z\dñÑ!@#\$%\^&\*\(\)_\-\+=\{\}\[\]:;"\'<>,\.?\/\\\\|~`€]{8,}$/u';

        if (!preg_match($dniRegex, $dni)) {
            echo "<p style='color: red;'>Error: El DNI debe tener 8 números seguidos de una letra mayúscula.</p>";
        } elseif (!preg_match($emailRegex, $email)) {
            echo "<p style='color: red;'>Error: Introduce un correo electrónico válido.</p>";
        } elseif (!preg_match($passwordRegex, $pass)) {
            echo "<p style='color: red;'>Error: La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.</p>";
        } else {
            if (crear_usuario($con, $usuario, $pass, $nombre, $apellido, $dni, $email, $telefono, $tipo)) {
                header("Location: adminUsuarios.php");
                exit();
            } else {
                echo "<p style='color: red;'>Error al crear usuario.</p>";
            }
        }
        #endregion 
    }
}
#endregion
?>

<?php #region HTML y Javascript para expresiones regulares 
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario Admin</title>
    <!-- <link rel="stylesheet" type="text/css" href="../css/admin-styles.css"> -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../js/regExp.js"></script>
</head>

<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex flex-col max-w-[90%] w-[40em] bg-gray-300 p-8 rounded shadow-xl gap-6">
            <h1 class="font-bold text-lg bg-orange-400 md:text-5xl underline text-center p-2 rounded-lg">CREAR NUEVO USUARIO</h1>
        <div class="flex justify-center items-center">
            <span class="block h-0.5 w-130 bg-black opacity-40"></span>
        </div>
        <div class="flex flex-col ">
            <form id="formUsuarios" method="POST" action="" class="flex flex-col w-full gap-6">
                <div>
                    <label for ="usuario" class="font-bold">Usuario*</label>
                    <input type="text" name="usuario" id="usuario" placeholder="Nombre de usuario" minlength="4" required class=" p-2 border rounded-xl w-full" />
                </div>
                <div>
                    <label for ="nombre" class="font-bold">Nombre*</label>
                    <input type="text" name="nombre" id="nombre" placeholder="Nombre" minlength="3" required class=" p-2 border rounded-xl w-full" />
                </div>
                <div>
                    <label for ="apellido" class="font-bold">Apellido*</label>
                    <input type="text" name="apellido" id="apellido" placeholder="Apellido" minlength="3" required class=" p-2 border rounded-xl w-full" />
                </div>
                <div>
                    <label for ="dni" class="font-bold">DNI*</label>
                    <input type="text" name="dni" id="dni" placeholder="DNI" required class=" p-2 border rounded-xl w-full" />
                </div>
                <div>
                    <label for ="email" class="font-bold">Email*</label>
                    <input type="email" name="email" id="email" placeholder="Correo electrónico" required class=" p-2 border rounded-xl w-full" />
                </div>
                <div>
                    <label for ="number" class="font-bold">Teléfono*</label>
                    <input type="number" name="telefono" id="telefono" placeholder="Teléfono" minlength="9" required class=" p-2 border rounded-xl w-full" />
                </div>
                <div>
                    <label for ="tipo" class="font-bold">Tipo de Usuario*</label>
                    <select name="tipo" required class=" p-2 border rounded-xl text-center w-full">
                        <option value="">Selecciona...</option>
                        <option value="0">Administrador</option>
                        <option value="1">Jefe de Equipo</option>
                        <option value="2">Empleado</option>
                    </select>
                </div>
                <div>
                    <label for ="pass" class="font-bold">Contraseña*</label>
                    <input type="text" name="pass" id="pass" placeholder="Contraseña" required class=" p-2 border rounded-xl w-full" />
                </div>
        </div>
        <label for="campos">Los campos asignados con un <strong>(*)</strong> son <strong>Obligatorios</strong></label>
        <div class="flex justify-center items-center gap-10 ">
            <input type="submit" value="Crear Usuario" class="bg-orange-400 text-white p-2 rounded-xl w-[10em] items-center cursor-pointer hover:bg-orange-700 font-bold" />
                <button type="button" onclick="window.location.href='adminUsuarios.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-fit p-3 shadow-lg">Cancelar</button>
        </div>
        <span class="block h-0.5 w-full bg-black opacity-40"></span>


        <div class="flex justify-center items-center gap-10">
            <form action="../logout.php" method="POST" class="p-5 flex md:flex-row flex-col gap-10">
                <button type="button" onclick="window.location.href='administradores.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-fit p-3 shadow-lg">Panel de Administrador</button>

            </form>
        </div>
        </form>
    </div>

</body>

</html>
<?php #endregion 
?>