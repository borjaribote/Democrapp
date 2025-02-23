<?php
if (!defined('INIT_LOADED')) {
    define('INIT_LOADED', true);
    require_once __DIR__ . '/../core/init.php';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id =  $_POST['user_id'];
    $round_id = $_POST['round_id'];
    $votes = [];
    if (!empty($_POST['token-3'])) {
        $votes[$_POST['token-3']] = 3;
    }
    if (!empty($_POST['token-2'])) {
        $votes[$_POST['token-2']] = 2;
    }
    if (!empty($_POST['token-1'])) {
        $votes[$_POST['token-1']] = 1;
    }

    // Validar que los datos son correctos
    if (!$user_id || !$round_id || count($votes) !== 3) {
        exit();
    }

    guardarVotos($user_id, $round_id, $votes);
}

function guardarVotos($user_id, $round_id, $votes) {
    global $conexion;
    $conexion->begin_transaction(); 
    try {
        foreach ($votes as $topic_id => $value) {
            if (!is_numeric($topic_id) || !is_numeric($value) || $value < 1 || $value > 3) {
                throw new Exception("Datos inválidos");
            }

            $sql = "INSERT INTO votes (user_id, topic_id, round_id, value) VALUES (?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("iiii", $user_id, $topic_id, $round_id, $value);
            $stmt->execute();
        }     
        $conexion->commit(); 
        header("Location: " . BASE_URL . "index.php?mensaje=voto_guardado");
        exit();
    } catch (Exception $e) {
        $conexion->rollback(); 
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



