<?php
// Control de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: ../index.php");
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
        <div class="flex flex-col gap-10 p-5 w-full md:w-[50em] max-w-[90%] mt-10 rounded-xl bg-gray-300 justify-center items-center">
            <h1 class="font-bold text-lg bg-orange-400 md:text-5xl underline text-center p-2 rounded-lg">EDITAR PROYECTO</h1>
        <form method="POST" action=""
            class="flex flex-col w-full p-4 gap-6 rounded-lg items-center">
            <input type="hidden" name="id_proyecto" value="<?php echo $proyectos['id_proyecto']; ?>">
            
            <div class="flex flex-col w-full">
            <label for="nombre" class="font-bold">Nombre*</label>
            <input type="text" name="nombre" class="text-center rounded-lg p-1"
                value="<?php echo htmlspecialchars($proyectos['nombre']); ?>" required>
            </div>
            
            <div class="flex flex-col w-full">
            <label for="descripcion" class="font-bold">Descripción*</label>
            <textarea name="descripcion" class="text-black text-center rounded-lg p-1 w-full" rows="5" cols="45"
                required><?php echo htmlspecialchars($proyectos['descripcion']); ?></textarea>
            </div>
            <div class="flex flex-col w-full">
            <label for="fecha_inicio" class="font-bold">Fecha de inicio*</label>
            <input type="date" name="fecha_inicio" class="text-center rounded-lg p-1"
                value="<?php echo htmlspecialchars($proyectos['fecha_inicio']); ?>" required>
            </div>
            
            <div class="flex flex-col w-full">
            <label for="fecha_fin" class="font-bold">Fecha final del Proyecto (opcional)</label>
            <input type="date" name="fecha_fin" class="text-center rounded-lg p-1"
                value="<?php echo htmlspecialchars($proyectos['fecha_fin']);?>">
            </div>
        
            <div class="flex flex-col w-full">
            <label for="estado" class="font-bold">Estado*</label>
            <select name="estado" class="rounded-lg text-center p-1">
                <option value="pendiente" <?php echo ($proyectos['estado'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                <option value="en progreso" <?php echo ($proyectos['estado'] == 'en progreso') ? 'selected' : ''; ?>>En Progreso</option>
                <option value="completado" <?php echo ($proyectos['estado'] == 'completado') ? 'selected' : ''; ?>>Completado</option>
            </select>
            </div>
            <label for="campos">Los campos asignados con un <strong>(*)</strong> son <strong>Obligatorios</strong></label>
            <button type="submit" class="hover:bg-orange-700 rounded-lg bg-orange-400 p-3 text-white font-bold" name="guardar_cambios">Guardar Cambios</button>

            <!-- Botones de volver y logOut -->
            <span class="block h-0.5 w-full bg-black opacity-40"></span>

            <div class="flex justify-center items-center gap-10">
                    <button type="button" onclick="window.location.href='adminProyectos.php'"class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Cancelar</button>
            </div>
        </form>

<?php
$con->close();
?>