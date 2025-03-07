<?php
// control de inicio de sesión
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 2) {
    $_SESSION['error'] = "Debes iniciar sesión antes de acceder.";
    header("Location: index.php");
    exit();
}
require_once("database.php");

$con = crearConexion();
?>

<head>
    <title>Página de Empleado</title>
    <link rel="stylesheet" href="../css/empleado-styles.css">
</head>

<!--LogOut-->
<form action="logout.php" method="POST">
    <button type="submit" class="logout-button">Cerrar Sesión</button>
</form>