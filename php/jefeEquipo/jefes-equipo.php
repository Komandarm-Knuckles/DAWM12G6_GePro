<?php
// control de inicio de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 1) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: index.php");
    exit();
}
require_once("../database.php");

$usuario = $_SESSION['usuario'];
$con = crearConexion();

// Obtener reuniones
$sql_reuniones = "SELECT * FROM reuniones WHERE id_proyecto = ?";
$stmt = $con->prepare($sql_reuniones);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$reuniones = $stmt->get_result();

// Obtener tareas
$sql_tareas = "SELECT * FROM tareas WHERE usuario = ?";
$stmt = $con->prepare($sql_tareas);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$tareas = $stmt->get_result();

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
    header("Location: jefes-equipo.php");
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
    header("Location: jefes-equipo.php");
}

// ELIMINAR TAREAS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_tarea'])) {
    $id_tarea = intval($_POST['eliminar_tarea']);
    $stmt = $con->prepare("DELETE FROM tareas WHERE id_tarea = ?");
    $stmt->bind_param("i", $id_tarea);
    $stmt->execute();
    header("Location: jefes-equipo.php");
    exit();
}

// ELIMINAR REUNIONES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_reunion'])) {
    $id_reunion = intval($_POST['eliminar_reunion']);
    $stmt = $con->prepare("DELETE FROM reuniones WHERE id_reunion = ?");
    $stmt->bind_param("i", $id_reunion);
    $stmt->execute();
    header("Location: jefes-equipo.php");
    exit();
}

// EDITAR TAREAS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_tarea'])) {
    $id_tarea = intval($_POST['id_tarea']);
    header("Location: editarTareasJefeEquipo.php?id=" . $id_tarea);
    exit();
}

// EDITAR REUNIONES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_reunion'])) {
    $id_reunion = intval($_POST['id_reunion']);
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];

    $stmt = $con->prepare("UPDATE reuniones SET titulo=?, descripcion=?, fecha=? WHERE id_reunion=?");
    $stmt->bind_param("sssi", $titulo, $descripcion, $fecha, $id_reunion);
    $stmt->execute();
    header("Location: editarReunionesJefeEquipo.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel de Jefe de Equipo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-full min-h-screen flex flex-col justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex flex-col gap-10 p-10 w-full bg-gray-300 justify-center bg-opacity-50 items-center">
        <h2 class="font-bold text-orange-600 text-4xl underline">Bienvenido/a, <?php echo htmlspecialchars($usuario); ?></h2>
        <h2 class="font-bold text-orange-600 text-4xl underline">Proyectos</h2>
        <ul class="flex flex-wrap gap-2 w-full">
        <?php if ($proyectos->num_rows == 0) { ?>
                        <span class="text-center font-bold">No hay proyectos.</span>
                    <?php } ?>
            <?php while ($proyecto = $proyectos->fetch_assoc()) { ?>
                <li class="flex gap-5">
                    <?php
                    echo
                    "<div class='flex w-full bg-gray-200 gap-2 rounded-lg shadow-lg p-5'>". 
                    "<div class='flex flex-col w-full gap-2 p-5'>". 
                        "<p class='font-bold text-orange-600'>-ID: <span class='text-black'>".htmlspecialchars($proyecto['id_proyecto'])."</p>".
                        "<p class='font-bold text-orange-600'>Nombre: <span class='text-black'>".htmlspecialchars($proyecto['nombre'])."</p>".
                        "<p class='font-bold text-orange-600'>Descripción: <span class='text-black'>".htmlspecialchars($proyecto['descripcion'])."</p>".
                        "<p class='font-bold text-orange-600'>Fecha de Inicio: <span class='text-black'>".htmlspecialchars($proyecto['fecha_inicio'])."</p>".
                        "<p class='font-bold text-orange-600'>Fecha de Fin: <span class='text-black'>".htmlspecialchars($proyecto['fecha_fin'])."</p>".
                        "<p class='font-bold text-orange-600'>Estado: <span class='text-black'>".htmlspecialchars($proyecto['estado'])."</p>"


                    ?>
                <?php echo "</div></div>"; ?>
                </li>
            <?php } ?>
        </ul>

        <h3 class="font-bold text-orange-600 text-3xl underline">Reuniones</h3>
        <ul>
        <?php if ($reuniones->num_rows == 0) { ?>
                        <span class="text-center font-bold">No hay reuniones.</span>
                    <?php } ?>
            <?php while ($reunion = $reuniones->fetch_assoc()) { ?>
                <li class="flex gap-5">
                    <?php
                    echo " <p class='font-bold text-orange-400'>-Nombre de la Reunión:".htmlspecialchars($reunion['titulo'])."</p>".
                        " <p class='font-bold text-orange-400'>Fecha:". $reunion['fecha']."</p>".
                        "<p class='font-bold text-orange-400'>Hora:". $reunion['hora']."</p>". 
                        "<p class='font-bold text-orange-400'>Descripción:".htmlspecialchars($reunion['descripcion'])."</p>"
                         ?>
                   
                    <div class="flex gap-1">
                        <form method="POST" action="">
                            <input type="hidden" name="editar_reunion" value="<?php echo $reunion['id_reunion']; ?>">
                            <button type="submit" class="cursor-pointer">
                                <img src='../../img/square-pen.png' alt='Editar' style='width: 20px; height: 20px;'
                                    class='hover:bg-green-500 hover:scale-105' />
                            </button>
                        </form>
                        <form method="POST" action="">
                            <input type="hidden" name="eliminar_reunion" value="<?php echo $reunion['id_reunion']; ?>">
                            <button type="submit"
                                onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');"
                                class="cursor-pointer">
                                <img src="../../img/trash-2.png" alt="Eliminar" style="width: 20px; height: 20px;"
                                    class="hover:bg-red-500 hover:scale-105">
                            </button>
                        </form>
                    </div>
                </li>
            <?php } ?>
        </ul>

        <h3 class="font-bold text-orange-400 text-3xl underline">Tareas</h3>
        <ul class="flex gap-2 flex-wrap w-full">
        <?php if ($tareas->num_rows == 0) { ?>
                        <span class="text-center font-bold">No hay tareas.</span>
                    <?php } ?>
            <?php while ($tarea = $tareas->fetch_assoc()) { ?>
                <li class="flex gap-5">
                    <?php
                    echo
                    "<div class='flex w-full bg-gray-200 gap-2 rounded-lg shadow-lg p-5'>". 
                    "<div class='flex flex-col w-full gap-2 p-5'>".
                        " <p class='font-bold text-orange-400'>-ID: <span class='text-black'>". htmlspecialchars($tarea['id_tarea'])."</span></p> ".
                        " <p class='font-bold text-orange-400'>-Nombre: <span class='text-black'>".htmlspecialchars($tarea['nombre'])."</p> ". 
                        " <p class='font-bold text-orange-400'>-Usuario Asignado: <span class='text-black'>".htmlspecialchars($tarea['usuario'])."</p>".
                        " <p class='font-bold text-orange-400'>-Descripció: <span class='text-black'>".htmlspecialchars($tarea['descripcion'])."</p>".
                        " <p class='font-bold text-orange-400'>Estado: <span class='text-black'>".htmlspecialchars($tarea['estado'])."</p>"
                     ?>
                    
                    <div class="flex gap-3">
                        <span class="font-bold text-orange-400">Acciones:</span>
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
                    </div>
                <?php echo "</div></div>"; ?>
                </li>
            <?php } ?>
        </ul>

        <h3 class="font-bold text-orange-400 text-3xl underline">Crear Reunión</h3>
        <form method="post" class="flex flex-col w-full gap-2">
            <input type="text" name="titulo" placeholder="Título" required class="p-2 border rounded" />
            <textarea name="descripcion" placeholder="Descripción" required class="p-2 border rounded"></textarea>
            <input type="date" name="fecha" required class="p-2 border rounded" />
            <input type="time" name="hora" required class="p-2 border rounded" />
            <input type="number" name="id_proyecto" placeholder="ID Proyecto" required class="p-2 border rounded" />
            <button type="submit" name="crear_reunion"
                class="p-2 bg-orange-400 hover:bg-orange-700 cursor-pointer text-white rounded">Crear Reunión</button>
        </form>

        <h3 class="font-bold text-orange-400 text-3xl underline">Crear Tarea</h3>
        <form method="post" class="flex flex-col w-full gap-2">
            <input type="text" name="nombre" placeholder="Nombre" required class="p-2 border rounded" />
            <textarea name="descripcion" placeholder="Descripción" required class="p-2 border rounded"></textarea>
            <input type="number" name="id_proyecto" placeholder="ID Proyecto" required class="p-2 border rounded" />
            <input type="text" name="usuario_asignado" placeholder="Usuario asignado" required
                class="p-2 border rounded" />
            <input type="date" name="fecha_vencimiento" required class="p-2 border rounded" />
            <button type="submit" name="crear_tarea"
                class="p-2 bg-orange-400 hover:bg-orange-700 cursor-pointer text-white rounded">Crear Tarea</button>
        </form>

        <form action="../logout.php" method="POST">
            <button type="submit"
                class="p-2 bg-orange-400 rounded-xl shadow-lg cursor-pointer p-3 text-white hover:bg-orange-700">Cerrar
                Sesión</button>
        </form>
    </div>
</body>

</html>