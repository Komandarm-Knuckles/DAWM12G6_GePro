<?php
session_start();
require_once 'database.php';

$con = crearConexion();
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
                    <input type="text" name="iniciar-sesion" placeholder="Nombre"><br>
                    <input type="password" name="pass" placeholder="Contraseña"><br>
                    <input type="submit" value="INICIAR SESION">
                </form>
            </div>
        </div>
    </main>
</body>
</html>