<?php

// Control de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: index.php");
    exit();
}

require_once("../database.php");
$con = crearConexion();

// Envio del formulario proyecto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_proyecto'])) {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null; 
    $estado = $_POST['estado'];

    if (!empty($nombre) && !empty($descripcion) && !empty($fecha_inicio) && !empty($estado)) {
        if (crear_proyecto($con, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado)) {
            header("Location: " . $_SERVER['PHP_SELF']); 
            exit();
        } else {
            $error = "Error al crear el proyecto.";
        }
    } else {
        $error = "Todos los campos son obligatorios excepto la fecha de fin.";
    }
}

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
?>

<head>
    <title>Panel de Proyectos Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex flex-col w-full max-w-[90%] justify-center items-center gap-20 pt-20">
        
        <!-- Tabla proyectos -->
        <div class="flex flex-col p-10 w-full max-w-[65%] gap-5 bg-gray-300 rounded">
            <div class="flex justify-center items-center bg-gray-300">
                <h2 class="font-bold text-orange-400 text-3xl underline">PROYECTOS</h2>
            </div>
            <div class="flex flex-col bg-gray-300 max-h-[300px] text-center gap-5 overflow-y-auto shadow-2xl w-full">
                <table class='styled-table w-full p-4 text-center rounded'>
                    <tr class='sticky bg-orange-400 text-white top-0 p-4'>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Fecha inicio</th>
                        <th>Fecha fin</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    <?php while ($proyectos = $result_proyectos->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $proyectos['id_proyecto']; ?></td>
                            <td><?php echo htmlspecialchars($proyectos['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($proyectos['descripcion']); ?></td>
                            <td><?php echo htmlspecialchars($proyectos['fecha_inicio']); ?></td>
                            <td><?php echo htmlspecialchars($proyectos['fecha_fin'] ?: 'No definida'); ?></td>
                            <td><?php echo htmlspecialchars($proyectos['estado']); ?></td>
                            <td class="flex justify-center items-center gap-3 pt-4">
                                <form method="POST" action="">
                                    <input type="hidden" name="id_proyecto" value="<?php echo $proyectos['id_proyecto']; ?>">
                                    <button type="submit" name="editar_proyecto" class="cursor-pointer">
                                        <img src='../../img/square-pen.png' alt='Editar' style='width: 20px; height: 20px;' class='hover:bg-green-500 hover:scale-105' />
                                    </button>
                                </form>

                                <form method="POST" action="">
                                    <input type="hidden" name="id_proyecto" value="<?php echo $proyectos['id_proyecto']; ?>">
                                    <button type="submit" name="eliminar_proyecto" onclick="return confirm('¿Estás seguro que quieres eliminar este proyecto?');" class="cursor-pointer">
                                        <img src="../../img/trash-2.png" alt="Eliminar" style="width: 20px; height: 20px;" class="hover:bg-red-500 hover:scale-105">
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

        <!-- Crear Nuevo Proyecto -->

        <div class="flex flex-col gap-10 p-10 w-full max-w-[65%] bg-gray-300 justify-center items-center">
            <h2 class="font-bold text-orange-400 text-3xl underline">NUEVO PROYECTO</h2>

            
            <?php if (isset($error)) { echo "<p class='text-red-600'>$error</p>"; } ?>

            <form method="POST" action="" class="flex flex-col w-full gap-6">
                <input type="text" name="nombre" placeholder="Nombre" required class="p-2 border rounded" />

                <textarea name="descripcion" placeholder="Descripción" required class="p-2 border rounded"></textarea>
                Fecha Inicio:
                <input type="date" name="fecha_inicio" required class="p-2 border rounded" />
                Fecha Fin:
                <input type="date" name="fecha_fin" class="p-2 border rounded" />

                <select name="estado" class="w-[20em] rounded-lg p-1">
                    <option value="pendiente">Pendiente</option>
                    <option value="en proceso">En proceso</option>
                    <option value="completado">Completado</option>
                </select>

                <button type="submit" name="crear_proyecto" class="p-2 bg-orange-400 hover:bg-orange-700 cursor-pointer text-white rounded">Crear Nuevo Proyecto</button>
            </form>

            <!-- Botones de volver y logOut -->
            <div class="flex justify-center items-center gap-10">
                <form action="../logout.php" method="POST" class="p-5 flex flex-col gap-10">
                <button type="button" onclick="window.location.href='administradores.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Volver</button>
                <button type="submit" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-40 shadow-lg cursor-pointer font-bold text-white">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </div>

    <?php
    $con->close();
    ?>
</body>
