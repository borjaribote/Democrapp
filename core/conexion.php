<?php

/* require_once $_SERVER['DOCUMENT_ROOT'] . "/proyectos/Democrapp/core/config.php"; 
 */
require_once __DIR__ . '/config.php';

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "DemocrApp";

$conexion = new mysqli($servername, $username, $password, $database);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
