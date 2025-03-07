<?php
// Control de inicio de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: index.php");
    exit();
}
require_once("database.php");

$con = crearConexion();

/* 
CREAR USUARIOS
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
        if (crear_usuario($con, $usuario, $pass, $nombre, $apellido, $dni, $email, $telefono, $tipo)) {
            header("Location: administradores.php");
            exit();
        } else {
            echo "<p style='color: red;'>Error al crear usuario.</p>";
        }
    }
}
*/

/* 

MODIFICAR USUARIOS

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'], $_POST['nueva_pass'], $_POST['nuevo_nombre'], $_POST['nuevo_apellido'], $_POST['nuevo_dni'], $_POST['nuevo_email'], $_POST['nuevo_telefono'], $_POST['nuevo_tipo'])) {

    $usuario = $_POST['usuario'];
    $nueva_pass = $_POST['nueva_pass'];
    $nuevo_nombre = $_POST['nuevo_nombre'];
    $nuevo_apellido = $_POST['nuevo_apellido'];
    $nuevo_dni = $_POST['nuevo_dni'];
    $nuevo_email = $_POST['nuevo_email'];
    $nuevo_telefono = $_POST['nuevo_telefono'];
    $nuevo_tipo = $_POST['nuevo_tipo'];

    if (empty($usuario) || empty($nueva_pass) || empty($nuevo_nombre) || empty($nuevo_apellido) || empty($nuevo_dni) || empty($nuevo_email) || empty($nuevo_telefono) || $nuevo_tipo === '') {
        echo "<p style='color: red;'>Error: Rellena todos los campos.</p>";
    } else {
        if (modificar_usuarios($con, $usuario, $nueva_pass, $nuevo_nombre, $nuevo_apellido, $nuevo_dni, $nuevo_email, $nuevo_telefono, $nuevo_tipo)) {
            header("Location: administradores.php");
            exit();
        } else {
            echo "<p style='color: red;'>Error al modificar usuario.</p>";
        }
    }
}
*/

// ELIMINAR USUARIOS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_usuario'])) 
    {
        $usuario = $_POST['eliminar_usuario'];
        borrar_usuario($con, $usuario);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Administrador</title>
    <!-- <link rel="stylesheet" type="text/css" href="../css/admin-styles.css"> -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<?php
echo "
<div class='flex flex-col justify-center items-center gap-5'>
<h1 class='text-2xl font-bold'>Bienvenido a la Página de Administrador</h1>";


// ---------------------------GESTIÓN DE USUARIOS-----------------------------

echo "<h2 style='color:blue;'>Gestión de Usuarios:</h2>
</div>";
$usuarios = obtener_todos_los_usuarios($con);

    // MOSTRAR TABLA USUARIOS
    echo "<h4>Usuarios registrados:</h4>";
    if ($usuarios->num_rows === 0) {
        echo "<p>No se encuentran usuarios</p>";
    } else {
        echo "<table class='styled-table'>
            <thead>
                <tr>
                    <th>USUARIO</th><th>CONTRASEÑA</th><th>NOMBRE</th><th>APELLIDO</th><th>DNI</th><th>EMAIL</th><th>TELEFONO</th><th>TIPO</th><th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>";
        
        while ($fila = obtener_resultados($usuarios)) {
            extract($fila);
            echo "<tr>
                    <td>$usuario</td>
                    <td>$pass</td>
                    <td>$nombre</td>
                    <td>$apellido</td>
                    <td>$dni</td>
                    <td>$email</td>
                    <td>$telefono</td>
                    <td>$tipo</td>";
                    if ($_SESSION['usuario'] !== $usuario) 
                    {
                        echo "
                            <td>
                                <form method='POST' action='adminEditarUsuario.php'>
                                    <input type='hidden' name='editar_usuario' value='$usuario'>
                                    <input type='submit' style='background:#007bff;color:white;border:none;cursor:pointer;font-size:14px;float:left;margin-left:2px;' value='Editar'>
                                </form>
                                <form method='POST' action=''>
                                    <input type='hidden' name='eliminar_usuario' value='$usuario'>
                                    <input type='submit' onclick=\"return confirm('¿Estás seguro de que quieres eliminar este usuario?');\" style='background:#007bff;color:white;border:none;cursor:pointer;font-size:14px;float:left;margin-left:2px;' value='Eliminar'>
                                </form>
                            </td>";
                    }
                    else
                    {
                        echo "
                            <td>
                                <form method='POST' action='adminEditarUsuario.php'>
                                    <input type='hidden' name='editar_usuario' value='$usuario'>
                                    <input type='submit' style='background:#007bff;color:white;border:none;cursor:pointer;font-size:14px;float:left;margin-left:2px;' value='Editar'>
                                </form>
                            </td>";
                    }
                  echo "</tr>";
        }
        
        echo "</tbody></table>";
    }
    

echo "<h4>Dar de alta nuevos usuarios:</h4>";
?>
<!-- CREAR USUARIOS -->
<form method='POST' action='adminCrearUsuario.php'>
    <input type='submit' value="Añadir Usuario">
</form>

<!-- 
 CREAR USUARIOS 

<form method='POST' action='' id="admin-createUserForm">
    <input type='text' name='usuario' placeholder='Nombre de usuario' required><br>
    <label for="nombre">Nombre:</label>
    <input type='text' name='nombre' placeholder='Nombre' required><br>
    <label for="apellido">Apellido:</label>
    <input type='text' name='apellido' placeholder='Apellido' required><br>
    <label for="dni">DNI:</label>
    <input type='text' name='dni' placeholder='DNI' required><br>
    <label for="email">Email:</label>
    <input type='email' name='email' placeholder='Email' required><br>
    <label for="telefono">Teléfono:</label>
    <input type='number' name='telefono' placeholder='Telefono' required><br>
    <label for="pass">Contraseña:</label>
    <input type='password' name='pass' placeholder='Contraseña' required><br>
    <label for="tipo" id="admin-createUserFormDropdown">Tipo:</label>
    <select name='tipo' required>
        <option value=''>Selecciona...</option>
        <option value='0'>Administrador</option>
        <option value='1'>Jefe de Equipo</option>
        <option value='2'>Empleado</option>
    </select><br>
    <input type='submit' value='Crear'>
</form>
-->

<!-- 
MODIFICAR USUARIOS

<hr>
<h4>Modificar Usuarios:</h4>
<form method='POST' action='' id="admin-editUserForm">
    <label for="usuario">Usuario:</label>
    <input type='text' name='usuario' placeholder='Usuario a modificar' required><br>
    <label for="nuevo_nombre">Nombre:</label>
    <input type='text' name='nuevo_nombre' placeholder='Nuevo Nombre' required><br>
    <label for="nuevo_apellido">Apellido:</label>
    <input type='text' name='nuevo_apellido' placeholder='Nuevo Apellido' required><br>
    <label for="nuevo_dni">DNI:</label>
    <input type='text' name='nuevo_dni' placeholder='Nuevo DNI' required><br>
    <label for="nuevo_email">Email:</label>
    <input type='email' name='nuevo_email' placeholder='Nuevo Email' required><br>
    <label for="nuevo_telefono">Teléfono:</label>
    <input type='number' name='nuevo_telefono' placeholder='Nuevo Telefono' required><br>
    <label for="nueva_pass">Contraseña:</label>
    <input type='password' name='nueva_pass' placeholder='Nueva Contraseña' required><br>
    <label>Tipo:</label>
    <select name='nuevo_tipo' required>
        <option value='0'>Administrador</option>
        <option value='1'>Jefe de Equipo</option>
        <option value='2'>Empleado</option>
    </select><br>
    <input type='submit' value='Modificar'>
</form>
-->

<!--LogOut-->
<form action="logout.php" method="POST">
    <button type="submit" class="logout-button">Cerrar Sesión</button>
</form>