<?php
// control de inicio de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 1) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: ../index.php");
    exit();
}
require_once("../database.php");

$usuario = $_SESSION['usuario'];
$con = crearConexion();

// Obtener proyectos del usuario actual (a través de la tabla intermedia)
$sql_proyectos = "SELECT DISTINCT p.* FROM proyectos p
                  INNER JOIN proyectos_usuarios pu ON p.id_proyecto = pu.id_proyecto
                  WHERE pu.usuario = ?";
$stmt_proyectos = $con->prepare($sql_proyectos);
$stmt_proyectos->bind_param("s", $usuario);
$stmt_proyectos->execute();
$proyectos = $stmt_proyectos->get_result();


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
    <div class="flex flex-col gap-10 rounded-xl p-10 w-[50em] max-w-[90%] bg-gray-300 justify-center items-center">
        <div class="flex flex-col items-center gap-6">
            <h2 class="font-bold text-orange-400 text-4xl underline">Bienvenido/a, <?php echo htmlspecialchars($usuario); ?></h2>
            <section class="flex flex-col justify-center items-center rounded-xl bg-gray-300 p-10 gap-3">

                <div class=" flex flex-col items-center justify-center relative p-2 ">
                    <img src="<?php echo htmlspecialchars($imagen_a_mostrar); ?>"
                        alt="Imagen Usuario"
                        class="w-[15em] h-[15em] rounded-lg"
                        id="profileImage" />
                    <form id="imageUploadForm" method="post" enctype="multipart/form-data" style="display:none;">
                        <input type="file" name="profileImage" id="imageInput" accept="image/*">
                        <input type="hidden" name="upload_image" value="1">
                    </form>
                    <button class="absolute right-1 top-1" id="uploadButton">
                        <img src="../../img/pencil-line.svg" alt="Editar imagen" class="bg-orange-400 rounded-lg p-1" />
                    </button>
                </div>
               
                    <p class="text-orange-400 text-xl font-bold">Proyectos Asignados:<span class="text-gray-600"> <?php echo htmlspecialchars($proyectos->num_rows); ?></span></p>
                

            </section>
        </div>
        <div class="flex flex-col p-10 w-full max-w-[90%] gap-10  justify-center items-center rounded-lg">
            
            <a href="jefeProyectos.php" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white text-center">
                Ir a los Proyectos Asignados
            </a>

            <a href="jefeReuniones.php" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white text-center">
                Ir a las Reuniones Asignadas
            </a>
            <a href="jefeTareas.php" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl w-60 shadow-lg cursor-pointer font-bold text-white text-center">
                Ir a las Tareas Asignadas
            </a>
            <form action="../logout.php" method="POST">
                <button type="submit"
                    class="p-2 bg-orange-400 rounded-xl shadow-lg cursor-pointer p-3 text-white hover:bg-orange-700">Cerrar
                    Sesión</button>
            </form>
        </div>
    </div>
</body>

</html>