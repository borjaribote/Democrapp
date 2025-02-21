<?php
if (!defined('INIT_LOADED')) {
    define('INIT_LOADED', true);
    require_once __DIR__ . '/../core/init.php';
}
require_once  __DIR__.'/../functions/cohere_api.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
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
}

/*Página: Index Fase:Propuestas */
function insertarTema($data) {
    global $conexion;
 try{
    $topic = $data['topic'];
    $description = $data['description'];

    $title = generarTituloGlobalCohere($topic);
    $sql = "INSERT INTO topics (user_id, title, topic, description) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("isss", $_SESSION['user_id'], $title, $topic, $description);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "index.php?mensaje=tema_registrado&value=$topic");
        exit();  
    }
    } catch (mysqli_sql_exception $e) {
        if ($conexion->errno == 1062) { 
            echo "enrtraaa";
            header("Location: " . BASE_URL . "index.php?mensaje=tema_duplicado&value=$topic");
            exit();
        } else {
            echo "Error al registrar tema: " . $conexion->error;
        }
    }
}

/* No se usa */
function actualizarTema($id) {
    global $conexion;

    $sql = "UPDATE topics SET is_approved = true WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "pages/temas/vista.php");
        exit();  
    } else {
        echo "Error al eliminar tema.";
    }
}

/*Página: Temas Ventana:Aprobar temas */
function eliminarTema($id) {
    global $conexion;

    $sql = "DELETE FROM topics WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "pages/temas/vista.php");
        exit();  
    } else {
        echo "Error al eliminar tema.";
    }
}

/*Página: Mis temas */
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

/*Página: Temas Ventana:Aprobar temas */
function consultarTemasPendientes() {
    global $conexion;
    
    $order_by = "created_at DESC"; 
    $allowed_columns = ["title", "topic", "created_at"];
    $allowed_directions = ["ASC", "DESC"];
    $order = isset($_GET['order']) && in_array($_GET['order'], $allowed_columns) ? $_GET['order'] : "created_at";
    $direction = isset($_GET['direction']) && in_array($_GET['direction'], $allowed_directions) ? $_GET['direction'] : "DESC";
    $new_direction = ($direction === "ASC") ? "DESC" : "ASC";
    $order_by = "$order $direction";

    $sql = "SELECT * FROM topics WHERE is_approved = FALSE ORDER BY $order_by";
    $result = $conexion->query($sql);
    
    $temas = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $temas[] = $row;
        }
    }

    return [$temas, $new_direction]; 
}

/*Página: Temas Ventana:Temas aprobados */
function consultarTemasAprobadosOrdenado() {
    global $conexion;
    
    $order_by = "created_at DESC"; 
    $allowed_columns = ["title", "topic", "created_at"];
    $allowed_directions = ["ASC", "DESC"];
    $order = isset($_GET['order']) && in_array($_GET['order'], $allowed_columns) ? $_GET['order'] : "created_at";
    $direction = isset($_GET['direction']) && in_array($_GET['direction'], $allowed_directions) ? $_GET['direction'] : "DESC";
    $new_direction = ($direction === "ASC") ? "DESC" : "ASC";
    $order_by = "$order $direction";

    $sql = "SELECT * FROM topics WHERE is_approved = TRUE  ORDER BY $order_by";
    $result = $conexion->query($sql);
    
    $temas = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $temas[] = $row;
        }
    }

    return [$temas, $new_direction]; 
}

/*Página: Rondas Ventana:Crear ronda Fase: Clasificación */
function consultarTemasAprobados() {
    global $conexion;
    
    $order_by = "created_at DESC"; 
    $sql = "SELECT * FROM topics WHERE is_approved = TRUE  ORDER BY $order_by";
    $result = $conexion->query($sql);
    
    $temas = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $temas[] = $row;
        }
    }

    return $temas; 
}
/*Página: Index Fase: Clasificación */
function obtenerTemasRondaActiva() {
    global $conexion;

    // Obtener la ronda activa
    $sql_ronda = "SELECT id FROM rounds WHERE status = 'active' LIMIT 1";
    $stmt_ronda = $conexion->prepare($sql_ronda);
    $stmt_ronda->execute();
    $result_ronda = $stmt_ronda->get_result();
    $ronda = $result_ronda->fetch_assoc();

    if (!$ronda) {
        return []; // No hay ronda activa
    }

    // Obtener los temas asociados a la ronda activa
    $sql_temas = "SELECT *
                  FROM topics t
                  JOIN topic_rounds tr ON t.id = tr.topic_id
                  WHERE tr.round_id = ?";
    
    $stmt_temas = $conexion->prepare($sql_temas);
    $stmt_temas->bind_param("i", $ronda['id']);
    $stmt_temas->execute();
    $result_temas = $stmt_temas->get_result();

    $temas = [];
    while ($row = $result_temas->fetch_assoc()) {
        $temas[] = $row;
    }

    return $temas;
}

?>