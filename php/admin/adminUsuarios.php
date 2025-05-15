<!-- Aquí es donde hay que meter luego lo de administradores.php -->

<?php
// Control de inicio de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: index.php");
    exit();
}

require_once("../database.php");
$con = crearConexion();

// ELIMINAR USUARIOS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_usuario'])) {
    $usuario = $_POST['eliminar_usuario'];
    borrar_usuario($con, $usuario);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Usuarios</title>
    <!-- <link rel="stylesheet" type="text/css" href="../css/admin-styles.css"> -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<?php
// (Asumiendo que tienes las funciones obtener_todos_los_usuarios() y obtener_resultados() definidas)

echo "<body class='w-full  bg-cover bg-center bg-fixed z-10 bg-[url(\"../../img/pixels14.jpg\")]'>";

// div principal
echo "<div class='flex w-full min-h-screen justify-center items-center'>";

// div Contenedor con ancho máximo y centrado
echo "<div class='flex w-full md:flex-row flex-col justify-center items-stretch max-w-[90%]'>"; // items-stretch extiende el div izquiedo en el eje Y

// Div Izquierdo con altura total del contenedor
echo "<section class='flex md:flex-col md:w-80 w-full flex-wrap md:justify-start justify-center items-center bg-orange-400 md:gap-10 gap-5 pt-5'>";
echo "<img class='md:w-13 w-[10em]' src='../../img/LogoEmpresa.png' alt='logo Empresa'/>
        <div class='flex gap-2'>
        <img src='../../img/folder-git-2.svg' alt='imagenProyectos'/>
        <a href='adminProyectos.php' class='font-bold text-white text-lg'>Proyectos</a>
        </div>
      <div class='flex gap-2'>
      <img src='../../img/projector.svg' alt='imagenReuniones'/>
      <a href='adminReuniones.php' class='font-bold text-white text-lg'>Reuniones</a>
      </div>
      <div class='flex gap-2'>
      <img src='../../img/clipboard-list.svg' alt='imagentareas'/>
      <a href='adminTareas.php' class='font-bold text-white text-lg'>Tareas</a>
      </div>";
// final div izquierdo
echo "</section>";

// Div derecho 
echo "<div class='flex flex-col min-h-screen gap-6 justify-center items-center bg-gray-300 w-full'>";

// ---------------------------GESTIÓN DE USUARIOS-----------------------------

echo "<h1 class='font-bold text-4xl text-orange-400'>GESTIÓN DE USUARIOS</h1>";
echo "<h3 class='text-xl font-bold text-orange-400'>Usuarios registrados:</h3>";
$usuarios = obtener_todos_los_usuarios($con);

// MOSTRAR TABLA USUARIOS
if ($usuarios->num_rows === 0) {
    echo "<p>No se encuentran usuarios</p>";
} else {
    echo "<div class='max-h-[800px] overflow-y-auto shadow-2xl w-full'>"; // Aseguramos que el div tenga ancho total
    echo "<table class='styled-table w-full'>"; // Aseguramos que la tabla tenga ancho total
    echo "<thead>
            <tr class='sticky bg-orange-400 text-white top-0 bg-gray-300'>
                <th class='p-3'>USUARIO</th>
                <th class='p-3'>NOMBRE</th>
                <th class='p-3'>APELLIDO</th>
                <th class='p-3'>DNI</th>
                <th class='p-3'>EMAIL</th>
                <th class='p-3'>TELEFONO</th>
                <th class='p-3'>TIPO</th>
                <th class='p-3'>ACCIONES</th>
            </tr>
        </thead>
        <tbody>";

    while ($fila = obtener_resultados($usuarios)) {
        extract($fila);
        echo "<tr>
                <td class='text-center p-4 font-semibold'>$usuario</td>
                <td class='text-center p-4 font-semibold'>$nombre</td>
                <td class='text-center p-4 font-semibold'>$apellido</td>
                <td class='text-center p-4 font-semibold'>$dni</td>
                <td class='text-center p-4 font-semibold'>$email</td>
                <td class='text-center p-4 font-semibold'>$telefono</td>
                <td class='text-center p-4 font-semibold'>$tipo</td>";
        if ($_SESSION['usuario'] !== $usuario) {
            echo "
                <td>
                    <div class='flex justify-center items-center'>
                        <form method='POST' action='adminEditarUsuario.php'>
                            <input type='hidden' name='editar_usuario' value='$usuario'>
                            <button type='submit' value='Editar' class='cursor-pointer'>
                                <img src='../../img/square-pen.png' alt='Eliminar' style='width: 20px; height: 20px;'  class='hover:bg-green-500 hover:scale-105'/> 
                            </button>
                        </form>
                        <form method='POST' action=''>
                            <input type='hidden' name='eliminar_usuario' value='$usuario'>
                            <button type='submit' onclick=\"return confirm('¿Estás seguro de que quieres eliminar este usuario?');\" class='cursor-pointer'>
                                <img src='../../img/trash-2.png' alt='Eliminar' style='width: 20px; height: 20px;' class='hover:bg-red-500 hover:scale-105'> 
                            </button>
                        </form>
                    </div>
                </td>";
        } else {
            echo "
                <td>
                    <form class='flex justify-center items-center' method='POST' action='adminEditarUsuario.php'>
                        <input type='hidden' name='editar_usuario' value='$usuario'>
                        <button type='submit' value='Editar' class='cursor-pointer'>
                            <img src='../../img/square-pen.png' alt='Eliminar' style='width: 20px; height: 20px;'  class='hover:bg-green-500 hover:scale-105'/> 
                        </button>
                    </form>
                </td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>"; // Cierra el div que envuelve la tabla
}

?>
<div class="flex gap-4 justify-center items-center shadow-2xl px-5 ">
    <h4 class="font-semibold">Dar de alta nuevos usuarios:</h4>
    <form method="POST" action="adminCrearUsuario.php" class="flex justify-center items-center mt-4">
        <button type="submit"
            class="bg-orange-400 hover:bg-orange-700 rounded-xl shadow-lg font-bold text-white px-4 py-2">
            Añadir Usuario
        </button>
    </form>
</div>


<!-- Botones de volver a panel administrador o volver -->
<div class="flex justify-center items-center gap-10">
    <form action="../logout.php" method="POST" class="p-5 flex md:flex-row flex-col gap-10">
        <button type="button" onclick="window.location.href='administradores.php'"
            class="bg-orange-400 hover:bg-orange-700 text-white font-bold rounded-xl w-fit p-3 shadow-lg">Panel de
            Administrador</button>
    </form>
</div>
<?php

// Final divs
echo "</div></div></div>";
echo "</body>";
?>