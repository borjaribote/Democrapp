<?php
session_start(); // Necesario para mantener la sesión en todas las páginas
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <body data-baseurl="<?= BASE_URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>assets/css/global.css">
    <!-- Bootstrap 5.3 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">    <link rel="stylesheet" href="global.css">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Sistema de Votación</a>
            
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="proponer_tema.php">Nueva Propuesta</a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <div class="d-flex">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <span class="navbar-text me-3">Hola, <?= $_SESSION['user_name'] ?></span>
                        <a href="acciones/logout.php" class="btn btn-danger">Cerrar sesión</a>
                    <?php else: ?>
                        <a href="index.php" class="btn btn-primary me-2">Login</a>
                        <a href="registro.php" class="btn btn-success">Registrarse</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
