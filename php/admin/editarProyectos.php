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

// Obtener el proyecto de la base de datos
if (isset($_GET['id'])) {
    $id_proyecto = $_GET['id'];
    $stmt = $con->prepare("SELECT * FROM proyectos WHERE id_proyecto = ?");
    $stmt->bind_param("i", $id_proyecto);
    $stmt->execute();
    $result = $stmt->get_result();
    $proyectos = $result->fetch_assoc();

    if (!$proyectos) {
        echo "proyecto no encontrado.";
        exit;
    }
} else {
    echo "ID proyecto no proporcionado.";
    exit;
}

    // Obtener los datos del formulario
if (isset($_POST['guardar_cambios'])) {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null; 
    $estado = $_POST['estado'];

    // Actualizar el proyecto en la base de datos
    $stmt = $con->prepare("UPDATE proyectos SET nombre = ?, descripcion = ?, fecha_inicio = ?, fecha_fin= ?, estado = ? WHERE id_proyecto = ?");
    $stmt->bind_param("sssssi", $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado, $id_proyecto);
    if ($stmt->execute()) {
        echo "
        <script>
            alert('Proyecto actualizado correctamente.');
            window.location.href = 'adminProyectos.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('Error al actualizar Proyecto.');
            window.location.href = 'adminProyectos.php';
        </script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Editar Proyecto Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body
    class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex flex-col gap-10 p-5 w-full max-w-[65%] rounded-xl bg-gray-300 justify-center items-center">
        <h2 class="font-bold text-orange-400 text-3xl underline">EDITAR PROYECTO</h2>
        <form method="POST" action=""
            class="flex flex-col w-full p-4 gap-6 rounded-lg shadow-xl text-center items-center">
            <input type="hidden" name="id_proyecto" value="<?php echo $proyectos['id_proyecto']; ?>">
            
            <strong><label for="nombre" class="text-xl text-black">Nombre:</label></strong>
            <input type="text" name="nombre" class="flex text-center w-[20em] rounded-lg p-1"
                value="<?php echo htmlspecialchars($proyectos['nombre']); ?>" required>
            
            <strong><label for="descripcion" class="text-xl text-black">Descripción:</label></strong>
            <textarea name="descripcion" class="text-black rounded-lg p-1" rows="5" cols="45"
                required><?php echo htmlspecialchars($proyectos['descripcion']); ?></textarea>
            
            <strong><label for="fecha_inicio" class="text-xl text-black">Fecha de inicio:</label></strong>
            <input type="date" name="fecha_inicio" class="flex text-center w-[20em] rounded-lg p-1"
                value="<?php echo htmlspecialchars($proyectos['fecha_inicio']); ?>" required>
            
            <strong><label for="fecha_fin" class="text-xl text-black">Fecha de fin:</label></strong>
            <input type="date" name="fecha_fin" class="flex text-center w-[20em] rounded-lg p-1"
                value="<?php echo htmlspecialchars($proyectos['fecha_fin']); ?>">
            
            <strong><label for="estado" class="text-xl text-black">Estado:</label></strong>
            <select name="estado" class="w-[20em] rounded-lg p-1">
                <option value="pendiente" <?php echo ($proyectos['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                <option value="en progreso" <?php echo ($proyectos['estado'] == 'en progreso') ? 'selected' : ''; ?>>En Progreso</option>
                <option value="completado" <?php echo ($proyectos['estado'] == 'completado') ? 'selected' : ''; ?>>Completado</option>
            </select>

            <button type="submit" class="hover:bg-orange-700 rounded-lg bg-orange-500 p-3 text-white" name="guardar_cambios">Guardar Cambios</button>
            
            <!-- Botones de volver y logOut -->

            <div class="flex justify-center items-center gap-10">
                <form action="../logout.php" method="POST" class="p-5 flex gap-10">
                    <button type="button" onclick="history.back()"class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Volver</button>
                    <button type="submit" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-40 shadow-lg cursor-pointer font-bold text-white">Cerrar Sesión</button>
                </form>
            </div>
        </form>

<?php
$con->close();
?>