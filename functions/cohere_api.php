<?php 
require_once __DIR__ . '/../vendor/autoload.php'; 

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../core');
$dotenv->load();

$api_key = $_ENV['COHERE_API_KEY'] ?? getenv('COHERE_API_KEY') ?? null;
$prompt_ruta = $_SERVER['DOCUMENT_ROOT'] . "/proyectos/democrapp/assets/documents/prompt.txt";
$prompt = file_exists($prompt_ruta) ? trim(file_get_contents($prompt_ruta)) : null;


if (!$api_key || !$prompt) {
    die("Error: La API Key o el prompt no están configurados.");
}
function generarTituloGlobalCohere($tema) {
    global $api_key, $prompt;
    
    $url = "https://api.cohere.ai/v1/chat"; 
    
    $prompt = str_replace("{TEXTO_USUARIO}", $tema, $prompt);

    $data = json_encode([
        "message" => $prompt,
        "model" => "command-r-plus", 
        "max_tokens" => 10,
        "temperature" => 0.05
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    $headers = [
        "Authorization: Bearer " . trim($api_key),
        "Content-Type: application/json"
    ];

    $options = [
        "http" => [
            "header" => implode("\r\n", $headers),
            "method" => "POST",
            "content" => $data
        ]
    ];


    $context = stream_context_create($options);
    $respuesta = @file_get_contents($url, false, $context);

    if ($respuesta === FALSE) {
        return "Error al conectar con Cohere.";
    }

    $resultado = json_decode($respuesta, true);

    $titulo = isset($resultado["text"]) ? trim($resultado["text"]) : null;

    $titulo = str_replace(["Título Global: ", "Título Global:"], "", $titulo);
    $titulo = trim($titulo); 

    if (empty($titulo)) {
        return "Error: La IA no generó un título válido.";
    }else{
        return $titulo;
    }

}

?>