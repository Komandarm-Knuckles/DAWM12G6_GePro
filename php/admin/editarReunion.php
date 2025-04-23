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
</head>
<body>
    <h1>Editar reunión</h1>
    <form method="POST">
        <label>Título:</label><br>
        <input type="text" name="titulo" value="<?php echo htmlspecialchars($reunion['titulo']); ?>" required><br><br>

        <label>Descripción:</label><br>
        <textarea name="descripcion" required><?php echo htmlspecialchars($reunion['descripcion']); ?></textarea><br><br>

        <label>Fecha:</label><br>
        <input type="date" name="fecha" value="<?php echo htmlspecialchars($reunion['fecha']); ?>" required><br><br>

        <label>Hora:</label><br>
        <input type="time" name="hora" value="<?php echo htmlspecialchars($reunion['hora']); ?>" required><br><br>

        <label>Proyecto:</label><br>
        <select name="id_proyecto" required>
            <option value="">Selecciona un proyecto</option>
            <?php while ($proyecto = $proyectos->fetch_assoc()): ?>
                <option value="<?php echo $proyecto['id_proyecto']; ?>" <?php if ($proyecto['id_proyecto'] == $reunion['id_proyecto']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($proyecto['nombre']); ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>

        <button type="submit">Guardar cambios</button>
    </form>
</body>
</html>
<?php #endregion ?>