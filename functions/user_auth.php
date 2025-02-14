<?php
require_once '../core/init.php';
//Si se ha enviado el formulario de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conexion->prepare("SELECT id, username, email, password_hash, is_admin FROM users WHERE email = ?");
    $password = $_POST['password'];

    try {
        $stmt->bind_param("s", $_POST['email']);
        $stmt->execute();
        $stmt->bind_result($id, $username, $email, $password_hash, $is_admin);
        $stmt->fetch();

        if (password_verify($password, $password_hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $username;
            $_SESSION['user_email'] = $email;
            if ($is_admin) {
                $_SESSION['is_admin'] = true;
            }
        } else {
            $_SESSION['error_message'] = "Contraseña incorrecta.";
        }
    } catch (Exception $e) {
        echo '<script>alert("Error en la consulta: ' . $e->getMessage() . '")</script>';
    }
    header("Location: " . BASE_URL . "index.php");
    exit;
}else{
    //Si no se ha llamado entonces la llamada al archivo es para cerrar session
    unset ($_SESSION['user_id']);
    unset ($_SESSION['user_name']);
    unset ($_SESSION['user_email']);
    session_destroy();
    setcookie("PHPSESSID", "", time() - 3600, "/");
    header("Location: " . BASE_URL . "index.php");
}



?>
