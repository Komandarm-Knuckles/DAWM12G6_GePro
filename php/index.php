<?php
session_start();
require_once 'database.php';

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
<body>
    <img class="w-full h-screen relative" src="../img/textura1.jpg" alt="">
    <!-- contenedor Principal -->
    <div class="w-[100em] flex z-10 absolute left-50 bottom-50 shadow-2xl">
        <!-- Div izquierdo -->
            <section class="flex flex-col  w-200 justify-center items-center gap-15 bg-orange-100">
                <div class="flex font-bold text-3xl">
                    <!-- imagen logo en pequeño al lado del nombre -->
                    <img src="" alt="">
                    <p> Logo + GePro </p>
                </div>
                <div class="font-bold text-4xl">
                    <p>Bienvenido/a</p>
                </div>
                <div class="flex flex-col w-100 items-center gap-10 p-10 ">
                <p class="font-bold text-3xl">Inicia de sesión</p>
                <form method="POST" class="flex flex-col w-full  ">
                    <!-- INPUT USUARIO -->
                    <p class="font-bold">USUARIO</p>
                    <input type="text" name="user" placeholder="Nombre" required
                        class="bg-green-500 shadow-xl rounded-md p-1 outline-none "/><br>

                    <!-- INPUT CONTRASEÑA -->
                    <p class="font-bold">CONTRASEÑA</p>
                    <input type="password" name="pass" placeholder="Contraseña" required
                        class="bg-green-500 shadow-xl rounded-md p-1 outline-none"/><br>
                    <input type="submit" value="INICIAR SESION"
                        class="bg-blue-500 p-2 hover:scale-115 duration-500 rounded-md"/>
                </form>
                </div>
            </section>
        <!-- Div derecho Logo empresa GePro -->
        <div class="flex">
            <img src="../img/textura1.jpg" alt="Iamgen Empresa">
        </div>
    </div>

</body>
</html>