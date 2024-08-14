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
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="vistad.php">Dashboard</a>
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
                            <a class="dropdown-item" href="configuracion.php">Configuración</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php">Cerrar sesión</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        
        <h1 class="mt-4"><i class="fa-solid fa-briefcase m-2"></i>Disponibilidad</h1>
        <div class="container">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Agregar Disponibilidad</button> 
    </div>
    </div>
    <div class="container">
        <table class="table m-2 mt-4">
            <thead>
                <tr>
                    <th>Nombre(s) de docente</th>
                    <th>Carrera</th>
                    <th>Materia</th>
                    <th>Dia</th>
                    <th>Hora</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
   


        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar datos del docente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="guardardocente.php" method="POST">
                        <div class="form-group">
                            <label for="matricula">Selecciona el nombre de docente*</label>
                            <input type="text" class="form-control" placeholder="Ingrese la Matricula" required name="matricula">
                            <small class="form-text text-muted">Por favor, ingrese la matricula del docente. Toma en cuenta que se toma el valor de Y desde un inicio</small>
                        </div>
                        <div class="form-group">
                            <label for="nombre_d">Nombre(s)</label>
                            <input type="text" class="form-control" placeholder="Ingresa los Nombre(s)" required name="nombre_d">
                        </div>
                        <div class="form-group">
                        <div class="row">
            <div class="col-6 col-md-4 col-lg-2 mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias" value="lunes" id="lunes">
                    <label class="form-check-label" for="lunes">Lunes</label>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias" value="martes" id="martes">
                    <label class="form-check-label" for="martes">Martes</label>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias" value="miércoles" id="miércoles">
                    <label class="form-check-label" for="miércoles">Miércoles</label>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias" value="jueves" id="jueves">
                    <label class="form-check-label" for="jueves">Jueves</label>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="dias" value="viernes" id="viernes">
                    <label class="form-check-label" for="viernes">Viernes</label>
                </div>
            </div>
        </div>
                        </div>
                        <div class="form-group">
                            <label for="apellido_m">Apellido Materno</label>
                            <input type="text" class="form-control" placeholder="Apellido Materno" required name="apellido_m">
                        </div>
                        <input type="submit" class="btn btn-primary" value="Guardar">
                    </form>
                </div>
            </div>
        </div>
    </div>







    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
