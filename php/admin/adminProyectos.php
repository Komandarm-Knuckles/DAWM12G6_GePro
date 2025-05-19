<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: ../index.php");
    exit();
}

require_once("../database.php");
$con = crearConexion();

// Eliminar proyecto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_proyecto'])) {
    $id_proyecto = $_POST['id_proyecto'];

    if (borrar_proyecto($con, $id_proyecto)) {
        header("Location: " . $_SERVER['PHP_SELF']); 
        exit();
    } else {
        $error = "Error al eliminar el proyecto.";
    }
}

// Editar proyecto
if (isset($_POST['editar_proyecto'])) {
    $id_proyecto = $_POST['id_proyecto'];
    header("Location: editarProyectos.php?id=" . $id_proyecto);
    exit;
}

$result_proyectos = obtener_todos_proyectos($con);
$usuarios = obtener_todos_los_usuarios($con);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Proyectos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-full bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex w-full min-h-screen justify-center items-center">
        <div class="flex w-full md:flex-row flex-col justify-center items-stretch max-w-[90%]">
            <!-- Menú lateral -->
            <section class="flex md:flex-col md:w-80 w-full flex-wrap md:justify-start justify-center items-center bg-orange-400 md:gap-10 gap-5 pt-5">
                <img class="md:w-13 w-[10em]" src="../../img/LogoEmpresa.png" alt="logo Empresa"/>
                <div class="flex gap-2">
                    <img src="../../img/users.svg" alt="imagenProyectos"/>
                    <a href="adminUsuarios.php" class="font-bold text-white text-lg">Usuarios</a>
                </div>
                <div class="flex gap-2">
                    <img src="../../img/projector.svg" alt="imagenReuniones"/>
                    <a href="adminReuniones.php" class="font-bold text-white text-lg">Reuniones</a>
                </div>
                <div class="flex gap-2">
                    <img src="../../img/clipboard-list.svg" alt="imagentareas"/>
                    <a href="adminTareas.php" class="font-bold text-white text-lg">Tareas</a>
                </div>
            </section>

            <!-- Contenido principal -->
            <div class="flex flex-col min-h-screen gap-6 justify-center items-center bg-gray-300 w-full">

                <h1 class='font-bold text-4xl text-orange-400'>GESTIÓN DE PROYECTOS</h1>
                <h3 class='text-xl font-bold text-orange-400'>Proyectos registrados:</h3>

                <?php if ($usuarios->num_rows === 0): ?>
                    <p>No se encuentran usuarios</p>
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
                                    <th class="p-3">Acciones</th>
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
                                        <td class="flex justify-center items-center gap-3 pt-4">
                                            <form method="POST" action="">
                                                <input type="hidden" name="id_proyecto" value="<?= $proyecto['id_proyecto'] ?>">
                                                <button type="submit" name="editar_proyecto" class="cursor-pointer">
                                                    <img src='../../img/square-pen.png' alt='Editar' style='width: 20px; height: 20px;' class='hover:bg-green-500 hover:scale-105' />
                                                </button>
                                            </form>
                                            <form method="POST" action="">
                                                <input type="hidden" name="id_proyecto" value="<?= $proyecto['id_proyecto'] ?>">
                                                <button type="submit" name="eliminar_proyecto" onclick="return confirm('¿Estás seguro que quieres eliminar este proyecto?');" class="cursor-pointer">
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

                <!-- Botón crear proyecto -->
                <button type="button" onclick="window.location.href='adminCrearProyecto.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-fit p-3 shadow-lg">CREAR PROYECTO</button>

                <!-- Botones de navegación -->
                <div class="flex justify-center items-center gap-10">
                    <form action="../logout.php" method="POST" class="p-5 flex md:flex-row flex-col gap-10">
                        <button type="button" onclick="window.location.href='administradores.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-fit p-3 shadow-lg">Panel de Administrador</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php $con->close(); ?>
</body>
</html>