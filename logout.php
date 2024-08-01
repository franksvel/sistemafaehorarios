<?php
session_start();

// Verifica si el usuario está autenticado
if (isset($_SESSION['access_token'])) {
    // Opción para revocar el token (opcional)
    require_once 'vendor/autoload.php';

    $client = new Google_Client();
    $client->setClientId('737255136278-udfv56p46c9u8tqo6l61kt251aodu28p.apps.googleusercontent.com');
    $client->setClientSecret('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');
    $client->setRedirectUri('http://localhost/sistemafaehorarios/auth.php');
    $client->setAccessToken($_SESSION['access_token']);
    
    $service = new Google_Service_Oauth2($client);

    // Revocar el token de acceso (opcional)
    $client->revokeToken($_SESSION['access_token']);
    
    // Borra la sesión
    session_unset();
    session_destroy();
}

// Redirige al inicio
header('Location: index.php');
exit();
