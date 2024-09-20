<?php
require_once 'vendor/autoload.php';
session_start();

// Crear una única instancia del cliente de Google
$client = new Google_Client();
$client->setClientId('737255136278-udfv56p46c9u8tqo6l61kt251aodu28p.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/sistemafaehorarios/index.php');
$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);

// Verificar si el token de acceso está disponible en la sesión
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    
    // Si el token ha expirado, usa el refresh token para obtener uno nuevo
    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        $_SESSION['access_token'] = $client->getAccessToken(); // Actualiza el token en la sesión
    }
} else {
    // Si no hay token, redirigir para obtener uno
    if (!isset($_GET['code'])) {
        header('Location: ' . $client->createAuthUrl());
        exit();
    } else {
        // Intercambiar el código por el token de acceso
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $_SESSION['access_token'] = $token;
        header('Location: ' . filter_var($client->getRedirectUri(), FILTER_SANITIZE_URL));
        exit();
    }
}

$service = new Google_Service_Oauth2($client);

try {
    // Obtener la información del usuario autenticado
    $userInfo = $service->userinfo->get();

    // Configura la conexión a la base de datos
    $dsn = 'mysql:host=localhost;dbname=sistemafaehorarios';
    $username = 'root';
    $password = 'root';

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener las materias, docentes, días y horas
    $stmt = $pdo->prepare('SELECT * FROM materias');
    $stmt->execute();
    $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare('SELECT * FROM docentes');
    $stmt->execute();
    $docentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare('SELECT * FROM dias');
    $stmt->execute();
    $dias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare('SELECT * FROM horas');
    $stmt->execute();
    $horas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Generar la tabla de horarios
    $tabla = '<table class="table table-bordered"><thead><tr>';

    foreach ($horas as $hora) {
        $tabla .= '<th>' . htmlspecialchars($hora['nombre_hora']) . '</th>';
    }

    $tabla .= '</tr></thead><tbody>';

    foreach ($dias as $dia) {
        $tabla .= '<tr><td>' . htmlspecialchars($dia['nombre_dia']) . '</td>';

        foreach ($horas as $hora) {
            $tabla .= '<td class="droppable" ondrop="drop(event)" ondragover="allowDrop(event)">';

            foreach ($materias as $materia) {
                $tabla .= '<div class="draggable" draggable="true" ondragstart="drag(event)">' . htmlspecialchars($materia['nombre_materia']) . '</div>';
            }

            $tabla .= '</td>';
        }

        $tabla .= '</tr>';
    }

    $tabla .= '</tbody></table>';

    echo $tabla;

} catch (PDOException $e) {
    echo 'Error en la base de datos: ' . $e->getMessage();
} catch (Exception $e) {
    echo 'Error en la API de Google: ' . $e->getMessage();
}
?>
