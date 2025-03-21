<?php
if (!defined('INIT_LOADED')) {
    define('INIT_LOADED', true);
    require_once __DIR__ . '/../core/init.php';
}
global $conexion;

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    echo ($stmt->num_rows > 0) ? "true" : "false";
    $stmt->close();
    exit();
}

?>
