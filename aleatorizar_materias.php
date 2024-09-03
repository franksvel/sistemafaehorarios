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

// Incluir el archivo de conexión a la base de datos
include 'db.php';

// Función para obtener las materias disponibles
function obtenerMaterias($conexion) {
    $query = "SELECT id_materia FROM materia";
    $result = $conexion->query($query);
    $materias = [];
    while ($row = $result->fetch_assoc()) {
        $materias[] = $row['id_materia'];
    }
    return $materias;
}

// Función para obtener los días y horas disponibles
function obtenerDiasHoras($conexion) {
    $query = "SELECT id_dia, id_hora FROM general GROUP BY id_dia, id_hora";
    $result = $conexion->query($query);
    $diasHoras = [];
    while ($row = $result->fetch_assoc()) {
        $diasHoras[] = ['id_dia' => $row['id_dia'], 'id_hora' => $row['id_hora']];
    }
    return $diasHoras;
}

// Función para aleatorizar las materias y asignarlas
function aleatorizarMaterias($conexion) {
    $materias = obtenerMaterias($conexion);
    $diasHoras = obtenerDiasHoras($conexion);

    foreach ($diasHoras as $diaHora) {
        $id_dia = $diaHora['id_dia'];
        $id_hora = $diaHora['id_hora'];

        // Eliminar asignaciones anteriores para el día y la hora
        $conexion->query("DELETE FROM asignacion WHERE id_dia = $id_dia AND id_hora = $id_hora");

        // Aleatorizar las materias y asignar una al día y hora
        shuffle($materias);
        $id_materia = $materias[array_rand($materias)];

        $query = "INSERT INTO asignacion (id_docente, id_materia, id_dia, id_hora) 
                  SELECT d.id_docente, $id_materia, $id_dia, $id_hora
                  FROM general g
                  INNER JOIN docente d ON g.id_docente = d.id_docente
                  WHERE g.id_dia = $id_dia AND g.id_hora = $id_hora
                  LIMIT 1";
        $conexion->query($query);
    }
}

// Ejecutar la aleatorización
aleatorizarMaterias($conexion);

echo json_encode(['success' => true]);
?>
