<?php

// Control de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: ../index.php");
    exit();
}

require_once("../database.php");
$con = crearConexion();

// Función para obtener todos los jefes de equipo
function obtener_todos_los_jefes($con)
{
    $sql = "SELECT usuario, nombre FROM usuarios WHERE tipo = 1"; // Ajusta el campo y tabla según tu estructura
    return $con->query($sql);
}

// Función para crear un proyecto
function crear_proyecto($con, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado)
{
    if (empty($fecha_fin)) {
        $sql = "INSERT INTO proyectos (nombre, descripcion, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, NULL, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $descripcion, $fecha_inicio, $estado);
    } else {
        $sql = "INSERT INTO proyectos (nombre, descripcion, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sssss", $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado);
    }
    return $stmt->execute();
    if ($stmt->execute()) {
        echo "
        <script>
            alert('Proyecto creado correctamente.');
            window.location.href = 'adminProyectos.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('Error al crear Proyecto.');
            window.location.href = 'adminProyectos.php';
        </script>";
        exit;
    }
}

// Envio del formulario proyecto
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['crear_proyecto'])) {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
    $estado = $_POST['estado'];
    $jefe_equipo = $_POST['jefe_equipo'];
    $empleados_asignados = isset($_POST['empleados_asignados']) ? $_POST['empleados_asignados'] : [];

    if (!empty($nombre) && !empty($descripcion) && !empty($fecha_inicio) && !empty($estado) && !empty($jefe_equipo)) {
        // Validar fecha fin
        if (!empty($fecha_fin) && strtotime($fecha_fin) <= strtotime($fecha_inicio)) {
            echo "<script>alert('La fecha de fin no puede ser la de hoy.');
                 window.location.href = window.location.href; </script>";
            exit();
        }
        // Crear proyecto
        if (crear_proyecto($con, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado)) {
            // Obtener el id del proyecto recién creado
            $id_proyecto = $con->insert_id;
            // Insertar en la tabla intermedia los Jefes de Equipo
            $stmt = $con->prepare("INSERT INTO proyectos_usuarios (id_proyecto, usuario) VALUES (?, ?)");
            $stmt->bind_param("is", $id_proyecto, $jefe_equipo);
            $stmt->execute();

             // Asignar empleados seleccionados
        foreach ($empleados_asignados as $empleado) {
            $stmt = $con->prepare("INSERT INTO proyectos_usuarios (id_proyecto, usuario) VALUES (?, ?)");
            $stmt->bind_param("is", $id_proyecto, $empleado);
            $stmt->execute();
        }

            echo "<script>
        alert('Proyecto creado correctamente.');
        window.location.href = 'adminProyectos.php';
    </script>";
            exit();
        } else {
            echo "<script>
        alert('Error al crear Proyecto.');
        window.location.href = 'adminProyectos.php';
    </script>";
            exit();
        }
    }
}

$result_proyectos = obtener_todos_proyectos($con);
?>

<head>
    <title>Crear Proyectos Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex flex-col max-w-[90%] w-[40em] bg-gray-300 p-8 rounded shadow-xl gap-6">
            <h1 class="font-bold text-lg bg-orange-400 md:text-5xl underline text-center p-2 rounded-lg">CREAR NUEVO PROYECTO</h1>
        <div class="flex justify-center items-center">
            <span class="block h-0.5 w-130 bg-black opacity-40"></span>
        </div>
        <div class="flex flex-col justify-center items-center">
            <?php if (isset($error)) {
                echo "<p class='text-red-600'>$error</p>";
            } ?>
            <form method="POST" action="" class="flex flex-col w-full md:max-w-[85%] justify-center items-center  gap-6">
                <div class="flex flex-col w-full">
                    <label for ="nombre" class="font-bold">Nombre *</label>
                    <input type="text" name="nombre" placeholder="Nombre del Proyecto" required class="p-2 w-full border rounded" />
                </div>
                <div class="flex flex-col w-full">
                    <label for ="descripcion" class="font-bold">Descripción *</label>
                    <textarea name="descripcion" placeholder="Descripción del proyecto" required class="p-2 border w-full rounded"></textarea>
                </div>
                <div class="flex flex-col w-full">
                    <label for ="fecha_inicio" class="font-bold">Fecha de Inicio del Proyecto *</label>
                    <input type="date" name="fecha_inicio" required class="p-2 border text-center w-full rounded" />
                </div>
                <div class="flex flex-col w-full">
                    <label for ="fecha_fin" class="font-bold">Fecha Final del Proyecto (Opcional)</label>
                    <input type="date" name="fecha_fin" class="p-2 border text-center w-full rounded" />
                </div>
                <div class="flex flex-col w-full">
                    <label for ="jefe_equipo" class="font-bold">Jefe de Equipo *</label>
                    <select name="jefe_equipo" class="rounded-lg border-2 text-center w-full p-1" required>
                        <option value="">Selecciona un jefe de equipo</option>
                        <?php
                        $result_jefes = obtener_todos_los_jefes($con);
                        while ($jefe = $result_jefes->fetch_assoc()) {
                            echo "<option value='" . $jefe['usuario'] . "'>" . htmlspecialchars($jefe['nombre']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="flex flex-col  w-full">
                <label for ="empleados_asignados" class="font-bold">Asigna uno o varios Empleado *</label>
                <select name="empleados_asignados[]" multiple class="rounded-lg border-2 text-center w-full p-1" required>
                    <?php
                    $result_empleados = $con->query("SELECT usuario, nombre FROM usuarios WHERE tipo = 2");
                    while ($empleado = $result_empleados->fetch_assoc()) {
                        echo "<option value='" . $empleado['usuario'] . "'>" . htmlspecialchars($empleado['nombre']) . "</option>";
                    }
                    ?>
                </select>
                <small class="text-green-800 text-center">Pulsa Ctrl (o Cmd en Mac) para seleccionar varios</small>
                </div>
                <div class="flex flex-col w-full">
                    <label for ="estado" class="font-bold">Estado del Proyecto *</label>
                    <select name="estado" class="rounded-lg border-2 text-center w-full p-1" required>
                        <option value="pendiente" selected>Pendiente</option>
                        <option value="en proceso">En proceso</option>
                        <option value="completado">Completado</option>
                    </select>
                </div>
                <label for="campos">Los campos asignados con un <strong>(*)</strong> son <strong>Obligatorios</strong></label>
                <input type="hidden" name="crear_proyecto" value="1">
                <button type="submit" class="p-2 w-[15em] bg-orange-400 hover:bg-orange-700 cursor-pointer font-bold text-white rounded-xl">Crear Proyecto</button>
                <!-- Boton de volver -->
                <div class="flex justify-center items-center ">
                    <button type="button" onclick="window.location.href='adminProyectos.php'" class="bg-orange-400 text-white p-2 rounded-xl w-[10em] items-center cursor-pointer hover:bg-orange-700 font-bold">Cancelar</button>
                </div>
            </form>

            <span class="block h-0.5 w-full bg-black opacity-40"></span>

            <!-- Botones de volver a panel administrados-->
            <div class="flex justify-center items-center gap-10">
                <form action="../logout.php" method="POST" class="p-5 flex md:flex-row flex-col">
                    <button type="button" onclick="window.location.href='administradores.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-fit p-3 shadow-lg">Panel de Administrador</button>
                </form>
            </div>
        </div>
    </div>


    <?php
    $con->close();
    ?>
</body>