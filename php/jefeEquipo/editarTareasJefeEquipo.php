<?php
include '../database.php';

$con = crearConexion();

if (isset($_GET['id'])) {
    $id_tarea = $_GET['id'];
    // Obtener la tarea de la base de datos
    $stmt = $con->prepare("SELECT * FROM tareas WHERE id_tarea = ?");
    $stmt->bind_param("i", $id_tarea);
    $stmt->execute();
    $result = $stmt->get_result();
    $tarea = $result->fetch_assoc();

    if (!$tarea) {
        echo "Tarea no encontrada.";
        exit;
    }
} else {
    echo "ID de tarea no proporcionado.";
    exit;
}

if (isset($_POST['guardar_cambios'])) {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];
    $fecha = $_POST['fecha'];
    $usuario = $_POST['usuario'];

    // Actualizar la tarea en la base de datos
    $stmt = $con->prepare("UPDATE tareas SET nombre = ?, descripcion = ?, estado = ?, fecha_vencimiento = ?, usuario = ? WHERE id_tarea = ?");
    $stmt->bind_param("sssssi", $nombre, $descripcion, $estado, $fecha, $usuario,$id_tarea );
    if ($stmt->execute()) {
        echo "<script>alert('Tarea actualizada con éxito.');</script>";
        // Redirigir de nuevo a jefes-equipo.php
        header("Location: jefes-equipo.php");
        exit;
    } else {
        echo "<script>alert('Error al actualizar la tarea.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Editar Tarea Jefe Equipo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body
    class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex flex-col gap-10 p-5 w-full max-w-[65%] rounded-xl bg-gray-300 justify-center items-center">
        <h2 class="text-orange-500 text-4xl">Editar Tarea</h2>
        <form method="POST" action=""
            class="flex flex-col w-full p-4 gap-6 rounded-lg shadow-xl text-center items-center">
            <label for="nombre" class="text-xl text-black">Nombre:</label>
            <input type="text" name="nombre" class="flex text-center w-[20em] rounded-lg p-1"
                value="<?php echo htmlspecialchars($tarea['nombre']); ?>" required>

            <label for="fecha" class="text-xl text-black">Fecha:</label>
            <input type="date" name="fecha" class="flex text-center rounded-lg p-1"
                value="<?php echo htmlspecialchars($tarea['fecha_vencimiento']); ?>" required>

                <label for="usuario" class="text-xl text-black">Usuario Asignado:</label>
<select name="usuario" class="flex text-center w-[20em] rounded-lg p-1" required>
    <?php
    // Get list of users from the database
    $users_query = $con->query("SELECT usuario FROM usuarios");
    while ($user = $users_query->fetch_assoc()) {
        $selected = ($user['usuario'] == $tarea['usuario_asignado']) ? 'selected' : '';
        echo "<option value='" . htmlspecialchars($user['usuario']) . "' $selected>" . htmlspecialchars($user['usuario']) . "</option>";
    }
    ?>
</select>

            <label for="descripcion" class="text-xl text-black">Descripción:</label>
            <textarea name="descripcion" class="text-black rounded-lg p-1" rows="5" cols="45"
                required><?php echo htmlspecialchars($tarea['descripcion']); ?></textarea>

            <label for="estado" class="text-xl text-black">Estado:</label>
            <select name="estado" class="w-[20em] rounded-lg p-1">
                <option value="pendiente" <?php if ($tarea['estado'] == "pendiente")
                    echo "selected"; ?>>Pendiente
                </option>
                <option value="en progreso" <?php if ($tarea['estado'] == "en progreso")
                    echo "selected"; ?>>En progreso
                </option>
                <option value="completada" <?php if ($tarea['estado'] == "completada")
                    echo "selected"; ?>>Completada
                </option>
            </select>

            <button type="submit" class="rounded-lg bg-orange-500 p-3 text-white" name="guardar_cambios">Guardar
                Cambios</button>
            <form action="../logout.php" method="POST" class="p-5 flex gap-10">
                <button type="button" onclick="window.location.href='jefes-equipo.php'"
                    class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Volver</button>
            </form>
        </form>
    </div>
</body>

</html>

<?php
$con->close();
?>