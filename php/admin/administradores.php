<?php

// control de inicio de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: index.php");
    exit();
}
require_once("../database.php");


$usuario = $_SESSION['usuario'];
$con = crearConexion();


$sql_tipo = "SELECT tipo FROM usuarios WHERE usuario = ?";
$stmt = $con->prepare($sql_tipo);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$tipo_usuario = $result->fetch_assoc()['tipo'];

if ($tipo_usuario != 0) {
    $_SESSION['error'] = "No tienes permisos de administrador";;
    exit();
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex flex-col gap-10 rounded-xl p-10 w-200 max-w-[90%] bg-gray-300 justify-center items-center">
        <div class="flex flex-col items-center gap-6">   
            <h1 class="font-bold md:text-5xl text-xl underline text-center">Bienvenid@, <?php echo htmlspecialchars($usuario); ?></h1>
            <h2 class="font-bold text-lg md:text-3xl underline text-center">Elige una de las opciones:</h2>
        </div>
        <div class="flex flex-col w-full gap-5 items-center">
            <a href="adminUsuarios.php" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white text-center">
                Usuarios
            </a>
            <span class="block h-0.5 w-full md:w-[20em] bg-black opacity-40"></span>
            <a href="adminCrearUsuario.php" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white text-center">
                Crear Usuarios
            </a>
            <span class="block h-0.5 w-full md:w-[20em] bg-black opacity-40"></span>
            <a href="adminProyectos.php" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white text-center">
                Proyectos
            </a>
            <span class="block h-0.5 w-full md:w-[20em] bg-black opacity-40"></span>
            <a href="adminReuniones.php" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white text-center">
                Reuniones
            </a>
            <span class="block h-0.5 w-full md:w-[20em] bg-black opacity-40"></span>
            <a href="adminTareas.php" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white text-center">
                Tareas
            </a>
            <span class="block h-0.5 w-full md:w-[20em] bg-black opacity-40"></span>
        </div>
        <form action="../logout.php" method="POST">
            <button type="submit" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-40 shadow-lg cursor-pointer font-bold text-white">Cerrar Sesión</button>
        </form>
    </div>
</body>
</html>
