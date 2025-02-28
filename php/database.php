<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "GePro";

function crearConexion(){
    $con = mysqli_connect($GLOBALS["host"], $GLOBALS["user"],$GLOBALS["pass"]);
    if (!$con) {
        die("Error al conectar con la Base de Datos de GePro" . mysqli_connect_error());
    }

    crearDB($con);
    mysqli_select_db($con,$GLOBALS["dbname"]);
    crear_tabla_usuarios($con);
    crear_tabla_jefeEquipo($con);
    crear_tabla_administradores($con);
    crear_tabla_grupos($con);
    crear_tabla_proyectos($con);
    crear_tabla_reuniones($con);
    crear_tabla_tareas($con);
    // drop_database_GePro($con);
    return $con;

}

    function crearDB($con){
        $query = "CREATE DATABASE IF NOT EXISTS GePro";
        mysqli_query($con, $query) or die ("Error al crear la base de datos" . mysqli_error($con)); 
    }

    function crear_tabla_usuarios($con){
        $query = "CREATE TABLE IF NOT EXISTS USUARIOS (
            usuario VARCHAR(255) PRIMARY KEY,
            pass VARCHAR(255) NOT NULL,
            nombre VARCHAR(255) NOT NULL,
            apellido VARCHAR(255) NOT NULL,
            dni VARCHAR(9) NOT NULL,
            email VARCHAR(255) NOT NULL,
            telefono INT NOT NULL,
            tipo INT NOT NULL
        )
    ";
    mysqli_query($con, $query) or die ("Error al crear la tabla usuarios" . mysqli_error($con)); 
    }

    function crear_tabla_jefeEquipo($con){
        $query = " CREATE TABLE IF NOT EXISTS jefeEquipo (
            id_jefeEquipo VARCHAR(255) NOT NULL PRIMARY KEY,
            nombre VARCHAR(255) NOT NULL,
            apellido VARCHAR(255) NOT NULL,
            dni VARCHAR(9) NOT NULL,
            email VARCHAR(255) NOT NULL,
            telefono INT NOT NULL
        )
    ";
        mysqli_query($con, $query) or die ("Error al crear la tabla jefeEquipo" . mysqli_error($con)); 
    }

    function crear_tabla_administradores($con){
        $query = "CREATE TABLE IF NOT EXISTS ADMINISTRADORES (
            id_admin INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(255) NOT NULL,
            apellido VARCHAR(255) NOT NULL,
            mail VARCHAR(255) NOT NULL,
            telefono INT NOT NULL
        )
    ";
        mysqli_query($con, $query) or die ("Error al crear la tabla administradores" . mysqli_error($con)); 
    }

    function crear_tabla_grupos($con){
        $query = "CREATE TABLE IF NOT EXISTS GRUPOS (
            id_grupos INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(255) NOT NULL,
            jefeEquipo VARCHAR(255) NOT NULL,
            usuarios VARCHAR(255) NOT NULL,
            FOREIGN KEY (jefeEquipo) REFERENCES jefeEquipo(id_jefeEquipo)
        )";
        mysqli_query($con, $query) or die ("Error al crear la tabla grupos" . mysqli_error($con)); 
    }
    
    function crear_tabla_proyectos($con){
        $query = "CREATE TABLE IF NOT EXISTS PROYECTOS (
            id_proyectos INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(255) NOT NULL,
            grupos INT NOT NULL,
            FOREIGN KEY (grupos) REFERENCES GRUPOS(id_grupos)
        )";
        mysqli_query($con, $query) or die ("Error al crear la tabla proyectos" . mysqli_error($con)); 
    }
    
    function crear_tabla_reuniones($con){
        $query = "CREATE TABLE IF NOT EXISTS REUNIONES (
            id_reuniones INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(255) NOT NULL,
            grupos INT NOT NULL,
            usuarios VARCHAR(255) NOT NULL,
            FOREIGN KEY (grupos) REFERENCES GRUPOS(id_grupos),
            FOREIGN KEY (usuarios) REFERENCES USUARIOS(usuario)
        )";
        mysqli_query($con, $query) or die ("Error al crear la tabla reuniones" . mysqli_error($con)); 
    }
    
    function crear_tabla_tareas($con){
        $query = "CREATE TABLE IF NOT EXISTS TAREAS (
            id_tareas INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(255) NOT NULL,
            usuarios VARCHAR(255) NOT NULL,
            id_proyectos INT NOT NULL,
            FOREIGN KEY (usuarios) REFERENCES USUARIOS(usuario),
            FOREIGN KEY (id_proyectos) REFERENCES PROYECTOS(id_proyectos)
        )";
        mysqli_query($con, $query) or die ("Error al crear la tabla tareas" . mysqli_error($con)); 
    }

    function drop_database_GePro($con){
        $query = "DROP DATABASE IF EXISTS GePro";
        mysqli_query($con, $query) or die ("Error al borrar la base de datos" . mysqli_error($con)); 
    }

//------------------------------------------ FUNCIONES -----------------------------------------------------

function crear_usuario($con, $usuario, $pass, $nombre, $apellido, $dni, $email, $telefono, $tipo) {
    $usuario = mysqli_real_escape_string($con, $usuario);
    $pass = password_hash(mysqli_real_escape_string($con, $pass), PASSWORD_DEFAULT); 
    $nombre = mysqli_real_escape_string($con, $nombre);
    $apellido = mysqli_real_escape_string($con, $apellido);
    $dni = mysqli_real_escape_string($con, $dni);
    $email = mysqli_real_escape_string($con, $email);
    $telefono = intval($telefono); 
    $tipo = intval($tipo); 
    $query = "INSERT INTO USUARIOS (usuario, pass, nombre, apellido, dni, email, telefono, tipo) VALUES ('$usuario', '$pass', '$nombre', '$apellido', '$dni', '$email', $telefono, $tipo)";
    mysqli_query($con, $query) or die("Error al crear el usuario: " . mysqli_error($con));
}

function obtener_usuarios($con, $usuarios) {
    $usuario = mysqli_real_escape_string($con, $usuarios);
    $query = "SELECT * FROM USUARIOS WHERE usuario = '$usuario'";
    $resultado = mysqli_query($con, $query);

    if (mysqli_num_rows($resultado) > 0) {
        return mysqli_fetch_assoc($resultado);
    } else {
        return null;
    }
}

function obtener_todos_los_usuarios($con) {
    $query = "SELECT * FROM USUARIOS";
    $resultado = mysqli_query($con, $query);

    if ($resultado) {
        return $resultado;
    } else {
        return null;
    }
}

function modificar_usuarios($con, $usuario, $nombre, $apellido, $dni, $email, $telefono, $tipo) {
    $usuario = mysqli_real_escape_string($con, $usuario);
    $nombre = mysqli_real_escape_string($con, $nombre);
    $apellido = mysqli_real_escape_string($con, $apellido);
    $dni = mysqli_real_escape_string($con, $dni);
    $email = mysqli_real_escape_string($con, $email);
    $telefono = intval($telefono);
    $tipo = intval($tipo);

    $query = "UPDATE USUARIOS SET nombre = '$nombre', apellido = '$apellido', dni = '$dni', email = '$email', telefono = $telefono, tipo = $tipo WHERE usuario = '$usuario'";
    mysqli_query($con, $query) or die("Error al modificar el usuario: " . mysqli_error($con));
}

function borrar_usuarios($con, $usuario) {
    $usuario = mysqli_real_escape_string($con, $usuario);
    $query = "DELETE FROM USUARIOS WHERE usuario = '$usuario'";
    mysqli_query($con, $query) or die("Error al borrar el usuario: " . mysqli_error($con));
}

function obtener_num_filas($resultado){
    return mysqli_num_rows($resultado);
}

function obtener_resultados($resultado){
    return mysqli_fetch_all($resultado, MYSQLI_ASSOC);
}

function cerrar_conexion($con){
    mysqli_close($con);
}
?>