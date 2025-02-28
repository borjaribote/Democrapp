<?php


if (!defined('INIT_LOADED')) {
    define('INIT_LOADED', true);
    require_once __DIR__ . '/../core/init.php';
}
accesoAutorizado("admin");

// Obtener el round_id desde la URL
$round_id = isset($_GET['round_id']) ? intval($_GET['round_id']) : 0;
if (!$round_id) {
    die("No se proporcionó un round_id válido.");
}

// Definir la ruta del log (ajusta la ruta según tu entorno)
$logFile = BASE_PATH . "logs/sendEmails.log"; // BASE_PATH se define en config.php

// Registrar inicio para depuración
error_log("Iniciando envío de correos para round_id = $round_id\n", 3, $logFile);

// Obtener todos los usuarios que tienen email
$sql = "SELECT email, username FROM users WHERE email IS NOT NULL";
$result = $conexion->query($sql);

if (!$result || $result->num_rows === 0) {
    error_log("No se encontraron usuarios con email.\n", 3, $logFile);
    die("No se encontraron usuarios.");
}

// Incluir PHPMailer (se carga desde vendor/autoload.php vía init.php o config.php)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Enviar el correo a cada usuario
while ($user = $result->fetch_assoc()) {
    $email = $user['email'];
    $name = $user['username'];

    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP – ajusta estos valores a tu proveedor
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'democrapweb@gmail.com';
        $mail->Password   = 'democrapweb123'; // Si usas 2FA, genera una contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Para TLS
        $mail->Port       = 587;

        $mail->setFrom('democrapweb@gmail.com', 'DemocrApp');
        $mail->addAddress($email, $name);

        $mail->Subject = "Nueva ronda iniciada en DemocrApp";
        $mail->Body    = "Se ha iniciado una nueva ronda en DemocrApp.\n\n¡Participa ahora!\n" . BASE_URL;

        $mail->send();
        error_log("Correo enviado a $email\n", 3, $logFile);
    } catch (Exception $e) {
        error_log("Error al enviar correo a $email: " . $mail->ErrorInfo . "\n", 3, $logFile);
    }
}

error_log("Finalizado envío de correos para round_id = $round_id\n", 3, $logFile);
echo "Correos enviados";
?>
