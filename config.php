<?php
require_once 'vendor/autoload.php';

session_start();

// Configuración del cliente de Google
$client = new Google_Client();
$client->setClientId('737255136278-udfv56p46c9u8tqo6l61kt251aodu28p.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/sistemafaehorarios/callback.php');
$client->addScope('email');
$client->addScope('profile');
$client->setAccessType('offline');
$client->setPrompt('select_account consent');
?>
