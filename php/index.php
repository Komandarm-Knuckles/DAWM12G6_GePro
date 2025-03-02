<?php
session_start();
require_once 'database.php';

$con = crearConexion();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user'], $_POST['pass']))
{
    $user = $_POST['user'];
    $pass = hash('sha256', $_POST['pass']);
    
    #region Query
    # PENDIENTE
    #endregion

    #region Verificacion
    if($result !== null && $user === $result['usuario'] && $pass === $user['pass'])
    {
        $_SESSION['usuario'] = $result['usuario'];
        $_SESSION['tipo'] = $result['tipo'];

        #region Redirección
        switch ($user['tipo'])
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

            default:
            echo "<script type='text/javascript'>alert('Bienvenido');</script>";
        }
        #endregion
    }
    else
    {
        echo "<script type='text/javascript'>alert('Usuario o contraseña incorrectos');</script>";
    } 
    #endregion
} 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto GePro</title>
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
                <form>
                    <p>Inicio de sesión</p>
                    <input type="text" name="user" placeholder="Nombre"><br>
                    <input type="password" name="pass" placeholder="Contraseña"><br>
                    <input type="submit" value="INICIAR SESION">
                </form>
            </div>
        </div>
    </main>
</body>
</html>