<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar configuración y conexión a la base de datos
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/conexion.php';
?>
