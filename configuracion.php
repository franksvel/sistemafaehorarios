<?php
require_once 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('737255136278-udfv56p46c9u8tqo6l61kt251aodu28p.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/sistemafaehorarios/auth.php');
$client->addScope(Google_Service_Gmail::GMAIL_READONLY);

if (!isset($_SESSION['access_token']) || $_SESSION['access_token'] === null) {
    header('Location: http://localhost/sistemafaehorarios/index.php');
    exit();
}

$client->setAccessToken($_SESSION['access_token']);
$service = new Google_Service_Gmail($client);


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuracion</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="dashboard.php">Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Menú
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="perfil.php">Perfil</a>
                            <a class="dropdown-item" href="#">Configuración</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php">Cerrar sesión</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        
        <h1 class="mt-4"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="80" height="80" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
  <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
</svg>Configuración</h1>
        <ul class="list-group mt-3">
        
        </ul>
    </div>

    <div class="card-container">
        <a href="roles.php">
        <div class="card">
            <i class="fas fa-person card-icon"></i>
            <h2 class="card-title">Roles</h2>
            <p class="card-description">Explora nuestras opciones para el hogar.</p>
        </div>
        </a>
        <a href="usuarios.php">
        <div class="card">
            <i class="fas fa-user card-icon"></i>
            <h2 class="card-title">Usuarios</h2>
            <p class="card-description">Encuentra soluciones para tu entorno laboral.</p>
        </div>
        </a>
        <a href="docentes.php">
        <div class="card">
            <i class="fas fa-chalkboard-user card-icon"></i>
            <h2 class="card-title">Docentes</h2>
            <p class="card-description">Mejora tu salud con nuestros consejos.</p>
        </div>
         </a>
         <a href="materia.php">
        <div class="card">
            <i class="fas fa-book card-icon"></i>
            <h2 class="card-title">Materia</h2>
            <p></p>
        </div>
         </a>
    </div>
    
    <a href="carrera.php">
    <div class="card-container">
        <div class="card">
            <i class="fas fa-school card-icon"></i>
            <h2 class="card-title">Carrera</h2>
            <p class="card-description">Explora nuestras opciones para el hogar.</p>
        </div>
    </a>
        <a href="disponibilidada.php">
        <div class="card">
            <i class="fas fa-briefcase card-icon"></i>
            <h2 class="card-title">Disponibilidad</h2>
            <p class="card-description">Encuentra soluciones para tu entorno laboral.</p>
        </div>
         </a>
         <a href="horarios.php">
         <div class="card">
            <i class="fas fa-clock card-icon"></i>
            <h2 class="card-title">Horarios</h2>
            <p class="card-description">Mejora tu salud con nuestros consejos.</p>
        </div>
         </a>
         <a href="soporte.php">
         <div class="card">
            <i class="fas fa-headset card-icon"></i>
            <h2 class="card-title">Soporte</h2>
            <p></p>
        </div>
         </a>
    </div>
    

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
