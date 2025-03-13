<?php

// control de inicio de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: index.php");
    exit();
}
require_once("database.php");


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
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../img/pixels14.jpg')]">
    <div class="flex flex-col gap-10 rounded-xl p-10 w-200 max-w-[80%] bg-gray-300 justify-center items-center">
        <div class="flex flex-col items-center gap-6">   
            <h1 class="font-bold text-5xl underline">Bienvenid@ , <?php echo htmlspecialchars($usuario); ?></h1>
            <h2 class="font-bold text-3xl underline">Elige una de las opciones:</h2>
        </div>
        <div class="flex flex-col gap-5 items-center">
            <button class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white">
                <a href="adminUsuarios.php">Mostrar Usuarios</a href="">
            </button>
            <span class="block h-0.5 w-100 bg-black opacity-40"></span>
            <button class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white">
                <a href="adminCrearUsuario.php">Crear Usuarios</a href="">
            </button>
            <span class="block h-0.5 w-100 bg-black opacity-40"></span>
            <button class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white">
                <a href="adminGrupos.php">Grupos de Usuarios</a href="">
            </button>
            <span class="block h-0.5 w-100 bg-black opacity-40"></span>
            <button class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white">
                <a href="adminProyectos.php">Proyectos</a href="">
            </button>
            <span class="block h-0.5 w-100 bg-black opacity-40"></span>
            <button class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white">
                <a href="adminReuniones.php">Reuniones</a href="">
            </button>
            <span class="block h-0.5 w-100 bg-black opacity-40"></span>
            <button class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white">
                <a href="adminTareas.php">Tareas</a href="">
            </button>
            <span class="block h-0.5 w-100 bg-black opacity-40"></span>
        </div> 
        <form action="logout.php" method="POST">
            <button type="submit" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-40 shadow-lg cursor-pointer font-bold text-white">Cerrar Sesión</button>
        </form>
        
    </div>

</body>
</html>
