<?php
// control de inicio de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 1) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: index.php");
    exit();
}
require_once("database.php");


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
    header("Location: jefes-equipo.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel de Jefe de Equipo</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../img/pixels14.jpg')]">
<div class="flex flex-col gap-10 p-10 w-full max-w-[65%] bg-gray-300 justify-center items-center">
            <h2 class="font-bold text-orange-400 text-4xl underline">Bienvenido/a, <?php echo htmlspecialchars($usuario); ?></h2>

            <h3 class="font-bold text-orange-400 text-3xl underline">Crear Proyecto</h3>
            <form method="post" class="flex flex-col w-full gap-2">
                <input type="text" name="nombre" placeholder="Nombre" required class="p-2 border rounded" />
                <textarea name="descripcion" placeholder="Descripción" required class="p-2 border rounded"></textarea>
                <input type="date" name="fecha_inicio" required class="p-2 border rounded" />
                <input type="date" name="fecha_fin" class="p-2 border rounded" />
                <button type="submit" name="crear_proyecto" class="p-2 bg-orange-400 hover:bg-orange-700 cursor-pointer text-white rounded">Crear Proyecto</button>
            </form>

            <h3 class="font-bold text-orange-400 text-3xl underline">Crear Reunión</h3>
            <form method="post" class="flex flex-col w-full gap-2">
                <input type="text" name="titulo" placeholder="Título" required class="p-2 border rounded" />
                <textarea name="descripcion" placeholder="Descripción" required class="p-2 border rounded"></textarea>
                <input type="date" name="fecha" required class="p-2 border rounded" />
                <input type="time" name="hora" required class="p-2 border rounded" />
                <input type="number" name="id_proyecto" placeholder="ID Proyecto" required class="p-2 border rounded" />
                <button type="submit" name="crear_reunion" class="p-2 bg-orange-400 hover:bg-orange-700 cursor-pointer text-white rounded">Crear Reunión</button>
            </form>

            <h3 class="font-bold text-orange-400 text-3xl underline">Crear Tarea</h3>
            <form method="post" class="flex flex-col w-full gap-2">
                <input type="text" name="nombre" placeholder="Nombre" required class="p-2 border rounded" />
                <textarea name="descripcion" placeholder="Descripción" required class="p-2 border rounded"></textarea>
                <input type="number" name="id_proyecto" placeholder="ID Proyecto" required class="p-2 border rounded" />
                <input type="text" name="usuario_asignado" placeholder="Usuario asignado" required class="p-2 border rounded" />
                <input type="date" name="fecha_vencimiento" required class="p-2 border rounded" />
                <button type="submit" name="crear_tarea" class="p-2 bg-orange-400 hover:bg-orange-700 cursor-pointer text-white rounded">Crear Tarea</button>
            </form>

            <h3 class="font-bold text-orange-400 text-3xl underline">Proyectos</h3>
            <ul>
                <?php while ($proyecto = $proyectos->fetch_assoc()) { ?>
                <li class="flex gap-5">
                <?php
                echo " <p class='font-bold text-orange-400'>-Nombre del Proyecto:</p> " . htmlspecialchars($proyecto['nombre']) .
                     " <p class='font-bold text-orange-400'>Estado:</p> " . htmlspecialchars($proyecto['estado'])
                ?>
                    <div class="flex gap-1">
                    <a href="editar_proyecto.php?id=<?php echo $proyecto['id_proyecto']; ?>">
                        <img src='../img/square-pen.png' alt='Eliminar' style='width: 20px; height: 20px;' class='hover:bg-green-500 hover:scale-105'/> 
                    </a>
                    <a href="borrar_proyecto.php?id=<?php echo $proyecto['id_proyecto']; ?>">
                        <img src='../img/trash-2.png' alt='Eliminar' style='width: 20px; height: 20px;' class='hover:bg-red-500 hover:scale-105'/> 
                    </a>
                    </div>
                </li>
                <?php } ?>
            </ul>

            <h3 class="font-bold text-orange-400 text-3xl underline">Reuniones</h3>
            <ul>
                <?php while ($reunion = $reuniones->fetch_assoc()) { ?>
                <li class="flex gap-5">
                    <?php 
                    echo " <p class='font-bold text-orange-400'>-Nombre de la Reunión:</p> " .  htmlspecialchars($reunion['titulo']) . 
                    " <p class='font-bold text-orange-400'>Fecha:</p>" . $reunion['fecha']; ?>
                    <div class="flex gap-1">
                        <a href="editar_reunion.php?id=<?php echo $reunion['id_reunion']; ?>">
                            <img src='../img/square-pen.png' alt='Eliminar' style='width: 20px; height: 20px;' class='hover:bg-green-500 hover:scale-105'/>
                        </a>
                        <a href="borrar_reunion.php?id=<?php echo $reunion['id_reunion']; ?>">
                            <img src='../img/square-pen.png' alt='Eliminar' style='width: 20px; height: 20px;' class='hover:bg-green-500 hover:scale-105'/>
                        </a>
                    </div>
                </li>
                <?php } ?>
            </ul>

            <h3 class="font-bold text-orange-400 text-3xl underline">Tareas</h3>
            <ul>
                <?php while ($tarea = $tareas->fetch_assoc()) { ?>
                <li class="flex gap-5">
                    <?php 
                    echo " <p class='font-bold text-orange-400'>-Nombre de la Tarea:</p> " .  htmlspecialchars($tarea['nombre']) . 
                    " <p class='font-bold text-orange-400'>Estado:</p> " . $tarea['estado']; ?>
                    <div class="flex gap-1">
                    <a href="editar_tarea.php?id=<?php echo $tarea['id_tarea']; ?>">
                    <img src='../img/square-pen.png' alt='Eliminar' style='width: 20px; height: 20px;' class='hover:bg-green-500 hover:scale-105'/> 
                    </a>
                    <a href="borrar_tarea.php?id=<?php echo $tarea['id_tarea']; ?>">
                    <img src='../img/trash-2.png' alt='Eliminar' style='width: 20px; height: 20px;' class='hover:bg-red-500 hover:scale-105'/> 
                    </a>
                    </div>

                </li>
                <?php } ?>
            </ul>
            <form action="logout.php" method="POST">
                <button type="submit" class="p-2 bg-orange-400 rounded-xl shadow-lg cursor-pointer p-3 text-white hover:bg-orange-700">Cerrar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>