<?php
session_start();
require_once '../conexion.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    switch ($action) {
        case 'insert':
            insertarTema($_POST);
            break;
        case 'update':
            actualizarTema($_POST);
            break;
        case 'delete':
            eliminarTema($_POST['id']);
            break;
        default:
            echo "Acción no válida.";
    }
} else {
    echo "Método de solicitud no válido.";
}

function insertarTema($data) {
    global $conexion;
    $topic = $data['topic'];
   /*  list($title, $category) = generateTitleAndCategory($topic); */
    $title = "Título no disponible";
    $category = "Uncategorized";
    $sql = "INSERT INTO topics ( title, topic, category) VALUES (?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $title, $topic, $category);

    if ($stmt->execute()) {
        $_SESSION['new_topic'] = $topic;
        header("Location: " . BASE_URL . "index.php");
        exit(); 
    } else {
        echo "Error al registrar tema: " . $conexion->error;
    }
}

function eliminarTema($id) {
    global $conexion;

    $sql = "DELETE FROM temas WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Tema eliminado correctamente.";
    } else {
        echo "Error al eliminar tema.";
    }
}

function generateTitleAndCategory($topic) {
    $api_token = $api_token = getenv("HUGGING_FACE_TOKEN");; 
    $url = "https://api-inference.huggingface.co/models/mistralai/Mistral-7B";

    // Solicitud a la IA para generar un título y una categoría en inglés
    $data = json_encode([
        "inputs" => "Genera un título descriptivo y una categoría general (máximo 3 palabras), separados por un salto de línea para: " . $topic
    ]);

    $options = [
        "http" => [
            "header" => "Authorization: Bearer $api_token\r\n" .
                        "Content-Type: application/json\r\n",
            "method" => "POST",
            "content" => $data,
            "timeout" => 10,  // Evita bloqueos si la API responde lento
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    // Manejo de error si la API no responde
    if ($response === FALSE) {
        return ["error" => "⚠️ Error al conectar con la API de Hugging Face"];
    }

    $result = json_decode($response, true);

    // Verificar que la respuesta contenga los datos esperados
    if (!isset($result[0]["generated_text"])) {
        return [
            "title" => "Título no disponible",
            "category" => "Uncategorized"
        ];
    }

    // Separar el resultado en título y categoría
    $generated_text = trim($result[0]["generated_text"]);
    $parts = explode("\n", $generated_text);

    // Asegurar que ambas partes existen y devolver los valores
    return [
        "title" => trim($parts[0] ?? "Título no disponible"),
        "category" => trim($parts[1] ?? "Uncategorized"),
    ];
}

/* 
function generarTituloHuggingFace($description) {
    $api_token = $api_token = getenv("HUGGING_FACE_TOKEN");;  // Reemplaza con tu clave de Hugging Face
    $url = "https://api-inference.huggingface.co/models/mistralai/Mistral-7B";

    $data = json_encode(["inputs" => "Genera un título breve para: " . $description]);

    $options = [
        "http" => [
            "header" => "Authorization: Bearer $api_token\r\n" .
                        "Content-Type: application/json\r\n",
            "method" => "POST",
            "content" => $data,
            "timeout" => 10, // Evita bloqueos en caso de respuesta lenta
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        return "⚠️ Error al generar título"; // Si falla la API, usar un mensaje de error
    }

    $resultado = json_decode($response, true);
    return $resultado[0]["generated_text"] ?? "Título no disponible";
}
 */
/* 
function compararTemas($tema1, $tema2) {
    $api_token = $api_token = getenv("HUGGING_FACE_TOKEN");;  // Reemplaza con tu API Key
    $url = "https://api-inference.huggingface.co/models/sentence-transformers/all-MiniLM-L6-v2";

    // Construir el JSON con los textos a comparar
    $data = json_encode([
        "inputs" => [
            "source_sentence" => $tema1,
            "sentences" => [$tema2]
        ]
    ]);

    $options = [
        "http" => [
            "header" => "Authorization: Bearer $api_token\r\n" .
                        "Content-Type: application/json\r\n",
            "method" => "POST",
            "content" => $data,
            "timeout" => 10,
        ],
    ];

    $context = stream_context_create($options);
    $respuesta = file_get_contents($url, false, $context);

    if ($respuesta === FALSE) {
        return "⚠️ Error al comparar temas.";
    }

    // Decodificar la respuesta JSON
    $resultado = json_decode($respuesta, true);

    // Extraer la puntuación de similitud
    $similitud = $resultado[0];  // Similaridad como número (ejemplo: 0.87)
    return $similitud;
} */

?>