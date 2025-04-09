    <?php
    // control de inicio de sesión
    session_start();
    if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 2) {
        $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
        header("Location: index.php");
        exit();
    }
    require_once("../database.php");

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

    // TODO - EXPLICAR ESTO, LA TAREA SE ASIGNA A UN PROYECTO Y A UN USUARIO, CON LO CUAL, SI LA TAREA LA ASOCIAMOS A EL USUARIO 1, AL USUARIO 1 LE APARECERÁ EL PROYECTO EL CUAL ESTA ASIGNADO A ESA TAREA
// Obtener tareas asignadas al usuario actual
$sql_tareas = "SELECT * FROM tareas WHERE usuario = ?";
$stmt_tareas = $con->prepare($sql_tareas);
$stmt_tareas->bind_param("s", $usuario);
$stmt_tareas->execute();
$tareas = $stmt_tareas->get_result();

// Obtener proyectos del usuario actual (a través de sus tareas)
$sql_proyectos = "SELECT DISTINCT p.* FROM proyectos p 
                  INNER JOIN tareas t ON p.id_proyecto = t.id_proyecto 
                  WHERE t.usuario = ?";
$stmt_proyectos = $con->prepare($sql_proyectos);
$stmt_proyectos->bind_param("s", $usuario);
$stmt_proyectos->execute();
$proyectos = $stmt_proyectos->get_result();

// Obtener reuniones relacionadas con los proyectos del usuario
$sql_reuniones = "SELECT r.* FROM reuniones r 
                  INNER JOIN tareas t ON r.id_proyecto = t.id_proyecto 
                  WHERE t.usuario = ?";
$stmt_reuniones = $con->prepare($sql_reuniones);
$stmt_reuniones->bind_param("s", $usuario);
$stmt_reuniones->execute();
$reuniones = $stmt_reuniones->get_result();
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

    <div class="flex flex-col justify-center items-center">
    <h1>Bienvenido/a, <?php echo htmlspecialchars($usuario);?></h1>





    <section class="bg-white dark:bg-gray-900">
        <div class="container px-6 py-8">
            <h2 class="text-2xl font-bold text-gray-800">Información del Usuario</h2>
            <div class="flex  bg-white items-center justify-center shadow-md rounded-lg p-6">
                <div class=" flex flex-col items-center justify-center relative ">
                <img src="../../img/pixels1.jpg" alt="Imagen Usuaruio" class="w-40 h-40 rounded-lg">
                <button class="absolute right-1 top-1 bg-white"><img src="../../img/pencil-line.svg" alt=""/></button>
                </div>´
                <div class="flex flex-col items-center justify-center gap-5">
                <p class="text-gray-600 font-bold">Nombre: <?php echo htmlspecialchars($usuario); ?></p>
                <p class="text-gray-600">Tipo de Usuario: <?php echo htmlspecialchars($tipo_usuario); ?></p>
                <p class="text-gray-600">Proyectos Asignados: <?php echo htmlspecialchars($proyectos->num_rows); ?></p>
            </div>
            </div>
        </div>
    </section>
        









        <div class="flex flex-col p-10 w-full max-w-[65%] gap-20 bg-gray-300 rounded">
            <div class='flex flex-col bg-gray-300 max-h-[300px] text-center gap-5 overflow-y-auto shadow-2xl bg-color w-full'>
                <h2 class="font-bold text-orange-400 text-3xl underline">Información de Proyectos</h2>
                <table class='styled-table w-full p-4 text-center rounded'>
                    <tr class='sticky bg-orange-400 text-white top-0 p-4 bg-gray-300'>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        
                    </tr>
                    <?php while ($proyecto = $proyectos->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $proyecto['id_proyecto']; ?></td>
                        <td><?php echo htmlspecialchars($proyecto['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($proyecto['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($proyecto['estado']); ?></td>

                    </tr>
                    <?php } ?>
                </table>
            </div>
            <div class='flex flex-col bg-gray-300 max-h-[300px] text-center gap-5 overflow-y-auto shadow-2xl bg-color w-full'>
            <h3 class="font-bold text-orange-400 text-3xl underline">Información de Reuniones</h3>
                <table class='styled-table w-full p-4 text-center rounded'>
                    <tr class='sticky bg-orange-400 text-white top-0 p-4 bg-gray-300'>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>ID Proyecto</th>

                    </tr>
                    <?php while ($reunion = $reuniones->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $reunion['id_reunion']; ?></td>
                        <td><?php echo htmlspecialchars($reunion['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($reunion['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($reunion['hora']); ?></td>
                        <td><?php echo htmlspecialchars($reunion['id_proyecto']); ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <div class='flex flex-col bg-gray-300 max-h-[300px] text-center gap-5 overflow-y-auto shadow-2xl bg-color w-full'>
            <h3 class="font-bold text-orange-400 text-3xl underline">información de Tareas</h3>
                <table class='styled-table w-full p-4 text-center rounded'>
                    <tr class='sticky bg-orange-400 text-white top-0 p-4 bg-gray-300'>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>ID Proyecto</th>
                    </tr>
                    <?php while ($tarea = $tareas->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tarea['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($tarea['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($tarea['estado']); ?></td>
                        <td><?php echo htmlspecialchars($tarea['id_proyecto']); ?></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <form action="../../php/logout.php" method="POST" class="flex items-center justify-center">
                <button type="submit" class="p-2 bg-orange-400 rounded-xl shadow-lg cursor-pointer p-3 text-white hover:bg-orange-700">Cerrar Sesión</button>
            </form>
        
    </div>
    </div>
</body>
</html>


