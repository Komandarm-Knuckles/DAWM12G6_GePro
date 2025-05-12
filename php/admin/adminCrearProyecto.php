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

// Eliminar proyecto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_proyecto'])) {
    $id_proyecto = $_POST['id_proyecto'];

    if (borrar_proyecto($con, $id_proyecto)) {
        header("Location: " . $_SERVER['PHP_SELF']); 
        exit();
    } else {
        $error = "Error al eliminar el proyecto.";
    }
}

// Editar proyecto
if (isset($_POST['editar_proyecto'])) {
    $id_proyecto = $_POST['id_proyecto'];
    header("Location: editarProyectos.php?id=" . $id_proyecto);
    exit;
}

$result_proyectos = obtener_todos_proyectos($con);
?>

<head>
    <title>Panel de Proyectos Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex flex-col w-full max-w-[90%] justify-center items-center gap-20 pt-20">
 
        <!-- Crear Nuevo Proyecto -->
        <button type="button" onclick="window.location.href='adminCrearProyecto.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">CREAR PROYECTO</button>

        <div class="flex flex-col gap-10 p-10 w-full md:max-w-[85%] bg-gray-300 justify-center items-center">
            <h2 class="font-bold text-orange-400 text-2xl text-center underline"> CREAR NUEVO PROYECTO</h2>
            
            
            <?php if (isset($error)) { echo "<p class='text-red-600'>$error</p>"; } ?>
            <form method="POST" action="" class="flex flex-col w-full md:max-w-[85%]  justify-center items-center  gap-6">
                <input type="text" name="nombre" placeholder="Nombre del Proyecto" required class="p-2 w-full border rounded" />

                <textarea name="descripcion" placeholder="Descripción" required class="p-2 border w-full rounded"></textarea>
                Fecha Inicio:
                <input type="date" name="fecha_inicio" required class="p-2 border w-full rounded" />
                Fecha Fin:
                <input type="date" name="fecha_fin" class="p-2 border w-full rounded" />
                Estado:
                <select name="estado" class="rounded-lg w-full p-1">
                    <option value="pendiente">Pendiente</option>
                    <option value="en proceso">En proceso</option>
                    <option value="completado">Completado</option>
                </select>

                <button type="submit" name="crear_proyecto" class="p-2 w-[15em] bg-orange-400 hover:bg-orange-700 cursor-pointer text-white rounded-xl">Crear Nuevo Proyecto</button>
            </form>
            <span class="block h-0.5 w-full bg-black opacity-40"></span>

            <!-- Botones de volver y logOut -->
            <div class="flex justify-center items-center gap-10">
                <form action="../logout.php" method="POST" class="p-5 flex md:flex-row flex-col gap-10">
                <button type="button" onclick="window.location.href='administradores.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Volver al Panel de Administrador</button>
                <button type="button" onclick="window.location.href='adminUsuarios.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Volver al Panel de Usuarios</button>
                </form>
            </div>
        </div>
    </div>

    <?php
    $con->close();
    ?>
</body>
