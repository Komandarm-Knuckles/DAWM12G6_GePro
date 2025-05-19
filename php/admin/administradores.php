<?php

// control de inicio de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: ../index.php");
    exit();
}
require_once("../database.php");


$usuario = $_SESSION['usuario'];
$con = crearConexion();


$sql_tipo = "SELECT tipo FROM usuarios WHERE usuario = ?";
$stmt = $con->prepare($sql_tipo);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$tipo_usuario = $result->fetch_assoc()['tipo'];

if ($tipo_usuario != 0) {
    $_SESSION['error'] = "No tienes permisos de administrador";;
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Administradores</title>
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
<body class="w-full min-h-screen flex flex-col justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">

    <!-- saludo e imagen de perfil -->

<section class="flex flex-col items-center gap-6 p-10">
    <div class="flex flex-col items-center justify-center bg-gray-300 p-4 rounded-lg shadow-lg">

        <h1 class="font-bold bg-orange-400 md:text-5xl text-xl underline text-center p-4 rounded-lg mb-4">
            Bienvenido/a, <?php echo htmlspecialchars($usuario); ?>
        </h1>

        <!-- Imagen de usuario -->
        <div class="relative">
            <img src="<?php echo htmlspecialchars($imagen_a_mostrar); ?>" 
                 alt="Imagen Usuario" 
                 class="w-[15em] h-[15em] rounded-lg" 
                 id="profileImage" />
            
            <button class="absolute right-1 top-1" id="uploadButton">
                <img src="../../img/pencil-line.svg" alt="Editar imagen" class="bg-orange-400 rounded-lg p-1"/>
            </button>
        </div>

        <form id="imageUploadForm" method="post" enctype="multipart/form-data" style="display:none;">
            <input type="file" name="profileImage" id="imageInput" accept="image/*">
            <input type="hidden" name="upload_image" value="1">
        </form>

    </div>
</section>


    <!-- opciones administrador -->

    <section class="flex flex-col items-center gap-6 bg-gray-300 bg-opacity-90 p-10 rounded-xl w-full max-w-[22%] shadow-lg">
        <h2 class="font-bold text-lg bg-orange-400 md:text-2xl underline text-center p-2 rounded-lg">Elige una de las opciones:</h2>
            
        <span class="block h-0.5 w-full  bg-black opacity-40"></span>
        <div class="flex flex-col w-full gap-5 items-center">
            <a href="adminUsuarios.php" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white text-center">Usuarios</a>
            <span class="block h-0.5 w-full bg-black opacity-40"></span>

            <a href="adminCrearUsuario.php" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white text-center">Crear Usuarios</a>
            <span class="block h-0.5 w-full  bg-black opacity-40"></span>

            <a href="adminProyectos.php" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white text-center">Proyectos</a>
            <span class="block h-0.5 w-full  bg-black opacity-40"></span>

            <a href="adminReuniones.php" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white text-center">Reuniones</a>
            <span class="block h-0.5 w-full  bg-black opacity-40"></span>

            <a href="adminTareas.php" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white text-center">Tareas</a>
            <span class="block h-0.5 w-full  bg-black opacity-40"></span>
        </div>


    </section><br>

        <form action="../logout.php" method="POST">
            <button type="submit" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-40 shadow-lg cursor-pointer font-bold text-white">
                Cerrar Sesión
            </button>
        </form><br>
</body>

</html>
