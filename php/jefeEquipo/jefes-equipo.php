<?php
// control de inicio de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 1) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: index.php");
    exit();
}
require_once("../database.php");

$usuario = $_SESSION['usuario'];
$con = crearConexion();

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
$sql_reuniones = "SELECT DISTINCT r.* FROM reuniones r 
                  INNER JOIN tareas t ON r.id_proyecto = t.id_proyecto 
                  WHERE t.usuario = ?";
$stmt_reuniones = $con->prepare($sql_reuniones);
$stmt_reuniones->bind_param("s", $usuario);
$stmt_reuniones->execute();
$reuniones = $stmt_reuniones->get_result();

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

// ELIMINAR TAREAS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_tarea'])) {
    $id_tarea = intval($_POST['eliminar_tarea']);
    $stmt = $con->prepare("DELETE FROM tareas WHERE id_tarea = ?");
    $stmt->bind_param("i", $id_tarea);
    $stmt->execute();
    header("Location: jefes-equipo.php");
    exit();
}

// ELIMINAR REUNIONES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_reunion'])) {
    $id_reunion = intval($_POST['eliminar_reunion']);
    $stmt = $con->prepare("DELETE FROM reuniones WHERE id_reunion = ?");
    $stmt->bind_param("i", $id_reunion);
    $stmt->execute();
    header("Location: jefes-equipo.php");
    exit();
}

// EDITAR TAREAS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_tarea'])) {
    $id_tarea = intval($_POST['id_tarea']);
    header("Location: editarTareasJefeEquipo.php?id=" . $id_tarea);
    exit();
}

// EDITAR REUNIONES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_reunion'])) {
    $id_reunion = intval($_POST['id_reunion']);
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];

    $stmt = $con->prepare("UPDATE reuniones SET titulo=?, descripcion=?, fecha=? WHERE id_reunion=?");
    $stmt->bind_param("sssi", $titulo, $descripcion, $fecha, $id_reunion);
    $stmt->execute();
    header("Location: editarReunionesJefeEquipo.php?id=" . urlencode($id_reunion));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_image'])) {
    // Verificar si hay algún error en la subida
    if ($_FILES['profileImage']['error'] == 0) {
        $usuario = $_SESSION['usuario']; // Asegúrate de tener el ID del usuario en la sesión
        
        // Directorio donde se guardarán las imágenes
        $upload_dir = '../../uploads/profile_images/';
        
        // Crear el directorio si no existe
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Obtener la extensión del archivo
        $file_extension = pathinfo($_FILES['profileImage']['name'], PATHINFO_EXTENSION);
        
        // Generar un nombre único para la imagen
        $new_file_name = 'user_' . $usuario . '_' . time() . '.' . $file_extension;
        
        // Ruta completa donde se guardará la imagen en el sistema de archivos
$upload_path = $upload_dir . $new_file_name;

// Ruta relativa para la base de datos (URL que usará el navegador)
$image_path = '../../uploads/profile_images/' . $new_file_name;

// Mover el archivo subido al directorio destino
if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $upload_path)) {
    // Actualizar la ruta de la imagen en la base de datos
    $stmt = $con->prepare("UPDATE usuarios SET imagen_perfil = ? WHERE usuario = ?");
    $stmt->bind_param("ss", $image_path, $usuario);
            
            if ($stmt->execute()) {
                // Redireccionar para evitar reenvío del formulario
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "Error al actualizar la base de datos: " . $stmt->error;
            }
        } else {
            echo "Error al subir la imagen.";
        }
    } else {
        echo "Error: " . $_FILES['profileImage']['error'];
    }
}

// Obtener la ruta de la imagen del usuario desde la base de datos
$usuario = $_SESSION['usuario'];
$imagen_perfil = ''; 

$stmt = $con->prepare("SELECT imagen_perfil FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Solo asignar si existe y no está vacío
    if (!empty($row['imagen_perfil'])) {
        $imagen_perfil = $row['imagen_perfil'];
    }
}
// Define una imagen por defecto
$default_image = '../../img/perfilPorDefecto.png';

// Verificar si la imagen de perfil está vacía o no existe
if (empty($imagen_perfil) || !file_exists($imagen_perfil)) {
    $imagen_a_mostrar = $default_image;
} else {
    $imagen_a_mostrar = $imagen_perfil;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel de Jefe de Equipo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const uploadButton = document.getElementById('uploadButton');
    const imageInput = document.getElementById('imageInput');
    const imageForm = document.getElementById('imageUploadForm');
    
    uploadButton.addEventListener('click', function() {
        imageInput.click();
    });
    
    imageInput.addEventListener('change', function() {
        if (imageInput.files.length > 0) {
            imageForm.submit();
        }
    });
});
</script>
<body class="w-full min-h-screen flex flex-col justify-center gap-4 items-center bg-cover bg-center bg-fixed z-10 py-10 bg-[url('../../img/pixels14.jpg')]">
        <h2 class="font-bold text-orange-400 text-4xl underline">Bienvenido/a, <?php echo htmlspecialchars($usuario); ?></h2>

    <section class="flex flex-col md:flex-row justify-center items-center bg-gray-300 p-10 gap-3">
            
                <div class=" flex flex-col items-center justify-center relative p-2 ">
                <img src="<?php echo htmlspecialchars($imagen_a_mostrar); ?>" 
     alt="Imagen Usuario" 
     class="w-[15em] h-[15em] rounded-lg" 
     id="profileImage"
     
/>
                <form id="imageUploadForm" method="post" enctype="multipart/form-data" style="display:none;">
        <input type="file" name="profileImage" id="imageInput" accept="image/*">
        <input type="hidden" name="upload_image" value="1">
    </form>
    <button class="absolute right-1 top-1" id="uploadButton">
        <img src="../../img/pencil-line.svg" alt="Editar imagen" class="bg-orange-400 rounded-lg p-1"/>
    </button>
                </div>
                <div class="flex flex-col text-xl justify-center gap-5">
                <p class="text-orange-400 font-bold">Nombre: <span class="text-gray-600"> <?php echo htmlspecialchars($usuario); ?></span></p>
                <p class="text-orange-400 font-bold">Proyectos Asignados:<span class="text-gray-600"> <?php echo htmlspecialchars($proyectos->num_rows); ?></span></p>
            </div>
            
    </section>

        <div class="flex flex-col p-10 gap-10 bg-white bg-opacity-70 justify-center items-center shadow-md rounded-lg">
        <h2 class="font-bold text-orange-500 text-4xl underline">Proyectos</h2>
        <ul class="flex flex-wrap gap-2 justify-center items-center w-full">
        <?php if ($proyectos->num_rows == 0) { ?>
                        <span class="text-center font-bold">No hay proyectos.</span>
                    <?php } ?>
            <?php while ($proyecto = $proyectos->fetch_assoc()) { ?>
                <li class="flex gap-5">
                    <?php
                    echo
                    "<div class='flex w-full bg-gray-300 gap-2 rounded-lg shadow-lg p-5'>". 
                    "<div class='flex flex-col w-full gap-2 p-5'>". 
                        "<p class='font-bold text-orange-400'>-ID: <span class='text-black'>".htmlspecialchars($proyecto['id_proyecto'])."</p>".
                        "<p class='font-bold text-orange-400'>Nombre: <span class='text-black'>".htmlspecialchars($proyecto['nombre'])."</p>".
                        "<p class='font-bold text-orange-400'>Descripción: <span class='text-black'>".htmlspecialchars($proyecto['descripcion'])."</p>".
                        "<p class='font-bold text-orange-400'>Fecha de Inicio: <span class='text-black'>".htmlspecialchars($proyecto['fecha_inicio'])."</p>".
                        "<p class='font-bold text-orange-400'>Fecha de Fin: <span class='text-black'>".htmlspecialchars($proyecto['fecha_fin'])."</p>".
                        "<p class='font-bold text-orange-400'>Estado: <span class='text-black'>".htmlspecialchars($proyecto['estado'])."</p>"


                    ?>
                <?php echo "</div></div>"; ?>
                </li>
            <?php } ?>
        </ul>

        <h3 class="font-bold text-orange-500 text-3xl underline">Reuniones</h3>
        <ul class="flex flex-wrap gap-2 justify-center items-center w-full">
        <?php if ($reuniones->num_rows == 0) { ?>
                        <span class="text-center font-bold">No hay reuniones.</span>
                    <?php } ?>
            <?php while ($reunion = $reuniones->fetch_assoc()) { ?>
                <li class="flex gap-5">
                    <?php
                    echo " <p class='font-bold text-orange-400'>-Nombre de la Reunión:".htmlspecialchars($reunion['titulo'])."</p>".
                        " <p class='font-bold text-orange-400'>Fecha:". $reunion['fecha']."</p>".
                        "<p class='font-bold text-orange-400'>Hora:". $reunion['hora']."</p>". 
                        "<p class='font-bold text-orange-400'>Descripción:".htmlspecialchars($reunion['descripcion'])."</p>"
                         ?>
                   
                    <div class="flex gap-1">
                        <form method="GET" action="editarReunionesJefeEquipo.php">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($reunion['id_reunion']); ?>">
                            <button type="submit" class="cursor-pointer">
                                <img src='../../img/square-pen.png' alt='Editar' style='width: 20px; height: 20px;'
                                    class='hover:bg-green-500 hover:scale-105' />
                            </button>
                        </form>

                        <form method="POST" action="">
                            <input type="hidden" name="eliminar_reunion" value="<?php echo $reunion['id_reunion']; ?>">
                            <button type="submit"
                                onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');"
                                class="cursor-pointer">
                                <img src="../../img/trash-2.png" alt="Eliminar" style="width: 20px; height: 20px;"
                                    class="hover:bg-red-500 hover:scale-105">
                            </button>
                        </form>
                    </div>
                </li>
            <?php } ?>
        </ul>

        <h3 class="font-bold text-orange-500 text-3xl underline">Tareas</h3>
        <ul class="flex flex-wrap gap-2 justify-center items-center w-full">
        <?php if ($tareas->num_rows == 0) { ?>
                        <span class="text-center font-bold">No hay tareas.</span>
                    <?php } ?>
            <?php while ($tarea = $tareas->fetch_assoc()) { ?>
                <li class="flex gap-5">
                    <?php
                    echo
                    "<div class='flex w-full bg-gray-300 gap-2 rounded-lg shadow-lg p-5'>". 
                    "<div class='flex flex-col w-full gap-2 p-5'>".
                        " <p class='font-bold text-orange-400'>-ID: <span class='text-black'>". htmlspecialchars($tarea['id_tarea'])."</span></p> ".
                        " <p class='font-bold text-orange-400'>-Nombre: <span class='text-black'>".htmlspecialchars($tarea['nombre'])."</p> ". 
                        " <p class='font-bold text-orange-400'>-Usuario Asignado: <span class='text-black'>".htmlspecialchars($tarea['usuario'])."</p>".
                        " <p class='font-bold text-orange-400'>-Descripció: <span class='text-black'>".htmlspecialchars($tarea['descripcion'])."</p>".
                        " <p class='font-bold text-orange-400'>Estado: <span class='text-black'>".htmlspecialchars($tarea['estado'])."</p>"
                     ?>
                    
                    <div class="flex gap-3">
                        <span class="font-bold text-orange-400">Acciones:</span>
                        <form method="POST" action="">
                            <input type="hidden" name="id_tarea" value="<?php echo $tarea['id_tarea']; ?>">
                            <button type="submit" name="editar_tarea" class="cursor-pointer">
                                <img src='../../img/square-pen.png' alt='Editar' style='width: 20px; height: 20px;'
                                    class='hover:bg-green-500 hover:scale-105' />
                            </button>
                        </form>
                        <form method="POST" action="">
                            <input type="hidden" name="eliminar_tarea" value="<?php echo $tarea['id_tarea']; ?>">
                            <button type="submit"
                                onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');"
                                class="cursor-pointer">
                                <img src="../../img/trash-2.png" alt="Eliminar" style="width: 20px; height: 20px;"
                                    class="hover:bg-red-500 hover:scale-105">
                            </button>
                        </form>
                    </div>
                <?php echo "</div></div>"; ?>
                </li>
            <?php } ?>
        </ul>

        <h3 class="font-bold text-orange-500 text-3xl underline">Crear Reunión</h3>
        <form method="post" class="flex flex-col w-[40em]  gap-2">
            <input type="text" name="titulo" placeholder="Título" required class="p-2 border rounded" />
            <textarea name="descripcion" placeholder="Descripción" required class="p-2 border rounded"></textarea>
            <input type="date" name="fecha" required class="p-2 border rounded" />
            <input type="time" name="hora" required class="p-2 border rounded" />
            <input type="number" name="id_proyecto" placeholder="ID Proyecto" required class="p-2 border rounded" />
            <button type="submit" name="crear_reunion"
                class="p-2 bg-orange-500 hover:bg-orange-700 cursor-pointer text-white rounded">Crear Reunión</button>
        </form>

        <h3 class="font-bold text-orange-500 text-3xl underline">Crear Tarea</h3>
        <form method="post" class="flex flex-col w-[40em]  gap-2">
            <input type="text" name="nombre" placeholder="Nombre" required class="p-2 border rounded" />
            <textarea name="descripcion" placeholder="Descripción" required class="p-2 border rounded"></textarea>
            <input type="number" name="id_proyecto" placeholder="ID Proyecto" required class="p-2 border rounded" />
            <input type="text" name="usuario_asignado" placeholder="Usuario asignado" required
                class="p-2 border rounded" />
            <input type="date" name="fecha_vencimiento" required class="p-2 border rounded" />
            <button type="submit" name="crear_tarea"
                class="p-2 bg-orange-500 hover:bg-orange-700 cursor-pointer text-white rounded">Crear Tarea</button>
        </form>
        <form action="../logout.php" method="POST">
            <button type="submit"
                class="p-2 bg-orange-500 rounded-xl shadow-lg cursor-pointer p-3 text-white hover:bg-orange-700">Cerrar
                Sesión</button>
        </form>
        </div>

</body>

</html>