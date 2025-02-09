<?php 
require_once '../conexion.php';

unset ($_SESSION['user_id']);
unset ($_SESSION['user_name']);
unset ($_SESSION['user_email']);
session_destroy();
setcookie("PHPSESSID", "", time() - 3600, "/");
header("Location: " . BASE_URL . "index.php");
?>
