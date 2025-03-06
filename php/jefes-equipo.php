<?php
require_once("database.php");
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$con = crearConexion();

// Verificar si es jefe de equipo
$sql_tipo = "SELECT tipo FROM usuarios WHERE usuario = ?";
$stmt = $con->prepare($sql_tipo);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$tipo_usuario = $result->fetch_assoc()['tipo'];

if ($tipo_usuario != 1) {
    header("Location: empleado.php");
    exit();
}

// Obtener reuniones
$sql_reuniones = "SELECT * FROM reuniones";
$reuniones = $con->query($sql_reuniones);

// Obtener tareas
$sql_tareas = "SELECT * FROM tareas";
$tareas = $con->query($sql_tareas);

// Obtener proyectos
$sql_proyectos = "SELECT * FROM proyectos";
$proyectos = $con->query($sql_proyectos);

// Crear reunión
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_reunion'])) {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $id_proyecto = $_POST['id_proyecto'];
    
    $stmt = $con->prepare("INSERT INTO reuniones (titulo, descripcion, fecha, hora, id_proyecto) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $titulo, $descripcion, $fecha, $hora, $id_proyecto);
    $stmt->execute();
    header("Location: jefeEquipo.php");
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
    $stmt->execute();
    header("Location: jefeEquipo.php");
}

// Crear proyecto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_proyecto'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $estado = "pendiente";
    
    $stmt = $con->prepare("INSERT INTO proyectos (nombre, descripcion, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado);
    $stmt->execute();
    header("Location: jefeEquipo.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel de Jefe de Equipo</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body>
    <h2>Bienvenido/a, <?php echo htmlspecialchars($usuario); ?></h2>
    
    <h3>Crear Proyecto</h3>
    <form method="post" >
        <input type="text" name="nombre" placeholder="Nombre" required>
        <textarea name="descripcion" placeholder="Descripción" required></textarea>
        <input type="date" name="fecha_inicio" required>
        <input type="date" name="fecha_fin">
        <button type="submit" name="crear_proyecto">Crear Proyecto</button>
    </form>
    
    <h3>Crear Reunión</h3>
    <form method="post">
        <input type="text" name="titulo" placeholder="Título" required>
        <textarea name="descripcion" placeholder="Descripción" required></textarea>
        <input type="date" name="fecha" required>
        <input type="time" name="hora" required>
        <input type="number" name="id_proyecto" placeholder="ID Proyecto" required>
        <button type="submit" name="crear_reunion">Crear Reunión</button>
    </form>

    <h3>Crear Tarea</h3>
    <form method="post">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <textarea name="descripcion" placeholder="Descripción" required></textarea>
        <input type="number" name="id_proyecto" placeholder="ID Proyecto" required>
        <input type="text" name="usuario_asignado" placeholder="Usuario asignado" required>
        <input type="date" name="fecha_vencimiento" required>
        <button type="submit" name="crear_tarea">Crear Tarea</button>
    </form>
    
    <h3>Proyectos</h3>
    <ul>
        <?php while ($proyecto = $proyectos->fetch_assoc()) { ?>
            <li><?php echo htmlspecialchars($proyecto['nombre']) . " - Estado: " . $proyecto['estado']; ?>
                <a href='editar_proyecto.php?id=<?php echo $proyecto['id_proyecto']; ?>'>Editar</a>
                <a href='borrar_proyecto.php?id=<?php echo $proyecto['id_proyecto']; ?>'>Borrar</a>
            </li>
        <?php } ?>
    </ul>
    
    <h3>Reuniones</h3>
    <ul>
        <?php while ($reunion = $reuniones->fetch_assoc()) { ?>
            <li><?php echo htmlspecialchars($reunion['titulo']) . " - " . $reunion['fecha']; ?>
                <a href='editar_reunion.php?id=<?php echo $reunion['id_reunion']; ?>'>Editar</a>
                <a href='borrar_reunion.php?id=<?php echo $reunion['id_reunion']; ?>'>Borrar</a>
            </li>
        <?php } ?>
    </ul>

    <h3>Tareas</h3>
    <ul>
        <?php while ($tarea = $tareas->fetch_assoc()) { ?>
            <li><?php echo htmlspecialchars($tarea['nombre']) . " - Estado: " . $tarea['estado']; ?>
                <a href='editar_tarea.php?id=<?php echo $tarea['id_tarea']; ?>'>Editar</a>
                <a href='borrar_tarea.php?id=<?php echo $tarea['id_tarea']; ?>'>Borrar</a>
            </li>
        <?php } ?>
    </ul>
</body>
</html>

