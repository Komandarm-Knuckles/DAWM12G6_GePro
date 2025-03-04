<?php
require_once("database.php");
session_start();
$con = crearConexion();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador - Gestión de Usuarios</title>
    <link rel="stylesheet" type="text/css" href="../css/styles.css">
<body>
    <h1>Bienvenido a la Página de Administrador</h1>

    <h2 style='color:blue;'>Gestión de Usuarios:</h2>
    <?php
    $usuarios = obtener_todos_los_usuarios($con);

    // MOSTRAR TABLA USUARIOS
    echo "<h4>Usuarios registrados:</h4>";
    if ($usuarios->num_rows === 0) {
        echo "<p>No se encuentran usuarios</p>";
    } else {
        echo "<table class='styled-table'>
            <thead>
                <tr>
                    <th>USUARIO</th><th>CONTRASEÑA</th><th>NOMBRE</th><th>APELLIDO</th><th>DNI</th><th>EMAIL</th><th>TELEFONO</th><th>TIPO</th>
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
                    <td>$tipo</td>
                  </tr>";
        }
        
        echo "</tbody></table>";
    }
    
// CREAR USUARIOS
echo "<h4>Dar de alta nuevos usuarios:</h4>";
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
?>
<form method='POST' action=''>
    <input type='text' name='usuario' placeholder='Nombre de usuario' required><br>
    <input type='text' name='nombre' placeholder='Nombre' required><br>
    <input type='text' name='apellido' placeholder='Apellido' required><br>
    <input type='text' name='dni' placeholder='DNI' required><br>
    <input type='email' name='email' placeholder='Email' required><br>
    <input type='number' name='telefono' placeholder='Telefono' required><br>
    <input type='password' name='pass' placeholder='Contraseña' required><br>
    <label>Selecciona el tipo de usuario:</label><br>
    <select name='tipo' required>
        <option value=''>Selecciona...</option>
        <option value='0'>Administrador</option>
        <option value='1'>Jefe de Equipo</option>
        <option value='2'>Empleado</option>
    </select><br>
    <input type='submit' value='Crear'>
</form>

<hr>

<!-- MODIFICAR USUARIOS -->
<h4>Modificar Usuarios:</h4>
<?php
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
?>
<form method='POST' action=''>
    <input type='text' name='usuario' placeholder='Usuario a modificar' required><br>
    <input type='text' name='nuevo_nombre' placeholder='Nuevo Nombre' required><br>
    <input type='text' name='nuevo_apellido' placeholder='Nuevo Apellido' required><br>
    <input type='text' name='nuevo_dni' placeholder='Nuevo DNI' required><br>
    <input type='email' name='nuevo_email' placeholder='Nuevo Email' required><br>
    <input type='number' name='nuevo_telefono' placeholder='Nuevo Telefono' required><br>
    <input type='password' name='nueva_pass' placeholder='Nueva Contraseña' required><br>
    <label>Nuevo tipo de usuario:</label><br>
    <select name='nuevo_tipo' required>
        <option value='0'>Administrador</option>
        <option value='1'>Jefe de Equipo</option>
        <option value='2'>Empleado</option>
    </select><br>
    <input type='submit' value='Modificar'>
</form>

<hr>

<!-- BORRAR USUARIOS -->
<h4>Borrar usuarios:</h4>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario_borrar'])) {
    $usuario = $_POST['usuario_borrar'];

    if (empty($usuario)) {
        echo "<p style='color: red;'>Error: El campo usuario es obligatorio.</p>";
    } else {
        if (borrar_usuario($con, $usuario)) {
            header("Location: administradores.php");
            exit();
        } else {
            echo "<p style='color: red;'>Error al borrar usuario.</p>";
        }
    }
}
?>
<form method='POST' action=''>
    <input type='text' name='usuario_borrar' placeholder='Usuario a borrar' required><br>
    <input type='submit' value='Borrar'>
</form>
