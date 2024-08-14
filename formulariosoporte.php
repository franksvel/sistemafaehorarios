<?php
require_once 'vendor/autoload.php';

session_start();

// Configura el cliente de Google
$client = new Google_Client();
$client->setClientId('737255136278-udfv56p46c9u8tqo6l61kt251aodu28p.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setScopes(Google_Service_Gmail::GMAIL_SEND);

if (isset($_SESSION['access_token']) && $_SESSION['access_token']['expires_in'] > time()) {
    $client->setAccessToken($_SESSION['access_token']);
    $service = new Google_Service_Gmail($client);
} else {
    echo 'No hay acceso válido. Por favor, inicie sesión.';
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = htmlspecialchars($_POST['nombre']);
    $email = htmlspecialchars($_POST['email']);
    $mensaje = htmlspecialchars($_POST['mensaje']);

    $to = 'frankedy.sanchez.velasco@gmail.com'; // Cambia esto por la dirección del administrador
    $subject = "Mensaje de Contacto de $nombre";
    $body = "Nombre: $nombre\nCorreo Electrónico: $email\n\nMensaje:\n$mensaje";

    $rawMessageString = "From: $email\r\n";
    $rawMessageString .= "To: $to\r\n";
    $rawMessageString .= "Subject: $subject\r\n";
    $rawMessageString .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $rawMessageString .= "\r\n";
    $rawMessageString .= $body;

    $rawMessage = base64url_encode($rawMessageString);
    
    $message = new Google_Service_Gmail_Message();
    $message->setRaw($rawMessage);

    try {
        $service->users_messages->send('me', $message);
        echo "El mensaje ha sido enviado exitosamente.";
    } catch (Exception $e) {
        echo "Hubo un problema al enviar el mensaje: " . htmlspecialchars($e->getMessage());
    }
} else {
    echo 'Método de solicitud no válido.';
}

function base64url_encode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}
?>
