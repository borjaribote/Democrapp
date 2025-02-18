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


function insertarRonda($data) {
    global $conexion;

    $name = $data['name'];
    $stage = $data['stage'];
    $start_date = $data['start_date'];
    $end_date = $data['end_date'];
    $status = 'inactive'; // Estado por defecto

    // Insertar la nueva ronda
    $sql = "INSERT INTO rounds (name, stage, start_date, end_date, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssss", $name, $stage, $start_date, $end_date, $status);

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

/**
 *  Eliminar una ronda por ID
 */
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

/**
 *  Consultar todas las rondas activas
 */
function obtenerRondasConTemas() {
    global $conexion;

    $sql = "SELECT id, name, stage, DATE_FORMAT(start_date, '%d-%m-%Y') AS start_date, 
                   DATE_FORMAT(end_date, '%d-%m-%Y') AS end_date, status 
            FROM rounds ORDER BY start_date DESC";
    $result = $conexion->query($sql);
    $rondas = [];

    while ($ronda = $result->fetch_assoc()) {
        // Obtener los temas y sus votos para cada ronda
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
            $temas[] = $tema; // Incluye tema y número de votos
        }

        // Agregar los temas y votos a la ronda
        $ronda['temas'] = $temas;
        $rondas[] = $ronda;
    }

    return $rondas;
}

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
        die("Error en la consulta: " . $conexion->error); // Para depuración
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

        // Obtener todos los temas de la ronda activa
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

    return $ronda; // Devuelve el array con los datos
}

?>
