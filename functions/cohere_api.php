<?php 
require_once '../core/init.php';
require_once '../vendor/autoload.php';


use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable("../core");
$dotenv->load();
$prompt_ruta = BASE_PATH."assets/documents/prompt.txt";
$prompt = file_exists($prompt_ruta) ? trim(file_get_contents($prompt_ruta)) : null;
 
if (!$api_key || !$prompt) {
    die("⚠️ Error: La API Key o el prompt no están configurados.");
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
        return "⚠️ Error al conectar con Cohere.";
    }

    $resultado = json_decode($respuesta, true);

    $titulo = isset($resultado["text"]) ? trim($resultado["text"]) : null;

    if (empty($titulo)) {
        return "⚠️ Error: La IA no generó un título válido.";
    }

    return $titulo;
}

?>