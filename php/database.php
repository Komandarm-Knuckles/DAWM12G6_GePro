<?php
function crearConexion() {
    $host = "localhost";
    $username = "root"; 
    $password = ""; 
    $dbname = "gepro";

    $con = new mysqli($host, $username, $password, $dbname);

    if ($con->connect_error) {
        die("Error de conexión: " . $con->connect_error);
    }
    return $con;
}

function obtener_todos_los_usuarios($con) {
    $sql = "SELECT * FROM usuarios";
    return $con->query($sql);
}

function obtener_resultados($resultado) {
    return $resultado->fetch_assoc();
}
function crear_usuario($con, $usuario, $pass, $nombre, $apellido, $dni, $email, $telefono, $tipo) {
    // Comprobar si el usuario ya existe
    $check = $con->prepare("SELECT usuario FROM usuarios WHERE usuario = ?");
    $check->bind_param("s", $usuario);
    $check->execute();
    $resultado = $check->get_result();

    if ($resultado->num_rows > 0) {
        echo "<script>alert('El usuario ya existe, cambia el nombre de usuario, Por Favor!')</script>";
        return false; 
    }

    // Crear usuario si no existe
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $con->prepare("INSERT INTO usuarios (usuario, pass, nombre, apellido, dni, email, telefono, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $usuario, $hashed_pass, $nombre, $apellido, $dni, $email, $telefono, $tipo);
    return $stmt->execute();
}



function modificar_usuarios($con, $usuario, $nueva_pass, $nuevo_nombre, $nuevo_apellido, $nuevo_dni, $nuevo_email, $nuevo_telefono, $nuevo_tipo) {
    $hashed_pass = password_hash($nueva_pass, PASSWORD_DEFAULT);
    $stmt = $con->prepare("UPDATE usuarios SET pass=?, nombre=?, apellido=?, dni=?, email=?, telefono=?, tipo=? WHERE usuario=?");
    $stmt->bind_param("ssssssis", $hashed_pass, $nuevo_nombre, $nuevo_apellido, $nuevo_dni, $nuevo_email, $nuevo_telefono, $nuevo_tipo, $usuario);
    return $stmt->execute();
}

function borrar_usuario($con, $usuario) {
    $stmt = $con->prepare("DELETE FROM usuarios WHERE usuario=?");
    $stmt->bind_param("s", $usuario);
    return $stmt->execute();
}
?>


