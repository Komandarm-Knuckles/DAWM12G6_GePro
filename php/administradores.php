<?php
require_once ("database.php");
session_start();
$con = crearConexion();
echo "<h1>Bienvenido a la Página de Administrador</h1>";

// ---------------------------GESTION DE USUARIOS-----------------------------

echo "<h2 style = 'color:blue';>Gestión de Usuarios:</h2>";
$usuarios = obtener_todos_los_usuarios($con);
$num_filas_usuarios = obtener_num_filas($usuarios);

// MOSTRAR TABLA USUARIOS
echo"<h4>Usuarios registrados:</h4>";
if ($num_filas_usuarios == 0){
    echo "<p>No se encuentran usuarios</p>";
}else{
    echo"<table border = '1'>
        <tr>
        <td>ID</td><td>CONTRASEÑA</td><td>NOMBRE</td><td>APELLIDO</td><td>DNI</td><td>EMAIL</td><td>TELEFONO</td><td>TIPO</td>
        </tr>";
        while ($fila = obtener_resultados($usuarios)) {
            extract($fila);
            echo "<tr><td>$usuario</td><td>$pass</td><td>$nombre</td><td>$apellido</td><td>$dni</td><td>$email</td><td>$telefono</td><td>$tipo</td></tr>";
        }
        echo "</table>";
}

//CREAR_USUARIOS
echo"<h4>Dar de alta nuevos usuarios:</h4>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'], $_POST['pass'], $_POST['nombre'], $_POST['apellido'],$_POST['dni'],$_POST['email'],$_POST['telefono'], $_POST['tipo'])) {
    $usuario = $_POST['usuario'];
    $pass = $_POST['pass'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $tipo = $_POST['tipo'];

    if (empty($usuario) ||empty($pass) ||empty($nombre) ||empty($apellido) ||empty($dni) ||empty($email) ||empty($telefono) || $tipo === '') {
        echo "<p style='color: red;'>Error: Todos los campos son obligatorios.</p>";
    }else{
          crear_usuario($con,$usuario,$pass,$nombre,$apellido,$dni,$email,$telefono,$tipo);
          echo "<p style='color: green;'>Usuario Creado Correctamente</p>";
          header("Location: administradores.php");
          exit();
    }
}
echo "<form method='POST' action = ''>
    <input type='text' name='usuario' placeholder='Nombre de usuario'><br>
    <input type='text' name='nombre' placeholder='Nombre'><br>
    <input type='text' name='apellido' placeholder='Apellido'><br>
    <input type='text' name='dni' placeholder='DNI'><br>
    <input type='text' name='email' placeholder='Email'><br>
    <input type='text' name='telefono' placeholder='Telefono'><br>
    <input type='password' name='pass' placeholder='Contraseña'><br>
    <label>Selecciona el tipo de usuario: 0-Administradores,1-Jefe de Equipo, 2-Empleados</label><br>
    Tipo de usuario: <select name = 'tipo'>
    <option></option>
    <option>0</option>
    <option>1</option>
    <option>2</option>
    </select><br>
    <input type = 'submit' value = 'Crear'>
    </form>";

echo "<hr>";

//MODIFICAR USUARIOS
echo "<h4>Modificar Usuarios:</h4>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'],$_POST['nueva_pass'], $_POST['nuevo_nombre'], $_POST['nuevo_apellido'],$_POST['nuevo_dni'],$_POST['nuevo_email'],
$_POST['nuevo_telefono'], $_POST['nuevo_tipo'])) {

    $usuario = $_POST['id_usuario'];
    $nueva_pass = $_POST['nueva_pass'];
    $nuevo_nombre = $_POST['nuevo_nombre'];
    $nuevo_apellido = $_POST['nuevo_apellido'];
    $nuevo_dni = $_POST['nuevo_dni'];
    $nuevo_email = $_POST['nuevo_email'];
    $nuevo_telefono = $_POST['nuevo_telefono'];
    $nuevo_tipo = $_POST['nuevo_tipo'];

    if (empty($usuario) ||empty($nueva_pass) ||empty($nuevo_nombre) ||empty($nuevo_apellido) ||empty($nuevo_dni) ||empty($nuevo_email) ||empty($nuevo_telefono) || $nuevo_tipo === '') {
        echo "<p style='color: red;'>Error: Rellena todos los campos.</p>";
    } else {
        modificar_usuarios($con, $usuario,$nueva_pass,$nuevo_nombre,$nuevo_apellido,$nuevo_dni,$nuevo_email,$nuevo_telefono, $nuevo_tipo);
        echo "<p style='color: green;'>Usuario modificado correctamente.</p>";
        header("Location: administradores.php");
        exit(); 
    }
}

echo   "<label>Introduce el ID del usuario que quieres modificar:</label>";
echo   "<form method='POST' action=''>
        ID Usuario:<input type='text' name='id_usuario'><br>
        <input type='text' name='nuevo_nombre' placeholder='Nuevo Nombre'><br>
        <input type='text' name='nuevo_apellido' placeholder='Nuevo Apellido'><br>
        <input type='text' name='dni' placeholder='Nuevo DNI'><br>
        <input type='text' name='email' placeholder='Nuevo Email'><br>
        <input type='text' name='telefono' placeholder='Nuevo Telefono'><br>
        <input type='password' name='nueva_pass' placeholder='Nueva Contraseña'><br>
        Nuevo tipo: <select name='nuevo_tipo'>
        <option value='0'>Administrador</option>
        <option value='1'>Jefe Equipo</option>
        <option value='2'>Empleado</option>
        </select><br>
        <input type='submit' value='Modificar'>
        </form>";

// BORRAR USUARIOS
echo "<h4>Borrar usuarios:</h4>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
    $usuario = $_POST['id_usuario'];

    if (empty($usuario)) {
        echo "<p style='color: red;'>Error: El campo ID de usuario es obligatorio.</p>";
    } else {
        borrar_usuario($con, $usuario);
        echo "<p style='color: green;'>Usuario Borrado Correctamente.</p>";
        header("Location: administradores.php");
    }
}

echo "<label>Introduce el ID del usuario que quieres borrar:</label>";
echo "<form method='POST' action=''>";
echo "<input type='text' name='id_usuario' placeholder='ID USUARIO'><br>";
echo "<input type='submit' value='Borrar'>";
echo "</form>";


?>