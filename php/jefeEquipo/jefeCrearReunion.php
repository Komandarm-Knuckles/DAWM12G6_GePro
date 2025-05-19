<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 1) 
{
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: ../index.php");
    exit();
}

require_once("../database.php");
$con = crearConexion();

$proyectos = $con->query("SELECT id_proyecto, nombre FROM proyectos");

// Crear reunión
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $id_proyecto = $_POST['id_proyecto'];

    $stmt = $con->prepare("INSERT INTO reuniones (titulo, descripcion, fecha, hora, id_proyecto) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $titulo, $descripcion, $fecha, $hora, $id_proyecto);

    if ($stmt->execute()) 
    {
        echo "<script>alert('Reunión creada con éxito.');
              window.location.href='jefeReuniones.php'; </script>";
        exit();
    } 
    else 
    {
        echo "<script>alert('Error al crear la reunión: " . $stmt->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Crear Reunión Jefe Equipo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex flex-col max-w-[90%] w-[40em] bg-gray-300 p-8 rounded shadow-xl gap-6 justify-center items-center">

        <h1 class="text-4xl font-bold text-center underline text-orange-400">Crear Nueva Reunión</h1>
        <div class="flex justify-center items-center">
            <span class="block h-0.5 w-130 bg-black opacity-40"></span>
        </div>

        <form method="POST" class="flex flex-col w-full gap-6">
            <div class="flex flex-col w-full">
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" required class="p-2 border rounded-xl w-full" placeholder="Escribe un título...">
            </div>
            <div class="flex flex-col w-full">
            <label for="descripcion">Descripción:</label>
            <textarea name="descripcion" required class="p-2 border rounded-xl w-full" placeholder="Escribe una descripción..."></textarea>
            </div>
            <div class="flex flex-col w-full">
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" required class="p-2 border rounded-xl text-center w-full">
            </div>
            <div class="flex flex-col w-full">
            <label for="hora">Hora:</label>
            <input type="time" name="hora" required class="p-2 border rounded-xl text-center w-full">
            </div>
            <div class="flex flex-col w-full">
            <label for="id_proyecto">Proyecto:</label>
            <select name="id_proyecto" required class="p-2 border rounded-xl text-center w-full">
                <option value="">Selecciona un proyecto</option>
                <?php while ($proyecto = $proyectos->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($proyecto['id_proyecto']); ?>">
                        <?php echo htmlspecialchars($proyecto['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            </div> 
            <div class="flex justify-center items-center">
                
            <button type="submit" name="crear_tarea" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Crear Reunión</button>
            </div> 
        </form>
        <button type="button" onclick="window.location.href='jefeReuniones.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Volver</button>

    </div>
</body>
</html>
