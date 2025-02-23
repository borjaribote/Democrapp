<?php
if (!defined('INIT_LOADED')) {
    define('INIT_LOADED', true);
    require_once __DIR__ . '/../core/init.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <body data-baseurl="<?= BASE_URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Democrapp</title>
    
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>assets/css/global.css">
    <link rel="icon" href="/assets/img/favicon_thumbs_up.ico" type="image/x-icon">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand greek" href="<?= BASE_URL ?>index.php">DemocrApp</a>
            
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>index.php">Inicio</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>pages/temas/mis_temas.php">Mis temas</a>
                        </li>
                        
                    <?php endif; ?>
                    <?php if(isset($_SESSION['is_admin'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Admin
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>pages/temas/vista.php">Administrar temas</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>pages/rondas/vista.php">Administrar rondas</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>pages/usuarios/administrar.php">Administrar usuarios</a></li>
                       </ul>
                    </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="d-flex align-items-end">
                            <a class="navbar-text me-3" href="<?= BASE_URL ?>pages/usuarios/micuenta.php"><span class="navbar-text"><?= $_SESSION['user_name'] ?></span>
                            <i class="fa-solid fa-user"></i></a>
                        </div>
                        <a href="<?= BASE_URL ?>functions/user_auth.php" class="btn btn-danger">Cerrar sesión</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>index.php" class="btn btn-primary me-2">Login</a>
                        <a href="<?= BASE_URL ?>pages/usuarios/registro.php" class="btn btn-success">Registrarse</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
