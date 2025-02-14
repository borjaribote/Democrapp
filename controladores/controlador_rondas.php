<?php
require_once '../core/init.php';

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
    $start_date = $data['start_date'];
    $end_date = $data['end_date'];
    $status = 'inactive'; // Estado por defecto

    // Insertar la nueva ronda
    $sql = "INSERT INTO rounds (name, start_date, end_date, status) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssss", $name, $start_date, $end_date, $status);

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
        header("Location: " . BASE_URL . "pages/administrar_rondas.php");
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
        header("Location: " . BASE_URL . "pages/administrar_rondas.php");
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
        header("Location: " . BASE_URL . "pages/administrar_rondas.php");
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

    $sql = "SELECT id, name, DATE_FORMAT(start_date, '%d-%m-%Y') AS start_date, 
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


?>
