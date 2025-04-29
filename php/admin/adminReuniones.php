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

#region Eliminar reunión seleccionada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_reunion'])) 
{
    $id_reunion = $_POST['eliminar_reunion'];
    borrar_reunion($con, $id_reunion);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
#endregion

#region Recogida de datos de las reuniones para poblar la tabla dinámica
$resultado = $con->query("SELECT * FROM reuniones");
#endregion

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Administrar Reuniones</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <h1>Lista de Reuniones</h1>
    <table>
        <thead>
            <tr>
                <?php
                #region Tabla dinámica
                if ($resultado->num_rows > 0) 
                {
                    $primeraFila = $resultado->fetch_assoc();
                    foreach ($primeraFila as $columna => $valor) 
                    {
                        echo "<th>" . htmlspecialchars($columna) . "</th>";
                    }
                    echo "<th>Acciones</th>";
                    echo "</tr></thead><tbody>";
                    echo "<tr>";
                    foreach ($primeraFila as $valor) 
                    {
                        echo "<td>" . htmlspecialchars($valor) . "</td>";
                    }
                    echo "<td>
                            <form method='GET' action='editarReunion.php' style='display:inline;'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($primeraFila['id_reunion']) . "'>
                                <button type='submit'>Editar</button>
                            </form>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='eliminar_reunion' value='" . htmlspecialchars($primeraFila['id_reunion']) . "'>
                                <button type='submit'>Eliminar</button>
                            </form>
                          </td>";
                    echo "</tr>";
                    while ($fila = $resultado->fetch_assoc()) 
                    {
                        echo "<tr>";
                        foreach ($fila as $valor) 
                        {
                            echo "<td>" . htmlspecialchars($valor) . "</td>";
                        }
                        echo "<td>
                                <form method='GET' action='editarReunion.php' style='display:inline;'>
                                    <input type='hidden' name='id' value='" . htmlspecialchars($fila['id_reunion']) . "'>
                                    <button type='submit'>Editar</button>
                                </form>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='eliminar_reunion' value='" . htmlspecialchars($fila['id_reunion']) . "'>
                                    <button type='submit'>Eliminar</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } 
                else 
                {
                    echo "<th>No hay reuniones registradas.</th>";
                }
                #endregion
                ?>
            </tr>
        </tbody>
    </table>

    <br>
    <!-- Botón de creación de reuniones -->
    <form method="GET" action="adminCrearReunion.php">
        <button type="submit">Crear nueva reunión</button>
    </form>
</body>
</html>
