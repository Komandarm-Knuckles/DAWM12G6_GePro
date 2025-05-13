<?php
require_once("../database.php");
$con = crearConexion();

#region Control de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) 
{
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: index.php");
    exit();
}
#endregion

#region Recogida de datos para inserción
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $id_proyecto = $_POST['id_proyecto'];

    $stmt = $con->prepare("INSERT INTO reuniones (titulo, descripcion, fecha, hora, id_proyecto) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $titulo, $descripcion, $fecha, $hora, $id_proyecto);
    $stmt->execute();

    header("Location: adminReuniones.php");
    exit();
}
#endregion

#region Datos para el dropdown dinámico de proyectos
$proyectos = $con->query("SELECT id_proyecto, nombre FROM proyectos");
#endregion

?>

<?php #region HTML ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Crear Reunión</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
<div class="flex flex-col max-w-[90%] w-[40em] bg-white p-8 rounded shadow-xl gap-6">
    
<h1 class="text-4xl font-bold text-center underline text-orange-400">Crear Nueva Reunión</h1>
<div class="flex justify-center items-center">
            <span class="block h-0.5 w-130 bg-black opacity-40"></span>
        </div>
    <form method="POST">
        Título:
        <input type="text" name="titulo" required class=" p-2 border rounded w-full">

        Descripción:
        <textarea name="descripcion" required class=" p-2 border rounded w-full" ></textarea>

        Fecha:
        <input type="date" name="fecha" required class=" p-2 border rounded w-full">

        Hora:
        <input type="time" name="hora" required class=" p-2 border rounded w-full">

        Proyecto:
        <select name="id_proyecto" required class=" p-2 border rounded w-full">
            <option value="">Selecciona un proyecto</option>
            <?php while ($proyecto = $proyectos->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($proyecto['id_proyecto']); ?>">
                    <?php echo htmlspecialchars($proyecto['nombre']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <br>
        <br>
        <div class="flex justify-center items-center gap-10 ">
                <input type="submit" value="Crear Reunion" class="bg-orange-400 text-white p-2 rounded w-[10em] items-center cursor-pointer hover:bg-orange-700 font-bold" />
        </div>
    </form>

        <!-- Botones de volver a panel administrados o panel usuario -->
        <div class="flex justify-center items-center gap-10">
            <form action="../logout.php" method="POST" class="p-5 flex md:flex-row flex-col gap-10">
            <button type="button" onclick="window.location.href='administradores.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Panel de Administrador</button>
            <button type="button" onclick="window.location.href='adminUsuarios.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Panel de Usuarios</button>
            </form>
        </div>
</div>
</body>
</html>
<?php #endregion ?>