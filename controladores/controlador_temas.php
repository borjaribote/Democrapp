<?php
require_once '../core/init.php';
require_once  BASE_PATH.'/functions/cohere_api.php'; //por mover de carpeta


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    echo $action;
    switch ($action) {
        case 'insert':
            insertarTema($_POST);
            break;
        case 'update':
            actualizarTema($_POST['topic_id']);
            break;
        case 'delete':
            eliminarTema($_POST['topic_id']);
            break;
        default:
            echo "Acción no válida.";
    }
} else {
}

function insertarTema($data) {
    global $conexion;
    $topic = $data['topic'];
    $title = generarTituloGlobalCohere($topic); 
    echo $title;
    $sql = "INSERT INTO topics (user_id, title, topic) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iss", $_SESSION['user_id'], $title, $topic);
        


    if ($stmt->execute()) {
        $_SESSION['new_topic'] = $topic;
        header("Location: " . BASE_URL . "pages/temas.php");
        exit();  
    } else {
        echo "Error al registrar tema: " . $conexion->error;
    }
}

function actualizarTema($id) {
    global $conexion;

    $sql = "UPDATE topics SET is_approved = true WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "pages/administrar_temas.php");
        exit();  
    } else {
        echo "Error al eliminar tema.";
    }
}

function eliminarTema($id) {
    global $conexion;

    $sql = "DELETE FROM topics WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "pages/administrar_temas.php");
        exit();  
    } else {
        echo "Error al eliminar tema.";
    }
}

function consultarDatosTemas($id) {
    global $conexion;

    $sql = "SELECT 
    t.topic, 
    t.is_approved, 
    CASE 
        WHEN t.is_approved = 1 THEN 'Aprobado'
        WHEN t.is_approved = 0 THEN 'Pendiente'
        ELSE 'Desconocido'
    END AS estado_tema, 
    r.stage, 
    COALESCE(v.total_votes, 0) AS total_votes
FROM topics t
LEFT JOIN topic_rounds tr ON t.id = tr.topic_id
LEFT JOIN rounds r ON tr.round_id = r.id
LEFT JOIN (
    SELECT topic_id, COUNT(*) AS total_votes 
    FROM votes 
    GROUP BY topic_id
) v ON t.id = v.topic_id
WHERE t.user_id = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $temas = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $temas[] = $row;
        }
    }
    
    return $temas;
}

function consultarTemasAprobados() {
    global $conexion;

    $sql = "SELECT id, title, topic, created_at FROM topics WHERE is_approved = TRUE";
    $result = $conexion->query($sql);
    $temas = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $temas[] = $row;
        }
    }
    
    return $temas;
}

function consultarTemasPendientes() {
    global $conexion;
    $order_by = "created_at DESC"; 

    if (isset($_GET['order'])) {
        $allowed_columns = ["title", "topic", "created_at"];
        $order = in_array($_GET['order'], $allowed_columns) ? $_GET['order'] : "created_at";
        $order_by = "$order ASC";
    }
    
    $sql = "SELECT id, title, topic, created_at FROM topics WHERE is_approved = FALSE ORDER BY $order_by";
    $result = $conexion->query($sql);
    
    $temas = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $temas[] = $row;
        }
    }
    
    return $temas;
}
?>