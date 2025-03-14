<!-- Aquí es donde hay que meter luego lo de administradores.php -->

<?php
// Control de inicio de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 0) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: index.php");
    exit();
}

require_once("database.php");
$con = crearConexion();

// ELIMINAR USUARIOS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_usuario'])) 
    {
        $usuario = $_POST['eliminar_usuario'];
        borrar_usuario($con, $usuario);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Administrador</title>
     <!-- <link rel="stylesheet" type="text/css" href="../css/admin-styles.css"> -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<?php
// (Asumiendo que tienes las funciones obtener_todos_los_usuarios() y obtener_resultados() definidas)

echo "<body class='w-full  bg-cover bg-center bg-fixed z-10 bg-[url(\"../img/pixels14.jpg\")]'>";

// div principal
echo "<div class='flex w-full min-h-screen justify-center items-center'>";

// div Contenedor con ancho máximo y centrado
echo "<div class='flex w-full md:flex-row flex-col justify-center items-stretch max-w-[90%]'>"; // items-stretch extiende el div izquiedo en el eje Y

// Div Izquierdo con altura total del contenedor
echo "<section class='flex md:flex-col md:w-80 w-full flex-wrap md:justify-start  justify-center items-center bg-orange-400 md:gap-10 gap-5 pt-5'>";
echo "<img class='md:w-50 w-15' src='../img/LogoEmpresa.png' alt='logo Empresa'/>
      <div class='flex gap-2'>
      <img src='../img/projector.svg' alt='imagenReuniones'/>
      <a href='adminReuniones.php' class='font-bold text-white text-lg'>Reuniones</a>
      </div>
      <div class='flex gap-2'>
      <img src='../img/folder-git-2.svg' alt='imagenProyectos'/>
      <a href='adminProyectos.php' class='font-bold text-white text-lg'>Proyectos</a>
      </div>
      <div class='flex gap-2'>
      <img src='../img/clipboard-list.svg' alt='imagenProyectos'/>
      <a href='adminTareas.php' class='font-bold text-white text-lg'>Tareas</a>
      </div>
      <div class='flex gap-2'>
      <img src='../img/users.svg' alt='imagenProyectos'/>
      <a href='adminGrupos.php' class='font-bold text-white text-lg'>Grupos</a>
      </div>";
// final div izquierdo
echo "</section>";

// Div derecho 
echo "<div class='flex flex-col py-10 max-h-screen gap-6 justify-center items-center bg-gray-300 w-full'>";

echo "<h1 class='text-center text-4xl font-bold underline'>Bienvenido a la Página de Administrador</h1>";

// ---------------------------GESTIÓN DE USUARIOS-----------------------------

echo "<h2 class='font-bold text-2xl underline'>- Gestión de Usuarios -</h2>";
$usuarios = obtener_todos_los_usuarios($con);

// MOSTRAR TABLA USUARIOS
echo "<h4 class='text-xl font-bold underline'>Usuarios registrados:</h4>";
if ($usuarios->num_rows === 0) {
    echo "<p>No se encuentran usuarios</p>";
} else {
    echo "<div class='max-h-[300px] overflow-y-auto shadow-2xl w-full'>"; // Aseguramos que el div tenga ancho total
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
                                <img src='../img/square-pen.png' alt='Eliminar' style='width: 20px; height: 20px;'  class='hover:bg-green-500 hover:scale-105'/> 
                            </button>
                        </form>
                        <form method='POST' action=''>
                            <input type='hidden' name='eliminar_usuario' value='$usuario'>
                            <button type='submit' onclick=\"return confirm('¿Estás seguro de que quieres eliminar este usuario?');\" class='cursor-pointer'>
                                <img src='../img/trash-2.png' alt='Eliminar' style='width: 20px; height: 20px;' class='hover:bg-red-500 hover:scale-105'> 
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
                            <img src='../img/square-pen.png' alt='Eliminar' style='width: 20px; height: 20px;'  class='hover:bg-green-500 hover:scale-105'/> 
                        </button>
                    </form>
                </td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>"; // Cierra el div que envuelve la tabla
}

?>
<div class="flex p-4 gap-2 justify-center items-center shadow-2xl">
    <h4 class="font-semibold">Dar de alta nuevos usuarios:</h4>
    <form method='POST' action='adminCrearUsuario.php' class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl shadow-lg cursor-pointer font-bold text-white">
        <button class="cursor-pointer" type='submit'> Añadir Usuario</button>
    </form>
</div>
<div class="flex gap-10">
<form action="logout.php" method="POST">
    <button type="submit" class="p-3 bg-orange-400 hover:bg-orange-700 rounded-xl shadow-lg cursor-pointer font-bold text-white w-50">Cerrar Sesión</button>
</form>
<button type="button" onclick="history.back()" class="bg-orange-400 hover:bg-orange-700 text-white font-bold p-3 rounded-xl w-50">Volver</button>
</div>
<?php

// Final divs
echo "</div></div></div>";
echo "</body>";
?>