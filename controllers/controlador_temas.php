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
        case 'eliminarTodos':
            eliminarTodos();
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
/*Página: Temas Ventana:Aprobar temas */
function eliminarTodos() {
    global $conexion;

    $sql = "DELETE FROM topics";
    $stmt = $conexion->prepare($sql);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "pages/temas/vista.php");
        exit();  
    } else {
        echo "Error al eliminar todos los temas.";
    }
}
/* Página: Temas Ventana: Borrar temas */
function consultarTemas() {
    global $conexion;

    $allowed_columns = ["title", "topic", "created_at", "stage"];
    $allowed_directions = ["ASC", "DESC"];
    $order = isset($_GET['order']) && in_array($_GET['order'], $allowed_columns) ? $_GET['order'] : "created_at";
    $direction = isset($_GET['direction']) && in_array($_GET['direction'], $allowed_directions) ? $_GET['direction'] : "DESC";
    $new_direction = ($direction === "ASC") ? "DESC" : "ASC";
    $sql = "SELECT t.*, 
                   COALESCE(r.stage, 'No asignado') AS last_stage
            FROM topics t
            LEFT JOIN topic_rounds tr ON t.id = tr.topic_id
            LEFT JOIN rounds r ON tr.round_id = r.id
            GROUP BY t.id
            ORDER BY $order $direction";

    $result = $conexion->query($sql);
    if (!$result) {
        die("Error en la consulta: " . $conexion->error);
    }
    $temas = [];
    while ($row = $result->fetch_assoc()) {
        $temas[] = $row;
    }
    return [$temas, $new_direction]; 
}



/*Página: Mis temas */
function consultarDatosTemas($id) {
    global $conexion;

    $sql = "SELECT 
                t.id, 
                t.topic, 
                t.is_approved, 
                CASE 
                    WHEN t.is_approved = 1 THEN 'Aprobado'
                    WHEN t.is_approved = 0 THEN 'Pendiente'
                    ELSE 'Desconocido'
                END AS estado_tema, 
                IFNULL(
                    (SELECT GROUP_CONCAT(DISTINCT r.stage SEPARATOR ', ') 
                     FROM topic_rounds tr 
                     JOIN rounds r ON tr.round_id = r.id 
                     WHERE tr.topic_id = t.id), 
                    'Sin fase'
                ) AS stage, 
                COALESCE(v.total_votes, 0) AS total_votes, 
                COALESCE(v.total_puntos, 0) AS total_puntos
            FROM topics t
            LEFT JOIN (
                SELECT topic_id, COUNT(*) AS total_votes, SUM(value) AS total_puntos
                FROM votes 
                GROUP BY topic_id
            ) v ON t.id = v.topic_id
            WHERE t.user_id = ?
            GROUP BY t.id, t.topic, t.is_approved";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $temas = [];
    while ($row = $result->fetch_assoc()) {
        $temas[] = $row;
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
function consultarTemasPorClasificarse() {
    global $conexion;
    
    $order_by = "created_at DESC"; 
    $sql = "SELECT * FROM topics WHERE is_approved = TRUE AND disqualified = FALSE AND finalist = FALSE ORDER BY $order_by";
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

/*Página: Temas Ventana: Clasificados */
function temasClasificadosOrdenado() {
    global $conexion;

    // Obtener parámetros de ordenación
    $order = $_GET['order'] ?? 'total_puntos'; // Orden predeterminado por puntos
    $direction = ($_GET['direction'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC'; // Dirección

    // Permitir solo ciertos valores para evitar SQL Injection
    $allowed_columns = ['topic', 'total_votos', 'total_puntos'];
    if (!in_array($order, $allowed_columns)) {
        $order = 'total_puntos';
    }

    $sql = "SELECT t.id, t.topic, t.description, 
                   COALESCE(SUM(v.value), 0) AS total_puntos,
                   COUNT(v.id) AS total_votos
            FROM topics t
            LEFT JOIN votes v ON t.id = v.topic_id
            WHERE t.finalist = 1
            GROUP BY t.id, t.topic, t.description
            ORDER BY $order $direction";

    $result = $conexion->query($sql);

    $temas = [];
    while ($row = $result->fetch_assoc()) {
        $temas[] = $row;
    }

    return [$temas, $direction];
}

/*Página: Rondas Ventana: Crear */
function temasClasificados() {
    global $conexion;

    $sql = "SELECT t.id, t.topic, t.description, 
                   COALESCE(SUM(v.value), 0) AS total_puntos,
                   COUNT(v.id) AS total_votos
            FROM topics t
            LEFT JOIN votes v ON t.id = v.topic_id
            WHERE t.finalist = 1
            GROUP BY t.id, t.topic, t.description";

    $result = $conexion->query($sql);

    $temas = [];
    while ($row = $result->fetch_assoc()) {
        $temas[] = $row;
    }

    return $temas;
}

/*Página: Index */
function obtenerGanador() {
    global $conexion;

    // Obtener la última ronda final con ganadores
    $sql_ronda_final = "SELECT id FROM rounds WHERE stage = 'final' AND status = 'finished' ORDER BY end_date DESC LIMIT 1";
    $stmt_ronda = $conexion->prepare($sql_ronda_final);
    $stmt_ronda->execute();
    $result_ronda = $stmt_ronda->get_result();
    $ronda_final = $result_ronda->fetch_assoc();

    if (!$ronda_final) {
        return []; // No hay rondas finales finalizadas
    }

    $round_id = $ronda_final['id'];

    // Obtener los ganadores de la ronda final con sus votos y puntos de esa ronda
    $sql_final = "SELECT 
                    t.id, 
                    t.topic, 
                    t.description, 
                    COUNT(v.id) AS votos_final, 
                    SUM(v.value) AS puntos_final
                FROM topics t
                LEFT JOIN votes v ON t.id = v.topic_id
                WHERE t.winner = TRUE AND v.round_id = ?
                GROUP BY t.id, t.topic, t.description";

    $stmt_final = $conexion->prepare($sql_final);
    $stmt_final->bind_param("i", $round_id);
    $stmt_final->execute();
    $result_final = $stmt_final->get_result();

    $ganadores_final = [];
    while ($row = $result_final->fetch_assoc()) {
        $ganadores_final[$row['id']] = $row;
    }

    // Obtener el total de votos y puntos de todas las rondas (incluyendo la final)
    $ganadores_totales = [];
    foreach ($ganadores_final as $id => $ganador) {
        $sql_total = "SELECT 
                        COUNT(v.id) AS votos_totales, 
                        SUM(v.value) AS puntos_totales
                    FROM votes v
                    WHERE v.topic_id = ?";

        $stmt_total = $conexion->prepare($sql_total);
        $stmt_total->bind_param("i", $id);
        $stmt_total->execute();
        $result_total = $stmt_total->get_result();
        $totales = $result_total->fetch_assoc();

        // Agregar los valores totales al ganador
        $ganadores_final[$id]['votos_totales'] = $totales['votos_totales'];
        $ganadores_final[$id]['puntos_totales'] = $totales['puntos_totales'];
    }

    return $ganadores_final;
}


?>