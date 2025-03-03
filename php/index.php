<?php
session_start();
require_once 'database.php';

$con = crearConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user'], $_POST['pass']))
{
    if(empty($_POST['user']) ||empty($_POST['pass']))
    {
        echo'<script type="text/javascript">alert("Debes rellenar ambos campos");window.location.href="index.php";</script>';
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
        if($result !== null && password_verify($_POST['pass'], $result['pass']))
        {
            $_SESSION['usuario'] = $result['usuario'];
            $_SESSION['tipo'] = $result['tipo'];

            #region Redirección
            switch ($result['tipo'])
            {
                case 'Admin':
                    header('Location: administradores.php');
                    exit();
                
                case 'Jefe':
                    header('Location: jefes.php');
                    exit();
                
                case 'Empleado':
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
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <ul class="nav-list">
                <li><a href="iniciar-sesion.html">Iniciar Sesión</a></li>
            </ul>
            <div class="rightNav">
            </div>
        </nav>    
    </header>
    <main>
        <div class="login-container">
            <div class="login-box">
                <form method="POST">
                    <p>Inicio de sesión</p>
                    <input type="text" name="user" placeholder="Nombre" required><br>
                    <input type="password" name="pass" placeholder="Contraseña" required><br>
                    <input type="submit" value="INICIAR SESION">
                </form>
            </div>
        </div>
    </main>
</body>
</html>