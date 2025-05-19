
<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 1) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: ../index.php");
    exit();
}

require_once("../database.php");
$con = crearConexion();

// Obtenemos el usuario de la sesión
$usuario = $_SESSION['usuario'];

$sql_proyectos = "SELECT DISTINCT id_proyecto 
                  FROM proyectos_usuarios 
                  WHERE usuario = ?";

$stmt_proyectos = $con->prepare($sql_proyectos);
$stmt_proyectos->bind_param("s", $usuario);
$stmt_proyectos->execute();
$resultado_proyectos = $stmt_proyectos->get_result();

if ($resultado_proyectos->num_rows > 0) {
    // Recopilamos todos los IDs de proyecto asociados con este jefe de equipo
    $ids_proyectos = [];
    while ($fila = $resultado_proyectos->fetch_assoc()) {
        $ids_proyectos[] = $fila['id_proyecto'];
    }
    
    // Construimos una consulta IN para obtener todas las tareas de los proyectos asociados
    $placeholders = str_repeat('?,', count($ids_proyectos) - 1) . '?';
    $sql_tareas = "SELECT * FROM tareas WHERE id_proyecto IN ($placeholders)";
    
    $stmt_tareas = $con->prepare($sql_tareas);
    
    // Preparamos el binding de parámetros dinámicamente
    $tipos = str_repeat('i', count($ids_proyectos));
    $stmt_tareas->bind_param($tipos, ...$ids_proyectos);
    
    $stmt_tareas->execute();
    $result_tareas = $stmt_tareas->get_result();
} else {
    // Si el jefe no tiene proyectos asignados, mostramos una tabla vacía
    $result_tareas = $con->query("SELECT * FROM tareas WHERE 1=0");
}

// Eliminar tarea
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_tarea'])) {
    $id_tarea = $_POST['id_tarea'];

    if (borrar_tarea($con, $id_tarea)) {
        header("Location: " . $_SERVER['PHP_SELF']); 
        exit();
    } else {
        $error = "Error al eliminar la tarea.";
    }
}

// Editar tarea
if (isset($_POST['editar_tarea'])) {
    $id_tarea = $_POST['id_tarea'];
    header("Location: editarTareasJefeEquipo.php?id=" . $id_tarea);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas Jefe Equipo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-full bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
<div class="flex w-full min-h-screen justify-center items-center">
        <div class="flex w-full md:flex-row flex-col justify-center items-stretch max-w-[90%]">
<section class="flex md:flex-col md:w-80 w-full flex-wrap md:justify-start justify-center items-center bg-orange-400 md:gap-10 gap-5 pt-5">
                <img class="md:w-13 w-[10em]" src="../../img/LogoEmpresa.png" alt="logo Empresa"/>
                <div class="flex gap-2">
                    <img src="../../img/folder-git-2.svg" alt="imagenProyectos"/>
                    <a href="jefeProyectos.php" class="font-bold text-white text-lg">Proyectos</a>
                </div>

                <div class="flex gap-2">
                    <img src="../../img/projector.svg" alt="imagenReuniones"/>
                    <a href="jefeReuniones.php" class="font-bold text-white text-lg">Reuniones</a>
                </div>
        </section>
 
            <!-- Contenido principal -->
            <div class="flex flex-col py-5 min-h-screen gap-6 justify-center items-center bg-gray-300 w-full">
            <h1 class="font-bold text-lg bg-orange-400 md:text-5xl underline text-center p-2 rounded-lg">GESTIÓN DE TAREAS</h1>
                <h3 class="text-xl font-bold text-lg bg-orange-400 md:text-2xl underline text-center p-2 rounded-lg">Tareas registradas:</h3>
        
                <?php if ($result_tareas->num_rows === 0): ?>
                    <p>No hay tareas registradas.</p>
                <?php else: ?>
                    <div class="max-h-[600px] overflow-y-auto shadow-2xl w-full">
                        <table class="styled-table w-full">
                            <thead>
                                <tr class="sticky bg-orange-400 text-white top-0">
                                    <th class="p-3">ID</th>
                                    <th class="p-3">Nombre</th>
                                    <th class="p-3">Descripción</th>
                                    <th class="p-3">Fecha Inicio</th>
                                    <th class="p-3">Fecha Fin</th>
                                    <th class="p-3">Estado</th>
                                    <th class="p-3">ID Proyecto</th>
                                    <th class="p-3">Usuario</th>
                                    <th class="p-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($tarea = $result_tareas->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center p-4 font-semibold"><?= $tarea['id_tarea'] ?></td>
                                        <td class="text-center p-4 font-semibold"><?= htmlspecialchars($tarea['nombre']) ?></td>
                                        <td class="text-center p-4 font-semibold"><?= htmlspecialchars($tarea['descripcion']) ?></td>
                                        <td class="text-center p-4 font-semibold"><?= $tarea['fecha_asignacion'] ?></td>
                                        <td class="text-center p-4 font-semibold"><?= $tarea['fecha_vencimiento'] ?></td>
                                        <td class="text-center p-4 font-semibold"><?= htmlspecialchars($tarea['estado']) ?></td>
                                        <td class="text-center p-4 font-semibold"><?= $tarea['id_proyecto'] ?></td>
                                        <td class="text-center p-4 font-semibold"><?= htmlspecialchars($tarea['usuario']) ?></td>
                                        <td class="flex justify-center items-center gap-3 pt-4">
                                            <form method="POST" action="">
                                                <input type="hidden" name="id_tarea" value="<?= $tarea['id_tarea'] ?>">
                                                <button type="submit" name="editar_tarea" class="cursor-pointer">
                                                    <img src='../../img/square-pen.png' alt='Editar' style='width: 20px; height: 20px;' class='hover:bg-green-500 hover:scale-105' />
                                                </button>
                                            </form>
                                            <form method="POST" action="">
                                                <input type="hidden" name="id_tarea" value="<?= $tarea['id_tarea'] ?>">
                                                <button type="submit" name="eliminar_tarea" onclick="return confirm('¿Estás seguro que quieres eliminar esta tarea?');" class="cursor-pointer">
                                                    <img src="../../img/trash-2.png" alt="Eliminar" style="width: 20px; height: 20px;" class="hover:bg-red-500 hover:scale-105">
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Botón crear tarea -->
                <button type="button" onclick="window.location.href='jefeCrearTarea.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">CREAR TAREA</button>
 <!-- Botones de navegación -->
                <div class="flex justify-center items-center gap-10">
                    <form action="../logout.php" method="POST" class="p-5 flex md:flex-row flex-col gap-10">
                        <button type="button" onclick="window.location.href='jefes-equipo.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-fit p-3 shadow-lg">Panel de Jefe de Equipo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php $con->close(); ?>
</body>
</html>