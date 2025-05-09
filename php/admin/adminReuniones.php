<?php
require_once("../database.php");
$con = crearConexion();

#region Control de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: index.php");
    exit();
}
#endregion

#region Eliminar reunión seleccionada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_reunion'])) {
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

<body class="w-full min-h-screen flex justify-center items-center bg-cover bg-center bg-fixed z-10 bg-[url('../../img/pixels14.jpg')]">
    <div class="flex flex-col w-full max-w-[90%] justify-center items-center gap-10 pt-20">
        <h1 class="font-bold text-orange-600 text-4xl underline">Lista de Reuniones</h1>
        <div class="flex flex-col md:p-10 p-4 w-full  gap-5 bg-gray-300 rounded">
            <div class="flex justify-center items-center bg-gray-300">
                <div class="flex flex-col bg-gray-300 max-h-[300px] text-center gap-5 overflow-y-auto shadow-2xl w-full">

                    <table class='styled-table w-full p-4 text-center rounded'>
                        <thead>
                            <tr class='sticky bg-orange-400 text-white top-0 p-4'>
                                <?php
                                #region Tabla dinámica
                                if ($resultado->num_rows > 0) {
                                    $primeraFila = $resultado->fetch_assoc();
                                    foreach ($primeraFila as $columna => $valor) {
                                        echo "<th>" . htmlspecialchars($columna) . "</th>";
                                    }
                                    echo "<th>Acciones</th>";
                                    echo "</tr></thead><tbody>";
                                    echo "<tr>";
                                    foreach ($primeraFila as $valor) {
                                        echo "<td>" . htmlspecialchars($valor) . "</td>";
                                    }
                                    echo "<td>
                            <form method='GET' action='editarReunion.php' style='display:inline;'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($primeraFila['id_reunion']) . "'>
                                <button type='submit'>
                                    <img src='../../img/square-pen.png' alt='Eliminar' style='width: 20px; height: 20px;' class='hover:bg-green-500 hover:scale-105'>
                                </button>
                            </form>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='eliminar_reunion' value='" . htmlspecialchars($primeraFila['id_reunion']) . "'>
                                <button type='submit'>
                                    <img src='../../img/trash-2.png' alt='Eliminar' style='width: 20px; height: 20px;' class='hover:bg-red-500 hover:scale-105'>
                                </button>
                            </form>
                          </td>";
                                    echo "</tr>";
                                    while ($fila = $resultado->fetch_assoc()) {
                                        echo "<tr>";
                                        foreach ($fila as $valor) {
                                            echo "<td>" . htmlspecialchars($valor) . "</td>";
                                        }
                                        echo "<td>
                                <form method='GET' action='editarReunion.php' style='display:inline;'>
                                    <input type='hidden' name='id' value='" . htmlspecialchars($fila['id_reunion']) . "'>
                                    <button type='submit'>
                                    <img src='../../img/square-pen.png' alt='Eliminar' style='width: 20px; height: 20px;' class='hover:bg-green-500 hover:scale-105'>
                                    </button>
                                </form>
                                <form method='POST' style='display:inline;'>
                                    <input type='hidden' name='eliminar_reunion' value='" . htmlspecialchars($fila['id_reunion']) . "'>
                                    <button type='submit'>
                                    <img src='../../img/trash-2.png' alt='Eliminar' style='width: 20px; height: 20px;' class='hover:bg-red-500 hover:scale-105'>
                                    </button>
                                </form>
                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<th>No hay reuniones registradas.</th>";
                                }
                                #endregion
                                ?>
                            </tr>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Botón de creación de reuniones -->
        <form method="GET" action="adminCrearReunion.php">
            <button type="submit" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[15em] p-3 shadow-lg">Crear nueva reunión</button>
        </form>
    
        <div class="flex justify-center items-center gap-10">
            <form action="../logout.php" method="POST" class="p-5 flex md:flex-row flex-col gap-10">
                <button type="button" onclick="window.location.href='administradores.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Volver al Panel de Administrador</button>
                <button type="button" onclick="window.location.href='adminUsuarios.php'" class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-[10em] p-3 shadow-lg">Volver al Panel de Usuarios</button>
            </form>
        </div>
    </div>



</body>

</html>