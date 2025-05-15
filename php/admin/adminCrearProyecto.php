<?php

// Control de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: index.php");
    exit();
}

require_once("../database.php");
$con = crearConexion();

// Envio del formulario proyecto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_proyecto'])) {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null; 
    $estado = $_POST['estado'];

    // Validar fecha fin (controlando el error de poner fecha actual)
    if (strtotime($fecha_fin) <= strtotime($fecha_inicio)) {
        echo "<script>alert('La fecha de fin no puede ser la de hoy.');
             window.location.href = window.location.href; </script>";
        exit();
    }

    if (!empty($nombre) && !empty($descripcion) && !empty($fecha_inicio) && !empty($estado)) {
        if (crear_proyecto($con, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado)) {
            header("Location: " . $_SERVER['PHP_SELF']); 
            exit();
        } else {
            $error = "Error al crear el proyecto.";
        }
    } else {
        $error = "Todos los campos son obligatorios excepto la fecha de fin.";
    }
}

$result_proyectos = obtener_todos_proyectos($con);
?>

<head>
    <title>Panel de Proyectos Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
        <div class="flex flex-col max-w-[90%] w-[40em] bg-gray-300 p-8 rounded shadow-xl gap-6">
            <h1 class="text-4xl font-bold text-center underline text-orange-400"> CREAR NUEVO PROYECTO</h1>
            <div class="flex justify-center items-center">
            <span class="block h-0.5 w-130 bg-black opacity-40"></span>
            </div>
            <div class="flex flex-col justify-center text-center items-center">
            <?php if (isset($error)) { echo "<p class='text-red-600'>$error</p>"; } ?>
            <form method="POST" action="" class="flex flex-col w-full md:max-w-[85%]  justify-center items-center  gap-6">
                Nombre:
                <input type="text" name="nombre" placeholder="Nombre del Proyecto" required class="p-2 w-full border rounded placeholder-center" />
                Descripción:
                <textarea name="descripcion" placeholder="Descripción" required class="p-2 border w-full rounded"></textarea>
                Fecha Inicio:
                <input type="date" name="fecha_inicio" required class="p-2 border text-center w-full rounded" />
                Fecha Fin:
                <input type="date" name="fecha_fin" class="p-2 border text-center w-full rounded" />
                Estado:
                <select name="estado" class="rounded-lg border-2 text-center w-full p-1">
                    <option value="pendiente">Pendiente</option>
                    <option value="en proceso">En proceso</option>
                    <option value="completado">Completado</option>
                </select>

                <button type="submit" name="crear_proyecto" class="p-2 w-[15em] bg-orange-400 hover:bg-orange-700 cursor-pointer text-white rounded-xl">Crear Nuevo Proyecto</button>
                <!-- Boton de volver -->
            <div class="flex justify-center items-center ">
                <button type="button" onclick="window.location.href='adminProyectos.php'" class="bg-orange-400 text-white p-2 rounded-xl w-[10em] items-center cursor-pointer hover:bg-orange-700 font-bold">Volver</button>
            </div>    
            </form>
        
            <span class="block h-0.5 w-full bg-black opacity-40"></span>
            
            <!-- Botones de volver a panel administrados-->
            <div class="flex justify-center items-center gap-10">
                <form action="../logout.php" method="POST" class="p-5 flex md:flex-row flex-col">
                <button type="button" onclick="window.location.href='administradores.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-fit p-3 shadow-lg">Panel de Administrador</button>
                </form>
            </div>
            </div>
        </div>
   

    <?php
    $con->close();
    ?>
</body>
