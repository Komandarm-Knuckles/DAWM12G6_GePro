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
// ----------------------------------------------------------------------------------------------------------------------------------------------------------
// Verificar si se está subiendo una imagen
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
        <!-- Script para la subida de la imagen de perfil de los usuarios -->
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
    </head>
    <body class="w-full min-h-screen flex flex-col justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex flex-col w-full justify-center items-center bg-opacity-25">
    

    <section class="flex flex-col w-fit p-10 gap-3">
        <h1 class="text-3xl text-center font-bold text-orange-600">Bienvenido/a, <?php echo htmlspecialchars($usuario);?></h1>
        <div class="flex flex-col w-full p-6">
            <div class="flex items-center justify-center bg-white p-10 bg-opacity-80 shadow-md rounded-lg gap-10">
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
        <img src="../../img/pencil-line.svg" alt="Editar imagen" class="bg-orange-600 rounded-lg p-1"/>
    </button>
                </div>
                <div class="flex flex-col text-xl justify-center gap-5">
                <p class="text-orange-400 font-bold">Nombre: <span class="text-gray-600"> <?php echo htmlspecialchars($usuario); ?></span></p>
                <p class="text-orange-400 font-bold">Proyectos Asignados:<span class="text-gray-600"> <?php echo htmlspecialchars($proyectos->num_rows); ?></span></p>
            </div>
            </div>
        </div>
    </section>

  
        
        <div class="flex flex-col p-10 w-full justify-center items-center gap-10 bg-gray-300 bg-opacity-25 rounded">
            <h2 class="font-bold text-center text-orange-400 text-3xl underline">Información de Proyectos</h2>
            <ul class="flex flex-wrap justify-center items-center gap-2 w-full">
        <?php if ($proyectos->num_rows == 0) { ?>
                        <span class="text-center font-bold">No hay proyectos.</span>
                    <?php } ?>
            <?php while ($proyecto = $proyectos->fetch_assoc()) { ?>
                <li class="flex gap-5">
                    <?php
                    echo
                    "<div class='flex w-full bg-gray-200 gap-2 rounded-lg shadow-lg p-5'>". 
                    "<div class='flex flex-col w-full gap-2 p-5'>". 
                        "<p class='font-bold text-orange-600'>-ID: <span class='text-black'>".htmlspecialchars($proyecto['id_proyecto'])."</p>".
                        "<p class='font-bold text-orange-600'>Nombre: <span class='text-black'>".htmlspecialchars($proyecto['nombre'])."</p>".
                        "<p class='font-bold text-orange-600'>Descripción: <span class='text-black'>".htmlspecialchars($proyecto['descripcion'])."</p>".
                        "<p class='font-bold text-orange-600'>Fecha de Inicio: <span class='text-black'>".htmlspecialchars($proyecto['fecha_inicio'])."</p>".
                        "<p class='font-bold text-orange-600'>Fecha de Fin: <span class='text-black'>".htmlspecialchars($proyecto['fecha_fin'])."</p>".
                        "<p class='font-bold text-orange-600'>Estado: <span class='text-black'>".htmlspecialchars($proyecto['estado'])."</p>"


                    ?>
                <?php echo "</div></div>"; ?>
                </li>
            <?php } ?>
        </ul>
            <h3 class="font-bold text-orange-400 text-3xl underline">Información de Reuniones</h3>
            <ul class="flex gap-2 flex-wrap justify-center items-center w-full">
        <?php if ($reuniones->num_rows == 0) { ?>
                        <span class="text-center font-bold">No hay reuniones.</span>
                    <?php } ?>
            <?php while ($reunion = $reuniones->fetch_assoc()) { ?>
                <li class="flex gap-5">
                    <?php
                    echo 
                    "<div class='flex w-full bg-gray-200 gap-2 rounded-lg shadow-lg p-5'>". 
                    "<div class='flex flex-col w-full gap-2 p-5'>".
                        " <p class='font-bold text-orange-400'>ID Reunion: <span class='text-black'>". htmlspecialchars($reunion['id_reunion'])."</span></p> ".
                        " <p class='font-bold text-orange-400'>Titulo: <span class='text-black'>".htmlspecialchars($reunion['titulo'])."</p> ". 
                        " <p class='font-bold text-orange-400'>Descripción: <span class='text-black'>".htmlspecialchars($reunion['descripcion'])."</p>".
                        " <p class='font-bold text-orange-400'>Fecha: <span class='text-black'>".htmlspecialchars($reunion['fecha'])."</p>".
                        " <p class='font-bold text-orange-400'>Hora: <span class='text-black'>".htmlspecialchars($reunion['hora'])."</p>".
                        " <p class='font-bold text-orange-400'>ID Proyecto: <span class='text-black'>".htmlspecialchars($reunion['id_proyecto'])."</p>"
                         
                         ?>
                <?php echo "</div></div>"; ?>
            </li>
            <?php } ?>
        </ul>

            <h3 class="font-bold text-orange-400 text-3xl underline">información de Tareas</h3>
            <ul class="flex gap-2 flex-wrap justify-center items-center w-full">
        <?php if ($tareas->num_rows == 0) { ?>
                        <span class="text-center font-bold">No hay tareas.</span>
                    <?php } ?>
            <?php while ($tarea = $tareas->fetch_assoc()) { ?>
                <li class="flex gap-5">
                    <?php
                    echo
                    "<div class='flex w-full bg-gray-200 gap-2 rounded-lg shadow-lg p-5'>". 
                    "<div class='flex flex-col w-full gap-2 p-5'>".
                        " <p class='font-bold text-orange-400'>-ID: <span class='text-black'>". htmlspecialchars($tarea['id_tarea'])."</span></p> ".
                        " <p class='font-bold text-orange-400'>-Nombre: <span class='text-black'>".htmlspecialchars($tarea['nombre'])."</p> ". 
                        " <p class='font-bold text-orange-400'>-Usuario Asignado: <span class='text-black'>".htmlspecialchars($tarea['usuario'])."</p>".
                        " <p class='font-bold text-orange-400'>-Descripció: <span class='text-black'>".htmlspecialchars($tarea['descripcion'])."</p>".
                        " <p class='font-bold text-orange-400'>Estado: <span class='text-black'>".htmlspecialchars($tarea['estado'])."</p>"
                     ?>
                    
                <?php echo "</div></div>"; ?>
                </li>
            <?php } ?>
        </ul>
            
            <form action="../../php/logout.php" method="POST" class="flex items-center justify-center">
                <button type="submit" class="p-2 bg-orange-400 rounded-xl shadow-lg cursor-pointer p-3 text-white hover:bg-orange-700">Cerrar Sesión</button>
            </form>
        
    </div>
    </div>
</body>
</html>


