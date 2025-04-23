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
</head>
<body>
    <h1>Crear nueva reunión</h1>
    <form method="POST">
        <label>Título:</label><br>
        <input type="text" name="titulo" required><br><br>

        <label>Descripción:</label><br>
        <textarea name="descripcion" required></textarea><br><br>

        <label>Fecha:</label><br>
        <input type="date" name="fecha" required><br><br>

        <label>Hora:</label><br>
        <input type="time" name="hora" required><br><br>

        <label>Proyecto:</label><br>
        <select name="id_proyecto" required>
            <option value="">Selecciona un proyecto</option>
            <?php while ($proyecto = $proyectos->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($proyecto['id_proyecto']); ?>">
                    <?php echo htmlspecialchars($proyecto['nombre']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Crear reunión</button>
    </form>
</body>
</html>
<?php #endregion ?>