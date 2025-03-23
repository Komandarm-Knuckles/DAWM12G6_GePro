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

<h2>Tareas</h2>
<table border="1">
    <tr>
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
            <td>
                <form method="POST" action="">
                    <input type="hidden" name="id_tarea" value="<?php echo $tarea['id_tarea']; ?>">
                    <button type="submit" name="editar_tarea">Editar</button>
                </form>

                <form method="POST" action="">
                    <input type="hidden" name="eliminar_tarea" value="<?php echo $tarea['id_tarea']; ?>">
                    <button type="submit">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

<h2>Crear Nueva Tarea</h2>
<form method="POST" action="">
    <label for="nombre">Nombre:</label><br>
    <input type="text" name="nombre" required><br><br>
    <label for="descripcion">Descripción:</label><br>
    <textarea name="descripcion" required></textarea><br><br>
    <label for="id_proyecto">Proyecto:</label><br>
    <select name="id_proyecto">
        <?php while ($proyecto = $result_proyectos->fetch_assoc()) { ?>
            <option value="<?php echo $proyecto['id_proyecto']; ?>"><?php echo htmlspecialchars($proyecto['nombre']); ?></option>
        <?php } ?>
    </select><br><br>
    <label for="usuario_asignado">Usuario Asignado:</label><br>
    <select name="usuario_asignado">
        <?php while ($usuario = $result_usuarios->fetch_assoc()) { ?>
            <option value="<?php echo $usuario['usuario']; ?>"><?php echo htmlspecialchars($usuario['usuario']); ?></option>
        <?php } ?>
    </select><br><br>
    <label for="fecha_vencimiento">Fecha de Vencimiento:</label><br>
    <input type="date" name="fecha_vencimiento" required><br><br>
    <button type="submit" name="crear_tarea">Crear Tarea</button>
</form>

<?php
$con->close();
?>