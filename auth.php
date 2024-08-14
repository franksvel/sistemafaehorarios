<?php
require_once 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('737255136278-udfv56p46c9u8tqo6l61kt251aodu28p.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/sistemafaehorarios/auth.php');
$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);

if (!isset($_GET['code'])) {
    // Redirigir al usuario a la página de autorización de Google
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit();
} else {
    // Intercambiar el código de autorización por un token de acceso
    $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    // Establecer el tiempo de expiración del token en la sesión
    $_SESSION['access_token']['expires_at'] = time() + $_SESSION['access_token']['expires_in'];
    header('Location: http://localhost/sistemafaehorarios/dashboard.php');
    exit();
}
?>
