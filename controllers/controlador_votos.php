<?php
if (!defined('INIT_LOADED')) {
    define('INIT_LOADED', true);
    require_once __DIR__ . '/../core/init.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $round_id = $_POST['round_id'] ?? null;
    $votes = $_POST['votes'] ?? [];

    if (!$user_id || !$round_id || count($votes) !== 3) {
        header("Location: " . BASE_URL . "index.php?mensaje=error_voto");
        exit();
    }

    guardarVotos($user_id, $round_id, $votes);
}

/**
 * Función para guardar los votos en la base de datos
 */
function guardarVotos($user_id, $round_id, $votes) {
    global $conexion;

    $conexion->begin_transaction(); // Iniciar transacción
    try {
        foreach ($votes as $topic_id => $points) {
            $sql = "INSERT INTO votes (user_id, topic_id, round_id, points) VALUES (?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("iiii", $user_id, $topic_id, $round_id, $points);
            $stmt->execute();
        }
        
        $conexion->commit(); // Confirmar transacción
        header("Location: " . BASE_URL . "index.php?mensaje=voto_guardado");
        exit();
    } catch (Exception $e) {
        $conexion->rollback(); // Revertir transacción en caso de error
        header("Location: " . BASE_URL . "index.php?mensaje=error_voto");
        exit();
    }
}


/**
 * Función para verificar si un usuario ya ha votado en una ronda
 */
function haVotadoEnRonda($user_id, $round_id) {
    global $conexion;
    
    $sql = "SELECT COUNT(*) FROM votes WHERE user_id = ? AND round_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $user_id, $round_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    
    return $count > 0;
}




function obtenerGanadorRonda($round_id) {
    global $conexion;
    if (!$round_id) return null;

    $sql = "SELECT t.topic, COUNT(v.user_id) AS total_votes
            FROM topics t
            LEFT JOIN votes v ON t.id = v.topic_id
            WHERE t.id IN (SELECT topic_id FROM topic_rounds WHERE round_id = ?)
            GROUP BY t.id
            ORDER BY total_votes DESC
            LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $round_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?: null;
}

function obtenerResultadosRonda($round_id) {
    global $conexion;
    if (!$round_id) return [];

    $sql = "SELECT t.id AS topic_id, t.title AS topic_title, t.topic AS topic, COUNT(v.user_id) AS total_votes
            FROM topics t
            LEFT JOIN votes v ON t.id = v.topic_id
            WHERE t.id IN (SELECT topic_id FROM topic_rounds WHERE round_id = ?)
            GROUP BY t.id, t.title, t.topic
            ORDER BY total_votes DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $round_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $resultados = [];
    while ($row = $result->fetch_assoc()) {
        $resultados[] = $row;
    }

    return $resultados;
}


