<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: ../index.php");
    exit();
}

require_once("../database.php");
$con = crearConexion();

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
    header("Location: editarReunion.php?id=" . $id_reunion);
    exit;
}

$result_reuniones = $con->query("SELECT * FROM reuniones");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Reuniones Admin</title>
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
                    <img src="../../img/folder-git-2.svg" alt="imagenProyectos"/>
                    <a href="adminProyectos.php" class="font-bold text-white text-lg">Proyectos</a>
                </div>

                <div class="flex gap-2">
                    <img src="../../img/clipboard-list.svg" alt="imagentareas"/>
                    <a href="adminTareas.php" class="font-bold text-white text-lg">Tareas</a>
                </div>
            </section>

            <!-- Contenido principal -->
            <div class="flex flex-col py-5 min-h-screen gap-6 justify-center items-center bg-gray-300 w-full">
            <h1 class="font-bold text-lg bg-orange-400 md:text-5xl underline text-center p-2 rounded-lg">GESTIÓN DE REUNIONES</h1>
                <h3 class="text-xl font-bold text-lg bg-orange-400 md:text-2xl underline text-center p-2 rounded-lg">Reuniones Registradas:</h3>
        
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
                <button type="button" onclick="window.location.href='adminCrearReunion.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">CREAR REUNIÓN</button>

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
