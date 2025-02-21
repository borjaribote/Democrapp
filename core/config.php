<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Cargar Composer

use Dotenv\Dotenv;

// Cargar las variables del archivo .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtener la API Key desde el .env
$api_key = $_ENV['COHERE_API_KEY'] ?? $_SERVER['COHERE_API_KEY'] ?? null;

if (!$api_key) {
    die("Error: La API Key de Cohere no está configurada.");
}


$root = $_SERVER['DOCUMENT_ROOT'] . "/proyectos/democrapp/";
define("BASE_URL", "http://localhost/proyectos/democrapp/");
define("BASE_PATH", $root);

?>
