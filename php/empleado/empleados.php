<?php
// control de inicio de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 2) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: index.php");
    exit();
}
require_once("database.php");

$usuario = $_SESSION['usuario'];
$con = crearConexion();

// Verificar si es empleado
$sql_tipo = "SELECT tipo FROM usuarios WHERE usuario = ?";
$stmt = $con->prepare($sql_tipo);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$tipo_usuario = $result->fetch_assoc()['tipo'];

if ($tipo_usuario != 2) {
    $_SESSION['error'] = "No eres un empleado";
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina de Empleado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
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


