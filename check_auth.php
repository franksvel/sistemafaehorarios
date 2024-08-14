<?php
session_start();

function is_authenticated() {
    if (isset($_SESSION['access_token']) && isset($_SESSION['access_token']['expires_at'])) {
        return time() < $_SESSION['access_token']['expires_at'];
    }
    return false;
}

if (!is_authenticated()) {
    header('Location: http://localhost/sistemafaehorarios/auth.php');
    exit();
}
?>
