<?php
session_start();

#region Comprobación de sesión existente
if (isset($_SESSION['usuario'])) 
{
    switch ($_SESSION['tipo']) 
    {
        case 0:
            header("Location: administradores.php");
            exit();
        case 1:
            header("Location: jefes-equipo.php");
            exit();
        case 2:
            header("Location: empleados.php");
            exit();
    }
}
#endregion

require_once 'database.php';

// mensaje de error sesión
if (isset($_SESSION['error'])) {
    echo "<p style='color: red; font-weight: bold; text-align:center;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']); // Borra el mensaje después de mostrarlo
}

$con = crearConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user'], $_POST['pass']))
{
    if(empty($_POST['user']) ||empty($_POST['pass']))
    {
        echo'<script type="text/javascript">alert("Debes rellenar ambos campos");</script>';
    } 
    else
    {
        #region Query
        $query = "SELECT usuario, pass, tipo FROM USUARIOS WHERE usuario = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "s", $_POST['user']);
        mysqli_stmt_execute($stmt);
        $return = mysqli_stmt_get_result($stmt);
        $result = mysqli_fetch_assoc($return);
        #endregion

        #region Verificacion
        if ($result && isset($result['pass']) && password_verify($_POST['pass'], $result['pass'])) 

        {
            $_SESSION['usuario'] = $result['usuario'];
            $_SESSION['tipo'] = $result['tipo'];

            #region Redirección
            switch ($result['tipo'])
            {
                case '0':
                    header('Location: administradores.php');
                    exit();
                
                case '1':
                    header('Location: jefes-equipo.php');
                    exit();
                
                case '2':
                    header('Location: empleados.php');
                    exit();
            }
            #endregion
        }
        else
        {
            echo'<script type="text/javascript">alert("Usuario o contraseña incorrectos");window.location.href="index.php";</script>';
        } 
        #endregion
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GePro</title>
    <!-- <link rel="stylesheet" href="../css/styles.css"/> -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="h-screen w-full justify-center bg-cover bg-center bg-fixed" style="background-image: url('../img/engranajesFondo.jpeg');">
    <!-- Contenedor Principal -->
    <div class="flex  justify-center items-center gap-5 h-screen w-full">
        <!-- div Contenedor login -->
        <section class="flex flex-col w-[30em] justify-center items-center gap-10 bg-gray-300 rounded-xl shadow-2xl p-10 ">
            <div class="flex flex-col items-center font-bold text-3xl gap-2">
                <img class="w-10" src="../img/LogoEmpresa.png" alt="logo Empresa">
                <p class="font-bold text-4xl">Bienvenido/a</p>
            </div>
            <span class="block h-0.5 w-80 bg-black opacity-40"></span>
            <!-- Div Formulario -->
            <div class="flex flex-col w-100 items-center gap-5 px-10">
                <p class="font-bold text-3xl">Inicia Sesión</p>
                <form method="POST" class="flex flex-col w-full">
                    <p class="font-bold">USUARIO</p>
                    <input type="text" name="user" placeholder="Nombre" required
                        class="bg-gray-700 shadow-xl text-white caret-white rounded-md p-2 outline-none"/><br>
                    <p class="font-bold">CONTRASEÑA</p>
                    <input type="password" name="pass" placeholder="Contraseña" required
                        class="bg-gray-700 shadow-xl text-white caret-white rounded-md p-2 outline-none"/><br>
                    <input type="submit" value="INICIAR SESIÓN"
                        class="bg-orange-400 p-2 hover:scale-110 transition-transform duration-300 rounded-md cursor-pointer"/>
                </form>
            </div>
            
        </section>
            <!-- div derecho -->
            <div class="flex hidden md:block">
            <img src="../img/LogoEmpresa.png" alt="Iamgen Empresa">
            </div>
    </div>
</body>
 
</html>