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
            actualizarRonda($_POST);
            break;
        case 'delete':
            eliminarRonda($_POST['round_id']);
            break;
        default:
            echo " Acción no válida.";
    }
}

/*Página: Rondas Ventana: Crear ronda*/
/*Insertar una nueva ronda con hora de inicio */
function insertarRonda($data) {
    global $conexion;

    $stage = $data['stage'];
    $start_date = $data['start_date'];
    $start_time = $data['start_time']; // Nueva hora de inicio
    $end_date = $data['end_date'];
    $end_time = $data['end_time'];
    $start_datetime = $start_date . ' ' . $start_time . ':00';
    $end_datetime = $end_date . ' ' . $end_time . ':00';
    $status = 'inactive'; // Estado por defecto

    // Insertar la nueva ronda
    $sql = "INSERT INTO rounds (stage, start_date, end_date, status) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssss", $stage, $start_datetime, $end_datetime, $status);

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
 *  Actualizar 
 */
function actualizarRonda($data) {
    global $conexion;

    $round_id = $data['round_id'];
    $status = $data['status'];
    $start_date = $data['start_date'];
    $start_time = $data['start_time'];
    $end_date = $data['end_date'];
    $end_time = $data['end_time'];
    $start_datetime = $start_date . ' ' . $start_time . ':00';
    $end_datetime = $end_date . ' ' . $end_time . ':00';

    $sql = "UPDATE rounds 
            SET status = ?, start_date = ?, end_date = ? 
            WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssi", $status, $start_datetime, $end_datetime, $round_id);

    if ($stmt->execute()) {
        header("Location: " . BASE_URL . "pages/rondas/vista.php?ronda_actualizada");
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

/* Página: Rondas Ventana: Gestionar rondas */
function obtenerRondasConTemas() {
    global $conexion;
    $sql = "SELECT id, stage, 
                   DATE_FORMAT(start_date, '%d-%m-%Y') AS start_date, 
                   DATE_FORMAT(start_date, '%H:%i') AS start_time, 
                   DATE_FORMAT(end_date, '%d-%m-%Y') AS end_date, 
                   DATE_FORMAT(end_date, '%H:%i') AS end_time, status 
            FROM rounds ORDER BY start_date DESC";
    $result = $conexion->query($sql);
    $rondas = [];

    while ($ronda = $result->fetch_assoc()) {
        // Obtener el total de puntos sumando el campo "value" en la tabla votes
        $temasSql = "SELECT t.topic, 
                            COALESCE((SELECT SUM(v.value) FROM votes v WHERE v.topic_id = t.id), 0) AS total_puntos
                     FROM topics t
                     JOIN topic_rounds tr ON t.id = tr.topic_id
                     WHERE tr.round_id = ?
                     ORDER BY total_puntos DESC";
                     
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

    $sql = "SELECT 
                r.id AS round_id, 
                r.stage, 
                r.start_date, 
                r.end_date, 
                t.id AS topic_id, 
                t.title, 
                t.description,
                COALESCE(v.total_votes, 0) AS total_votes,
                COALESCE(v.total_puntos, 0) AS total_puntos
            FROM rounds r
            LEFT JOIN topic_rounds tr ON r.id = tr.round_id
            LEFT JOIN topics t ON tr.topic_id = t.id
            LEFT JOIN (
                SELECT topic_id, COUNT(*) AS total_votes, SUM(value) AS total_puntos
                FROM votes 
                GROUP BY topic_id
            ) v ON t.id = v.topic_id
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
                    'description' => $row['description'],
                    'total_votes' => $row['total_votes'],
                    'total_puntos' => $row['total_puntos']
                ];
            }
        } while ($row = $result->fetch_assoc());
    }

    return $ronda; 
}


