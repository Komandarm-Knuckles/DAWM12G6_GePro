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

// Intentemos obtener los proyectos relacionados al jefe de equipo a través de las tareas
// Esta consulta asume que existe una relación entre tareas y proyectos,
// y que el jefe de equipo está registrado como usuario en las tareas
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
    
    // Construimos una consulta IN para obtener reuniones de todos los proyectos asociados
    $placeholders = str_repeat('?,', count($ids_proyectos) - 1) . '?';
    $sql_reuniones = "SELECT * FROM reuniones WHERE id_proyecto IN ($placeholders)";
    
    $stmt_reuniones = $con->prepare($sql_reuniones);
    
    // Preparamos el binding de parámetros dinámicamente
    $tipos = str_repeat('i', count($ids_proyectos));
    $stmt_reuniones->bind_param($tipos, ...$ids_proyectos);
    
    $stmt_reuniones->execute();
    $result_reuniones = $stmt_reuniones->get_result();
} else {
    // Si el jefe no tiene proyectos asignados, mostramos una tabla vacía
    $result_reuniones = $con->query("SELECT * FROM reuniones WHERE 1=0");
}

// Eliminar reunión
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_reunion'])) {
    $id_reunion = $_POST['id_reunion'];

    if (borrar_reunion($con, $id_reunion)) {
        header("Location: " . $_SERVER['PHP_SELF']); 
        exit();
    } else {
        $error = "Error al eliminar la reunión.";
    }
}

// Editar reunión
if (isset($_POST['editar_reunion'])) {
    $id_reunion = $_POST['id_reunion'];
    header("Location: editarReunionesJefeEquipo.php?id=" . $id_reunion);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reuniones Jefe Equipo</title>
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
                    <img src="../../img/clipboard-list.svg" alt="imagentareas"/>
                    <a href="jefeTareas.php" class="font-bold text-white text-lg">Tareas</a>
                </div>
        </section>
 
            <!-- Contenido principal -->
            <div class="flex flex-col py-5 min-h-screen gap-6 justify-center items-center bg-gray-300 w-full">
                <h1 class='font-bold text-4xl text-orange-400'>GESTIÓN DE REUNIONES</h1>
                <h3 class='text-xl font-bold text-orange-400'>Reuniones registradas:</h3>
        
                <?php if ($result_reuniones->num_rows === 0): ?>
                    <p>No hay reuniones registradas.</p>
                <?php else: ?>
                    <div class="max-h-[600px] overflow-y-auto shadow-2xl w-full">
                        <table class="styled-table w-full">
                            <thead>
                                <tr class="sticky bg-orange-400 text-white top-0">
                                    <th class="p-3">ID</th>
                                    <th class="p-3">Título</th>
                                    <th class="p-3">Descripción</th>
                                    <th class="p-3">Fecha</th>
                                    <th class="p-3">Hora</th>
                                    <th class="p-3">ID Proyecto</th>
                                    <th class="p-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($reunion = $result_reuniones->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center p-4 font-semibold"><?= $reunion['id_reunion'] ?></td>
                                        <td class="text-center p-4 font-semibold"><?= htmlspecialchars($reunion['titulo']) ?></td>
                                        <td class="text-center p-4 font-semibold"><?= htmlspecialchars($reunion['descripcion']) ?></td>
                                        <td class="text-center p-4 font-semibold"><?= $reunion['fecha'] ?></td>
                                        <td class="text-center p-4 font-semibold"><?= $reunion['hora'] ?></td>
                                        <td class="text-center p-4 font-semibold"><?= htmlspecialchars($reunion['id_proyecto']) ?></td>
                                        <td class="flex justify-center items-center gap-3 pt-4">
                                            <form method="POST" action="">
                                                <input type="hidden" name="id_reunion" value="<?= $reunion['id_reunion'] ?>">
                                                <button type="submit" name="editar_reunion" class="cursor-pointer">
                                                    <img src='../../img/square-pen.png' alt='Editar' style='width: 20px; height: 20px;' class='hover:bg-green-500 hover:scale-105' />
                                                </button>
                                            </form>
                                            <form method="POST" action="">
                                                <input type="hidden" name="id_reunion" value="<?= $reunion['id_reunion'] ?>">
                                                <button type="submit" name="eliminar_reunion" onclick="return confirm('¿Estás seguro que quieres eliminar esta reunión?');" class="cursor-pointer">
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

                <!-- Botón crear reunión -->
                <button type="button" onclick="window.location.href='jefeCrearReunion.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">CREAR REUNIÓN</button>
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