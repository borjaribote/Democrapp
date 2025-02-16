<?php
$mensaje = isset($_GET['mensaje']) ? explode("&", $_GET['mensaje'])[0] : ''; 
$value = isset($_GET['value']) ? htmlspecialchars($_GET['value'], ENT_QUOTES, 'UTF-8') : '';

switch ($mensaje) { 
    case 'usuario_registrado':
        echo '<div class="alert alert-success text-center">El correo <strong>'.$value.'</strong> se ha registrado exitosamente.</div>';
        break;
    case 'cuenta_eliminada':
        echo '<div class="alert alert-success text-center">La cuenta con correo <strong>'.$value.'</strong> ha sido eliminada correctamente.</div>';   
        break;
    case 'tema_registrado':
        echo '<div class="alert alert-success text-center" role="alert"> Su tema <strong>' . $value . '</strong> se ha registrado correctamente.</div>';
        break;
    case 'usuario_actualizado':
        echo '<div class="alert alert-success text-center">Sus datos se han actualizado correctamente</div>';
        break;
    case 'admin_actualizado':
        echo '<div class="alert alert-success text-center">Permisos de usuario actualizados correctamente</div>';
        break;
    case 'error_login':
        echo '<div class="alert alert-danger text-center">Error en el inicio de sesión. Por favor, intenta de nuevo.</div>';
        break;
    default:
        break;
}
?>

            
