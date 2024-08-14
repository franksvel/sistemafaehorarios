<?php
session_start();

// Destruir la sesión y redirigir al usuario
session_unset();
session_destroy();
header('Location: http://localhost/sistemafaehorarios/index.php');
exit();
?>
