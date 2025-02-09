<?php
require_once '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conexion->prepare("SELECT id, username, email, password_hash FROM users WHERE email = ?");
    $password = $_POST['password'];

    try {
        $stmt->bind_param("s", $_POST['email']);
        $stmt->execute();
        $stmt->bind_result($id, $username, $email, $password_hash);
        $stmt->fetch();

        if (password_verify($password, $password_hash)) {
            session_start();
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $username;
            $_SESSION['user_email'] = $email;
        } else {
            session_start();
            $_SESSION['error_message'] = "Contraseña incorrecta.";
        }
    } catch (Exception $e) {
        echo '<script>alert("Error en la consulta: ' . $e->getMessage() . '")</script>';
    }
    header("Location: " . BASE_URL . "index.php");
    exit;
}
?>
