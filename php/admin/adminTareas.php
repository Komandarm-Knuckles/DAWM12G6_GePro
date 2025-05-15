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

// Manejar la eliminación de tareas
if (isset($_POST['eliminar_tarea'])) {
    $id_tarea = $_POST['eliminar_tarea'];
    if (borrar_tarea($con, $id_tarea)) {
        echo "<script>alert('Tarea eliminada con éxito.');</script>";
        $result_tareas = obtener_todas_las_tareas($con);
    } else {
        echo "<script>alert('Error al eliminar la tarea.');</script>";
    }
}

// Manejar la edición de tareas
if (isset($_POST['editar_tarea'])) {
    $id_tarea = $_POST['id_tarea'];
    header("Location: editarTareas.php?id=" . $id_tarea);
    exit;
}

// Crear tarea
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_tarea'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_proyecto = $_POST['id_proyecto'];
    $usuario_asignado = $_POST['usuario_asignado'];
    $fecha_asignacion = date("Y-m-d");
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $estado = "pendiente";

    $stmt = $con->prepare("INSERT INTO tareas (nombre, descripcion, id_proyecto, usuario, fecha_asignacion, fecha_vencimiento, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissss", $nombre, $descripcion, $id_proyecto, $usuario_asignado, $fecha_asignacion, $fecha_vencimiento, $estado);

    if ($stmt->execute()) {
        echo "<script>alert('Tarea creada con éxito.');</script>";
        $result_tareas = obtener_todas_las_tareas($con);
    } else {
        echo "<script>alert('Error al crear la tarea: " . $stmt->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Tareas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-full bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex w-full min-h-screen justify-center items-center">
        <div class="flex w-full md:flex-row flex-col justify-center items-stretch max-w-[90%]">

            <!-- Menú lateral -->
            <section class="flex md:flex-col md:w-80 w-full flex-wrap md:justify-start justify-center items-center bg-orange-400 md:gap-10 gap-5 pt-5">
                <img class="md:w-13 w-[10em]" src="../../img/LogoEmpresa.png" alt="logo Empresa"/>
                <div class="flex gap-2">
                    <img src="../../img/users.svg" alt="imagenProyectos"/>
                    <a href="adminUsuarios.php" class="font-bold text-white text-lg">Usuarios</a>
                </div>
                <div class="flex gap-2">
                    
                    <img src="../../img/folder-git-2.svg" alt="imagenReuniones"/>
                    <a href="adminProyectos.php" class="font-bold text-white text-lg">Proyectos</a>
                </div>
                <div class="flex gap-2">
                    <img src="../../img/projector.svg" alt="imagentareas"/>
                    <a href="adminReuniones.php" class="font-bold text-white text-lg">Reuniones</a>
                </div>
            </section>

            <!-- Contenido principal -->
            <div class="flex flex-col py-5 min-h-screen gap-6 justify-center items-center bg-gray-300 w-full">


                <h1 class='font-bold text-4xl text-orange-400'>GESTIÓN DE TAREAS</h1>
                <h3 class='text-xl font-bold text-orange-400'>Tareas registradas:</h3>
               
                <?php if ($result_tareas->num_rows === 0): ?>
                    <p>No hay tareas registradas.</p>
                <?php else: ?>
                    <div class="max-h-[300px] overflow-y-auto shadow-2xl w-full">
                        <table class="styled-table w-full">
                            <thead>
                                <tr class="sticky bg-orange-400 text-white top-0">
                                    <th class="p-3">ID</th>
                                    <th class="p-3">Nombre</th>
                                    <th class="p-3">Descripción</th>
                                    <th class="p-3">Estado</th>
                                    <th class="p-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($tarea = $result_tareas->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center p-4 font-semibold"><?= $tarea['id_tarea'] ?></td>
                                        <td class="text-center p-4 font-semibold"><?= htmlspecialchars($tarea['nombre']) ?></td>
                                        <td class="text-center p-4 font-semibold"><?= htmlspecialchars($tarea['descripcion']) ?></td>
                                        <td class="text-center p-4 font-semibold"><?= htmlspecialchars($tarea['estado']) ?></td>
                                        <td class="flex justify-center items-center gap-3 pt-4">
                                            <form method="POST" action="">
                                                <input type="hidden" name="id_tarea" value="<?= $tarea['id_tarea'] ?>">
                                                <button type="submit" name="editar_tarea" class="cursor-pointer">
                                                    <img src='../../img/square-pen.png' alt='Editar' style='width: 20px; height: 20px;' class='hover:bg-green-500 hover:scale-105' />
                                                </button>
                                            </form>
                                            <form method="POST" action="">
                                                <input type="hidden" name="eliminar_tarea" value="<?= $tarea['id_tarea'] ?>">
                                                <button type="submit" onclick="return confirm('¿Estás seguro de que quieres eliminar esta tarea?');" class="cursor-pointer">
                                                    <img src="../../img/trash-2.png" alt="Eliminar" style="width: 20px; height: 20px;" class="hover:bg-red-500 hover:scale-105">
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Botón crear tarea -->
                <button type="button" onclick="window.location.href='adminCrearTarea.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">CREAR TAREA</button>

                <!-- Botones de navegación -->
                <div class="flex justify-center items-center gap-10">
                    <form action="../logout.php" method="POST" class="p-5 flex md:flex-row flex-col gap-10">
                        <button type="button" onclick="window.location.href='administradores.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-fit p-3 shadow-lg">Panel de Administrador</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php $con->close(); ?>
</body>
</html>
