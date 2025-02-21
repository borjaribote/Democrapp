<?php
if (!defined('INIT_LOADED')) {
    define('INIT_LOADED', true);
    require_once __DIR__ . '/../core/init.php';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    switch ($action) {
        case 'insert':
            insertarRonda($_POST);
            break;
        case 'update':
            actualizarRonda($_POST['round_id'], $_POST['status']);
            break;
        case 'delete':
            eliminarRonda($_POST['round_id']);
            break;
        default:
            echo " Acción no válida.";
    }
}

/*Página: Rondas Ventana: Crear ronda*/
function insertarRonda($data) {
    global $conexion;

    $name = $data['name'];
    $stage = $data['stage'];
    $start_date = $data['start_date'];
    $end_date = $data['end_date'];
    $end_time = $data['end_time'];
    $end_datetime = $end_date . ' ' . $end_time . ':00';
    $status = 'inactive'; // Estado por defecto
    
    // Insertar la nueva ronda
    $sql = "INSERT INTO rounds (name, stage, start_date, end_date, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssss", $name, $stage, $start_date, $end_datetime, $status);

    if ($stmt->execute()) {
        $round_id = $stmt->insert_id; // Obtener el ID de la ronda creada

        // Insertar los temas seleccionados en `topic_rounds`
        if (!empty($data['topics'])) {
            foreach ($data['topics'] as $topic_id) {
                $sqlTopic = "INSERT INTO topic_rounds (topic_id, round_id) VALUES (?, ?)";
                $stmtTopic = $conexion->prepare($sqlTopic);
                $stmtTopic->bind_param("ii", $topic_id, $round_id);
                $stmtTopic->execute();
            }
        }
        header("Location: " . BASE_URL . "pages/rondas/vista.php?ronda_creada");
        exit();
    } else {
        echo "Error al registrar la ronda: " . $conexion->error;
    }
}

/**
 * Página: Rondas Ventana: Gestionar rondas
 *  Actualizar el estado de una ronda (activar/inactivar/finalizar)
 */
function actualizarRonda($id, $status) {
    global $conexion;

    $sql = "UPDATE rounds SET status = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "pages/rondas/vista.php");
        exit();
    } else {
        echo "Error al actualizar la ronda.";
    }
}

/* Página: Rondas Ventana: Gestionar rondas */
function eliminarRonda($id) {
    global $conexion;

    $sql = "DELETE FROM rounds WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "pages/rondas/vista.php");
        exit();
    } else {
        echo "Error al eliminar la ronda.";
    }
}

/*Página: Rondas Ventana: Gestionar rondas*/
function obtenerRondasConTemas() {
    global $conexion;
    $sql = "SELECT id, name, stage, DATE_FORMAT(start_date, '%d-%m-%Y') AS start_date, 
                   DATE_FORMAT(end_date, '%d-%m-%Y') AS end_date, 
                   DATE_FORMAT(end_date, '%H:%i') AS end_time, status 
            FROM rounds ORDER BY start_date DESC";
    $result = $conexion->query($sql);
    $rondas = [];

    while ($ronda = $result->fetch_assoc()) {
        $temasSql = "SELECT t.topic, 
                            (SELECT COUNT(*) FROM votes v WHERE v.topic_id = t.id) AS votos
                     FROM topics t
                     JOIN topic_rounds tr ON t.id = tr.topic_id
                     WHERE tr.round_id = ?
                     ORDER BY votos DESC";
        $stmt = $conexion->prepare($temasSql);
        $stmt->bind_param("i", $ronda['id']);
        $stmt->execute();
        $temasResult = $stmt->get_result();

        $temas = [];
        while ($tema = $temasResult->fetch_assoc()) {
            $temas[] = $tema; 
        }
        $ronda['temas'] = $temas;
        $rondas[] = $ronda;
    }

    return $rondas;
}

/*Página: Index */
function obtenerRondaActiva() {
    global $conexion;

    $sql = "SELECT r.id AS round_id, r.name AS round_name, r.stage, r.start_date, r.end_date, 
                   t.id AS topic_id, t.title, t.description 
            FROM rounds r
            LEFT JOIN topic_rounds tr ON r.id = tr.round_id
            LEFT JOIN topics t ON tr.topic_id = t.id
            WHERE r.status = 'active'
            ORDER BY r.start_date ASC
            LIMIT 1";

    $result = $conexion->query($sql);

    if (!$result) {
        die("Error en la consulta: " . $conexion->error); 
    }

    $ronda = [];
    if ($row = $result->fetch_assoc()) {
        $ronda = [
            'round_id' => $row['round_id'],
            'round_name' => $row['round_name'],
            'stage' => $row['stage'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'topics' => []
        ];

        do {
            if ($row['topic_id']) {
                $ronda['topics'][] = [
                    'topic_id' => $row['topic_id'],
                    'title' => $row['title'],
                    'description' => $row['description']
                ];
            }
        } while ($row = $result->fetch_assoc());
    }

    return $ronda; 
}

/* Página: Index Estado: Resultados */
function obtenerUltimaRondaActiva() {
    global $conexion;

    $sql = "SELECT id, name, stage FROM rounds 
            WHERE status IN ('active', 'finished') 
            ORDER BY end_date DESC LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc() ?: null;
}
?>
