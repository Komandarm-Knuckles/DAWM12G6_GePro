<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 1)
{
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: ../index.php");
    exit();
}
require_once("../database.php");

$con = crearConexion();

// Obtener todos los proyectos para el desplegable
$proyectos_query = $con->query("SELECT id_proyecto, nombre FROM proyectos");
$proyectos = $proyectos_query->fetch_all(MYSQLI_ASSOC);

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
    $id_proyecto = $_POST['id_proyecto']; // Ahora obtenemos el id_proyecto del select

    // Actualizar la tarea en la base de datos
    $stmt = $con->prepare("UPDATE tareas SET nombre = ?, descripcion = ?, estado = ?, id_proyecto = ?, fecha_vencimiento = ?, usuario = ? WHERE id_tarea = ?");
    $stmt->bind_param("ssssssi", $nombre, $descripcion, $estado, $id_proyecto, $fecha, $usuario, $id_tarea);
    if ($stmt->execute()) {
        echo "<script>alert('Tarea actualizada con éxito.');</script>";
        // Redirigir de nuevo a jefeTareas.php
        header("Location: jefeTareas.php");
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

<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
        <div class="flex flex-col gap-10 p-5 w-full md:w-[50em] max-w-[90%] mt-10 rounded-xl bg-gray-300 justify-center items-center">

        <h2 class="text-orange-500 text-4xl underline">EDITAR TAREA</h2>
        <form method="POST" action=""
            class="flex flex-col w-full p-4 gap-6 rounded-lg items-center">
            <div class="flex flex-col w-full">
                <label for="nombre" class="text-xl">Nombre:</label>
                <input type="text" name="nombre" class="text-center  rounded-lg p-1"
                       value="<?php echo htmlspecialchars($tarea['nombre']); ?>" required>
            </div>

            <div class="flex flex-col w-full">
                <label for="fecha" class="text-xl">Fecha:</label>
                <input type="date" name="fecha" class="text-center rounded-lg  p-1"
                       value="<?php echo htmlspecialchars($tarea['fecha_vencimiento']); ?>" required>
            </div>
            <div class="flex flex-col w-full">
                <label for="id_proyecto" class="text-xl">Proyecto Asignado:</label>
                <select name="id_proyecto" class="text-center rounded-lg p-1" required>
                    <?php foreach ($proyectos as $proyecto): ?>
                        <option value="<?php echo htmlspecialchars($proyecto['id_proyecto']); ?>"
                            <?php if ($proyecto['id_proyecto'] == $tarea['id_proyecto']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($proyecto['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex flex-col w-full">
                <label for="usuario" class="text-xl">Usuario Asignado:</label>
                <select name="usuario" class="text-center  rounded-lg p-1" required>
                    <?php
                    $users_query = $con->query("SELECT usuario FROM usuarios WHERE tipo = 2");
                    while ($user = $users_query->fetch_assoc()) {
                        $selected = ($user['usuario'] == $tarea['usuario']) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($user['usuario']) . "' $selected>" . htmlspecialchars($user['usuario']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="flex flex-col w-full">
                <label for="descripcion" class="text-xl">Descripción:</label>
                <textarea name="descripcion" class="text-black text-center rounded-lg p-1 " rows="5" cols="45"
                          required><?php echo htmlspecialchars($tarea['descripcion']); ?></textarea>
            </div>
            <div class="flex flex-col w-full">
                <label for="estado" class="text-xl">Estado:</label>
                <select name="estado" class=" text-center rounded-lg p-1">
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
            </div>
            <button type="submit" class="rounded-lg bg-orange-500 hover:bg-orange-700 p-3 w-[10em] text-white" name="guardar_cambios">Guardar
                Cambios</button>
            <span class="block h-0.5 w-full bg-black opacity-40"></span>

            <form action="../logout.php" method="POST" class="p-5 flex gap-10">
                <button type="button" onclick="window.location.href='jefes-equipo.php'"
                        class="bg-orange-500 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em]  p-3 shadow-lg">Volver</button>
            </form>
        </form>
    </div>
</body>

</html>

<?php
$con->close();
?>