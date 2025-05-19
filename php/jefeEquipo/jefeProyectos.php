<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 1) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: ../index.php");
    exit();
}

require_once("../database.php");
$usuario = $_SESSION['usuario'];
$con = crearConexion();

// Obtenemos el usuario de la sesión

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
    
    // Construimos una consulta IN para obtener proyectos de todos los proyectos asociados
    $placeholders = str_repeat('?,', count($ids_proyectos) - 1) . '?';
    $sql_proyectos_asignados = "SELECT * FROM proyectos WHERE id_proyecto IN ($placeholders)";
    
    $stmt_proyectos_asignados = $con->prepare($sql_proyectos_asignados);
    
    // Preparamos el binding de parámetros dinámicamente
    $tipos = str_repeat('i', count($ids_proyectos));
    $stmt_proyectos_asignados->bind_param($tipos, ...$ids_proyectos);
    
    $stmt_proyectos_asignados->execute();
    $result_proyectos = $stmt_proyectos_asignados->get_result();
} else {
    // Si el jefe no tiene proyectos asignados, mostramos una tabla vacía
    $result_proyectos = $con->query("SELECT * FROM proyectos WHERE 1=0");
}



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos de Jefe de Equipo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-full bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex w-full min-h-screen justify-center items-center">
        <div class="flex w-full md:flex-row flex-col justify-center items-stretch max-w-[90%]">
            
        <!-- Menú lateral -->
            <section class="flex md:flex-col md:w-80 w-full flex-wrap md:justify-start justify-center items-center bg-orange-400 md:gap-10 gap-5 pt-5">
                <img class="md:w-13 w-[10em]" src="../../img/LogoEmpresa.png" alt="logo Empresa"/>
                <div class="flex gap-2">
                    <img src="../../img/projector.svg" alt="imagenReuniones"/>
                    <a href="jefeReuniones.php" class="font-bold text-white text-lg">Reuniones</a>
                </div>
                <div class="flex gap-2">
                    <img src="../../img/clipboard-list.svg" alt="imagentareas"/>
                    <a href="jefeTareas.php" class="font-bold text-white text-lg">Tareas</a>
                </div>
            </section>

            <!-- Contenido principal -->
            <div class="flex flex-col min-h-screen gap-6 justify-center items-center bg-gray-300 w-full">
            <h1 class="font-bold text-lg bg-orange-400 md:text-5xl underline text-center p-2 rounded-lg">GESTIÓN DE PROYECTOS</h1>
                <h3 class="text-xl font-bold text-lg bg-orange-400 md:text-2xl underline text-center p-2 rounded-lg">Proyectos Registrados:</h3>
                <?php if ($result_proyectos->num_rows === 0): ?>
                    <p>No hay proyectos registrados.</p>
                <?php else: ?>

                    <div class="max-h-[600px] overflow-y-auto shadow-2xl w-full">
                        <table class="styled-table w-full">
                            <thead>
                                <tr class="sticky bg-orange-400 text-white top-0">
                                    <th class="p-3">ID</th>
                                    <th class="p-3">NOMBRE</th>
                                    <th class="p-3">Descripción</th>
                                    <th class="p-3">Fecha inicio</th>
                                    <th class="p-3">Fecha fin</th>
                                    <th class="p-3">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($proyecto = $result_proyectos->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center p-4 font-semibold"><?= $proyecto['id_proyecto'] ?></td>
                                        <td class="text-center p-4 font-semibold"><?= htmlspecialchars($proyecto['nombre']) ?></td>
                                        <td class="text-center p-4 font-semibold"><?= htmlspecialchars($proyecto['descripcion']) ?></td>
                                        <td class="text-center p-4 font-semibold"><?= $proyecto['fecha_inicio'] ?></td>
                                        <td class="text-center p-4 font-semibold"><?= $proyecto['fecha_fin'] ?></td>
                                        <td class="text-center p-4 font-semibold"><?= $proyecto['estado'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        
                    </div>
                <?php endif; ?>
                
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