<?php
if (!defined('INIT_LOADED')) {
    define('INIT_LOADED', true);
    require_once __DIR__ . '/../core/init.php';
}

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
            eliminarUsuario($_POST);
            break;
        case 'selecAllUsers':
            consultarUsuarios();
            break;
        default:
            echo "Acción no válida.";
    }
}
/*Página: registro  */
function insertarUsuario($data) {
    global $conexion;

    $username = $data['username'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT); 

    $sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password); 

    if ($stmt->execute()) {
         header("Location: " . BASE_URL . "index.php?mensaje=usuario_registrado&value=$email");
         exit();
    } else {
        echo "Error al registrar usuario: " . $conexion->error;
    }
}

/*Página: Mi cuenta*/
function actualizarUsuario($data) {
    global $conexion;

    $id = $data['id'];
    if (isset($data['password'])) {
        $password = password_hash($data['password'], PASSWORD_DEFAULT); 
        $sql = "UPDATE users SET username = ?, email = ?, password_hash = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssi", $data['username'], $data['email'],$password, $id);
    }else if (isset($data['is_admin'])){
        $sql = "UPDATE users SET is_admin = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ii", $data['is_admin'], $id);
        $location = "/pages/usuarios/administrar.php?mensaje=admin_actualizado";
    }else{
        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssi", $data['username'], $data['email'], $id);  
        $_SESSION['user_name'] = $data['username'];
        $_SESSION['user_email'] = $data['email'];     
        $location = "/pages/usuarios/micuenta.php?mensaje=usuario_actualizado";
    }
    if ($stmt->execute()) {
        header("Location: " . BASE_URL . $location);
        exit();  
    } else {
        echo "Error al registrar usuario: " . $conexion->error;
    }
}

/*Página: usuarios/administrar */
function eliminarUsuario($data) {
    global $conexion;
    $email = $data['email'];
    $page = $data['page'];
    $id = $data['id'];
    $session=null;
    if ($page=="usuarios/administrar"){
        $location = "/pages/usuarios/administrar.php?mensaje=cuenta_eliminada&value=$email";
    }else if($page == "cuenta"){
        $location = "index.php?mensaje=cuenta_eliminada&value=$email";
    }
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($page=="usuarios/administrar"){
            $location = "/pages/usuarios/administrar.php?mensaje=cuenta_eliminada&value=$email";
        }else if($page == "cuenta"){
            session_destroy(); 
            $_SESSION = [];
            $location = "index.php?mensaje=cuenta_eliminada&value=$email";
        }
        header("Location: " . BASE_URL . $location);
        exit();
    } else {
        echo "Error al eliminar usuario.";
    }
}

/*Página: Mi cuenta*/
function consultarUsuario($id) {
    global $conexion;
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

/*Página: usuarios/administrar */
function consultarUsuarios($search = '') {
    global $conexion;
    
    $sql = "SELECT * FROM users";
    if (!empty($search)) {
        $sql .= " WHERE username LIKE ? OR email LIKE ?";
    }
    
    $stmt = $conexion->prepare($sql);
    if (!empty($search)) {
        $search_param = "%$search%";
        $stmt->bind_param("ss", $search_param, $search_param);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $usuarios = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
    }
    return $usuarios;
}
?>
