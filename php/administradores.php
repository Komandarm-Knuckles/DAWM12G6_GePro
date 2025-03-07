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
echo "<body class='h-screen w-full bg-cover bg-center bg-fixed z-10 bg-[url(\"../img/engranajesFondo.jpeg\")]'>";
// div principal
echo"<div class='flex w-full h-screen justify-center items-center max-w-screen'>";
// div Contenedor
echo"<div class='flex w-full md:flex-row flex-col  justify-center items-center'>";
// Div Izquierdo
echo"<div class='flex w-80 md:flex-col md:h-177.5 pl-10 pt-5 bg-orange-500 gap-10'>
        <img class='w-10' src='../img/LogoEmpresa.png' alt='logo Empresa'/>
        <p>GePro</p>
        <p>Reuniones</p>
        <p>Proyectos</p>
        <p>Tareas</p>
        <p>Grupos</p>
";
// final div izquierdo
echo"</div>";
// Div derecho
echo"<div class='flex flex-col justify-center items-center gap-10 bg-gray-300 p-10'>";
echo"<h1 class='text-2xl font-bold'>Bienvenido a la Página de Administrador</h1>";


// ---------------------------GESTIÓN DE USUARIOS-----------------------------

echo "<h2 style='color:blue;'>Gestión de Usuarios:</h2>";
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
            <tbody class='shadow-2xl'>";
        
        while ($fila = obtener_resultados($usuarios)) {
            extract($fila);
            echo "<tr>
                    <td class='p-7'>$usuario</td>
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
                                    <button type='submit' style='background:#007bff;color:white;border:none;cursor:pointer;font-size:14px;float:left;margin-left:2px;' value='Editar'>
                                    <img src='../img/square-pen.png' alt='Eliminar' style='width: 20px; height: 20px;'/> 
                                    </button>
                                    </form>
                                <form method='POST' action=''>
                                    <input type='hidden' name='eliminar_usuario' value='$usuario'>
                                    <button type='submit' onclick=\"return confirm('¿Estás seguro de que quieres eliminar este usuario?');\" style='border: none; background: none; padding: 0; cursor: pointer;'>
                                        <img src='../img/trash-2.png' alt='Eliminar' style='width: 20px; height: 20px;'> 
                                    </button>
                                </form>
                            </td>";
                    }
                    else
                    {
                        echo "
                            <td>
                                <form method='POST' action='adminEditarUsuario.php'>
                                    <input type='hidden' name='editar_usuario' value='$usuario'>
                                    <button type='submit' style='background:#007bff;color:white;border:none;cursor:pointer;font-size:14px;float:left;margin-left:2px;' value='Editar'>
                                    <img src='../img/square-pen.png' alt='Eliminar' style='width: 20px; height: 20px;'/> 
                                    </button>
                                </form>
                            </td>";
                    }
                  echo "</tr>";
        }
        
        echo "</tbody></table>";
    }
    


?>
<!-- CREAR USUARIOS -->
 <div class="flex p-5 gap-2 justify-center items-center shadow-2xl">
<h4>Dar de alta nuevos usuarios:</h4>
<form method='POST' action='adminCrearUsuario.php' class="p-1 border-2">
    <input type='submit' value="Añadir Usuario">
</form>
</div>
<!--LogOut-->
<form action="logout.php" method="POST">
    <button type="submit" class="logout-button p-1 border-2">Cerrar Sesión</button>
</form>
<?php

// Final divs
echo"</div></div></div>";
echo"</body>";
?>
