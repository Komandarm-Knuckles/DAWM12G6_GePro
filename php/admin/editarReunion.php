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

#region Comprobación de que se haya seleccionado una reunión
if (!isset($_GET['id'])) 
{
    echo "Error: No se ha seleccionado una reunión para editar.";
    exit();
}
$id_reunion = $_GET['id'];
#endregion

#region Recogida de datos para modificación
if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $id_proyecto = $_POST['id_proyecto'];

    $stmt = $con->prepare("UPDATE reuniones SET titulo=?, descripcion=?, fecha=?, hora=?, id_proyecto=? WHERE id_reunion=?");
    $stmt->bind_param("ssssii", $titulo, $descripcion, $fecha, $hora, $id_proyecto, $id_reunion);
    $stmt->execute();

    header("Location: adminReuniones.php");
    exit();
}
#endregion

#region Recogida de datos de la reunión a modificar para poblar los inputs
$stmt = $con->prepare("SELECT * FROM reuniones WHERE id_reunion = ?");
$stmt->bind_param("i", $id_reunion);
$stmt->execute();
$resultado = $stmt->get_result();
$reunion = $resultado->fetch_assoc();
$proyectos = $con->query("SELECT id_proyecto, nombre FROM proyectos");
#endregion
?>

<?php #region HTML ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Editar Reunión</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
<div class="flex flex-col w-full max-w-[90%] justify-center items-center gap-20">

    <h1 class="font-bold text-orange-600 text-4xl underline mt-10">Editar reunión</h1>
    <form method="POST" class="flex flex-col w-full max-w-[40em] text-center bg-white p-8 shadow-xl gap-6">
        Nombre de la reunión:
        <input type="text" name="titulo" class="text-center border-2 rounded-lg p-2 border-black" value="<?php echo htmlspecialchars($reunion['titulo']); ?>" required>
        Descripción:
        <textarea name="descripcion" class="text-center border-2 rounded-lg p-2 border-black" required><?php echo htmlspecialchars($reunion['descripcion']); ?></textarea>

        Fecha:
        <input type="date" name="fecha" class="text-center border-2 rounded-lg p-2 border-black" value="<?php echo htmlspecialchars($reunion['fecha']); ?>" required>

        Hora:
        <input type="time" name="hora" class="text-center border-2 rounded-lg p-2 border-black" value="<?php echo htmlspecialchars($reunion['hora']); ?>" required>

        <select name="id_proyecto" class="text-center border-2 rounded-lg p-2 border-black" required>
            <option value="">Selecciona un proyecto</option>
            <?php while ($proyecto = $proyectos->fetch_assoc()): ?>
                <option value="<?php echo $proyecto['id_proyecto']; ?>" <?php if ($proyecto['id_proyecto'] == $reunion['id_proyecto']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($proyecto['nombre']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <div class="flex justify-center items-center gap-10">
        <button type="button" onclick="window.location.href='adminReuniones.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Cancelar</button>
        <button type="submit" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Guardar cambios</button>
        </div>
    </form>
    </div>
</body>
</html>
<?php #endregion ?>