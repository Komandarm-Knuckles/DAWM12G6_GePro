<?php
include '../database.php';

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

// Crear tarea (igual que en jefes-equipo.php)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_tarea'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_proyecto = $_POST['id_proyecto'];
    $usuario_asignado = $_POST['usuario_asignado'];
    $fecha_asignacion = date("Y-m-d"); // Fecha de asignación actual
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $estado = "pendiente"; // Estado inicial

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
    <div class="flex flex-col w-full max-w-[90%] justify-center items-center gap-20 pt-20">
        <div class="flex flex-col p-10 w-full max-w-[65%] gap-5 bg-gray-300 rounded">
            <div class="flex justify-center items-center bg-gray-300">
                <h2 class="font-bold text-orange-400 text-3xl underline">Tareas</h2>
            </div>
            <div
                class='flex flex-col bg-gray-300 max-h-[300px] text-center gap-5 overflow-y-auto shadow-2xl bg-color w-full'>
                <table class='styled-table w-full p-4 text-center rounded'>
                    <tr class='sticky bg-orange-400 text-white top-0 p-4 bg-gray-300'>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    <?php while ($tarea = $result_tareas->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $tarea['id_tarea']; ?></td>
                            <td><?php echo htmlspecialchars($tarea['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($tarea['descripcion']); ?></td>
                            <td><?php echo htmlspecialchars($tarea['estado']); ?></td>
                            <td class="flex justify-center items-center gap-3 pt-4">
                                <form method="POST" action="">
                                    <input type="hidden" name="id_tarea" value="<?php echo $tarea['id_tarea']; ?>">
                                    <button type="submit" name="editar_tarea" class="cursor-pointer">
                                        <img src='../../img/square-pen.png' alt='Editar' style='width: 20px; height: 20px;'
                                            class='hover:bg-green-500 hover:scale-105' />
                                    </button>
                                </form>

                                <form method="POST" action="">
                                    <input type="hidden" name="eliminar_tarea" value="<?php echo $tarea['id_tarea']; ?>">
                                    <button type="submit"
                                        onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');"
                                        class="cursor-pointer">
                                        <img src="../../img/trash-2.png" alt="Eliminar" style="width: 20px; height: 20px;"
                                            class="hover:bg-red-500 hover:scale-105">
                                    </button>
                                </form>

                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

        <div class="flex flex-col gap-10 p-10 w-full max-w-[65%] bg-gray-300 justify-center items-center">
            <h2 class="font-bold text-orange-400 text-3xl underline">Crear Nueva Tarea</h2>
            <form method="POST" action="" class="flex flex-col w-full gap-6">
                <input type="text" name="nombre" placeholder="Nombre" required class="p-2 border rounded" />
                <textarea name="descripcion" placeholder="Descripción" required class="p-2 border rounded"></textarea>
                <select name="id_proyecto" class="p-2 rounded">
                    <?php while ($proyecto = $result_proyectos->fetch_assoc()) { ?>
                        <option value="<?php echo $proyecto['id_proyecto']; ?>">
                            <?php echo htmlspecialchars($proyecto['nombre']); ?></option>
                    <?php } ?>
                </select>
                <select name="usuario_asignado" class="p-2 rounded">
                    <?php while ($usuario = $result_usuarios->fetch_assoc()) { ?>
                        <option value="<?php echo $usuario['usuario']; ?>">
                            <?php echo htmlspecialchars($usuario['usuario']); ?></option>
                    <?php } ?>
                </select>
                <input type="date" name="fecha_vencimiento" class="p-2 rounded" required class="p-2 border rounded" />
                <button type="submit" name="crear_tarea"
                    class="p-2 bg-orange-400 hover:bg-orange-700 cursor-pointer text-white rounded">Crear Nueva
                    Tarea</button>
                <form action="../logout.php" method="POST" class="p-5 flex gap-10">
                    <button type="button" onclick="history.back()"
                        class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Volver</button>
                </form>
            </form>
        </div>
        <?php
        $con->close();
        ?>