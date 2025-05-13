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

// Obtener todas las tareas
$result_tareas = obtener_todas_las_tareas($con);


// Crear tarea (igual que en jefes-equipo.php)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_tarea'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_proyecto = $_POST['id_proyecto'];
    $usuario_asignado = $_POST['usuario_asignado'];
    $fecha_asignacion = date("Y-m-d"); // Fecha de asignación actual
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $estado = "pendiente"; // Estado inicial

    // Validar fecha vencimiento (controlando el error de poner fecha actual)
    if (strtotime($fecha_vencimiento) <= strtotime($fecha_asignacion)) {
        echo "<script>alert('La fecha de vencimiento debe ser posterior a hoy.');
             window.location.href = window.location.href; </script>";
        exit();
    }

    $stmt = $con->prepare("INSERT INTO tareas (nombre, descripcion, id_proyecto, usuario, fecha_asignacion, fecha_vencimiento, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissss", $nombre, $descripcion, $id_proyecto, $usuario_asignado, $fecha_asignacion, $fecha_vencimiento, $estado);
    
    if ($stmt->execute()) {
        echo "<script>alert('Tarea creada con éxito.');</script>";
        $result_tareas = obtener_todas_las_tareas($con);
    } else {
        echo "<script>alert('Error al crear la tarea: " . $stmt->error . "');</script>";
    }
}


// Obtener la lista de usuarios y proyectos para los desplegables
$result_usuarios = obtener_todos_los_usuarios($con);
$result_proyectos = $con->query("SELECT id_proyecto, nombre FROM proyectos");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Panel de Tareas Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body
    class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex flex-col w-full md:max-w-[65%] max-w-[95%] justify-center items-center gap-20 pt-20">
        <div class="flex flex-col gap-10 p-10 w-full max-w-full bg-gray-300 justify-center items-center">
            <h2 class="font-bold text-orange-400 text-3xl underline">Crear Nueva Tarea</h2>
            <form method="POST" action="" class="flex flex-col w-full justify-center items-center gap-6">
                <laber for="bombre">Nombre de la tarea:</laber>
                <input type="text" name="nombre" placeholder="Nombre" required class="p-2 border rounded w-full" />
                <laber for="descripcion">Descripción de la tarea: </laber>
                <textarea name="descripcion" placeholder="Descripción" required class="p-2 border rounded w-full"></textarea>
                <laber for="id_proyecto">Asignación de Proyecto: </laber>
                <select name="id_proyecto" class="p-2 rounded w-full">
                    <?php while ($proyecto = $result_proyectos->fetch_assoc()) { ?>
                        <option value="<?php echo $proyecto['id_proyecto']; ?>">
                            <?php echo htmlspecialchars($proyecto['nombre']); ?></option>
                    <?php } ?>
                </select>
                <laber for="usuario_asignado">Asignación de Usuario: </laber>
                <select name="usuario_asignado" class="p-2 rounded w-full">
                    <?php while ($usuario = $result_usuarios->fetch_assoc()) { ?>
                        <option value="<?php echo $usuario['usuario']; ?>">
                            <?php echo htmlspecialchars($usuario['usuario']); ?></option>
                    <?php } ?>
                </select>
                <laber for="fecha_vencimiento">Fecha de vencimiento de la tarea: </laber>
                <input type="date" name="fecha_vencimiento" class="p-2 rounded w-full" class="p-2 border rounded" required />
                <button type="submit" name="crear_tarea"
                    class="p-2 bg-orange-400 hover:bg-orange-700 cursor-pointer text-white w-[15em] rounded-xl">Crear Nueva Tarea
                </button>
            </form>
            <span class="block h-0.5 w-full bg-black opacity-40"></span>

            <!-- Boton de volver -->
            <div class="flex justify-center items-center gap-10">
                <form action="../logout.php" method="POST" class="p-5 flex flex-col md:flex-row gap-10">
                    <button type="button" onclick="history.back()" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Volver</button>
                </form>
            </div>

            <!-- Botones de volver a panel administrador o panel usuario -->
            <div class="flex justify-center items-center gap-10">
                <form action="../logout.php" method="POST" class="p-5 flex md:flex-row flex-col gap-10">
                <button type="button" onclick="window.location.href='administradores.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Volver al Panel de Administrador</button>
                <button type="button" onclick="window.location.href='adminUsuarios.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Volver al Panel de Usuarios</button>
                </form>
            </div>
        </div>

        <?php
        $con->close();
        ?>

        <?php

