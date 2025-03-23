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

    // Actualizar la tarea en la base de datos
    $stmt = $con->prepare("UPDATE tareas SET nombre = ?, descripcion = ?, estado = ? WHERE id_tarea = ?");
    $stmt->bind_param("sssi", $nombre, $descripcion, $estado, $id_tarea);
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
    <title>Editar Tarea</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
<div class="flex flex-col gap-10 p-10 w-full max-w-[65%] bg-gray-300 justify-center items-center">
    <h2>Editar Tarea</h2>
    <form method="POST" action="">
        <input type="hidden" name="id_tarea" value="<?php echo $tarea['id_tarea']; ?>">
        <label for="nombre">Nombre:</label><br>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($tarea['nombre']); ?>" required><br><br>
        <label for="descripcion">Descripción:</label><br>
        <textarea name="descripcion" required><?php echo htmlspecialchars($tarea['descripcion']); ?></textarea><br><br>
        <label for="estado">Estado:</label><br>
        <select name="estado">
            <option value="pendiente" <?php if ($tarea['estado'] == "pendiente") echo "selected"; ?>>Pendiente</option>
            <option value="en progreso" <?php if ($tarea['estado'] == "en progreso") echo "selected"; ?>>En progreso</option>
            <option value="completada" <?php if ($tarea['estado'] == "completada") echo "selected"; ?>>Completada</option>
        </select><br><br>
        <button type="submit" name="guardar_cambios">Guardar Cambios</button>
    </form>
</div>
</body>
</html>

<?php
$con->close();
?>