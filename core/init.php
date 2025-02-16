<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar configuración y conexión a la base de datos
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/conexion.php';
    


function accesoAutorizado($tipo='usuario') {
    $is_logged = isset($_SESSION['user_id']);
    $is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true;

    if ($tipo === 'usuario' && !$is_logged) {
        // Si la página requiere usuario registrado y no está logueado
        header("Location: ".BASE_URL."index.php");
        exit();
    }

    if ($tipo === 'admin' && !$is_admin) {
        // Si la página requiere admin y el usuario no es admin
        header("Location: ../pages/404.php");
        exit();
    }
}

?>
