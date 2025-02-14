<?php
require_once '../core/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    switch ($action) {
        case 'insert':
            insertarUsuario($_POST);
            break;
        case 'update':
            actualizarUsuario($_POST);
            break;
        case 'delete':
            eliminarUsuario($_POST['id']);
            break;
        default:
            echo "Acción no válida.";
    }
}

function insertarUsuario($data) {
    global $conexion;

    $username = $data['username'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT); 

    $sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password); 

    if ($stmt->execute()) {
         header("Location: " . BASE_URL . "index.php");
         exit();
    } else {
        echo "Error al registrar usuario: " . $conexion->error;
    }
}

function actualizarUsuario($data) {
    global $conexion;

    $id = $data['id'];
    $nombre = $data['nombre'];
    $email = $data['email'];

    $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssi", $nombre, $email, $id);

    if ($stmt->execute()) {
        echo "Usuario actualizado correctamente.";
    } else {
        echo "Error al actualizar usuario.";
    }
}

function eliminarUsuario($id) {
    global $conexion;

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Usuario eliminado correctamente.";
    } else {
        echo "Error al eliminar usuario.";
    }
}
?>
