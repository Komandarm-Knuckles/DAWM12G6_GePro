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

$result_proyectos = $con->query("SELECT id_proyecto, nombre FROM proyectos");
$result_usuarios = obtener_todos_los_usuarios($con);

// Crear tarea
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_tarea'])) 
{
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $id_proyecto = $_POST['id_proyecto'];
    $usuario_asignado = $_POST['usuario_asignado'];
    $fecha_asignacion = date("Y-m-d");
    $fecha_vencimiento = $_POST['fecha_vencimiento'];
    $estado = "pendiente";

    if (strtotime($fecha_vencimiento) <= strtotime($fecha_asignacion)) 
    {
        echo "<script>alert('La fecha de vencimiento debe ser posterior a hoy.');
              window.location.href = window.location.href; </script>";
        exit();
    }

    $stmt = $con->prepare("INSERT INTO tareas (nombre, descripcion, id_proyecto, usuario, fecha_asignacion, fecha_vencimiento, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissss", $nombre, $descripcion, $id_proyecto, $usuario_asignado, $fecha_asignacion, $fecha_vencimiento, $estado);

    if ($stmt->execute()) 
    {
        echo "<script>alert('Tarea creada con éxito.');
              window.location.href='jefeTareas.php'; </script>";
        exit();
    } 
    else 
    {
        echo "<script>alert('Error al crear la tarea: " . $stmt->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crear Tarea Jefe Equipo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex flex-col max-w-[90%] w-[40em] bg-gray-300 p-8 rounded shadow-xl justify-center items-center gap-6">
            <h1 class="font-bold text-lg bg-orange-400 md:text-5xl underline text-center p-2 rounded-lg">CREAR TAREA</h1>
        <div class="flex justify-center items-center">
            <span class="block h-0.5 w-130 bg-black opacity-40"></span>
        </div>

        <form method="POST" action="" class="flex flex-col w-full gap-6">
            <div class="flex flex-col w-full">
            <label for="nombre" class="font-bold">Nombre*</label>
            <input type="text" name="nombre" placeholder="Nombre de la tarea" required class="p-2 border rounded-xl w-full" />
            </div>
            <div class="flex flex-col w-full">
            <label for="descripcion" class="font-bold">Descripción*</label>
            <textarea name="descripcion" placeholder="Descripción de la tarea" required class="p-2 border rounded-xl w-full"></textarea>
            </div>
            <div class="flex flex-col w-full">
            <label for="id_proyecto" class="font-bold">Asigna un Proyecto*</label>
            <select name="id_proyecto" required class="p-2 border rounded-xl text-center w-full">
                <option value="" disabled selected>Selecciona un proyecto</option>
                <?php while ($proyecto = $result_proyectos->fetch_assoc()) { ?>
                    <option value="<?php echo $proyecto['id_proyecto']; ?>">
                        <?php echo htmlspecialchars($proyecto['nombre']); ?>
                    </option>
                <?php } ?>
            </select>
            </div>
            
            <div class="flex flex-col w-full">
            <label for="usuario_asignado" class="font-bold">Asigna un Usuario*</label>
            <select name="usuario_asignado" required class="p-2 border rounded-xl text-center w-full">
                <option value="" disabled selected>Selecciona un usuario</option>
                <?php while ($usuario = $result_usuarios->fetch_assoc()) { ?>
                    <option value="<?php echo $usuario['usuario']; ?>">
                        <?php echo htmlspecialchars($usuario['usuario']); ?>
                    </option>
                <?php } ?>
            </select>
            </div>
            <div class="flex flex-col w-full">
            <label for="fecha_vencimiento" class="font-bold">Fecha de vencimiento*</label>
            <input type="date" name="fecha_vencimiento" required class="p-2 border rounded-xl text-center w-full" />
            </div>     
            <label for="campos">Los campos asignados con un <strong>(*)</strong> son <strong>Obligatorios</strong></label>
            <div class="flex justify-center items-center">
            <button type="submit" name="crear_tarea" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Crear Tarea</button>
            </div>
        </form>

        <button type="button" onclick="window.location.href='jefeTareas.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Cancelar</button>
    </div>
</body>
</html>
