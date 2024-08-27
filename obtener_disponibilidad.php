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

// Funciones para obtener los días de la semana, las horas disponibles y la disponibilidad de los docentes
function obtenerDiasDeLaSemana($conexion) {
    $query = "SELECT id_dia, nombre_dia FROM dia ORDER BY id_dia";
    $result = $conexion->query($query);
    $dias = [];
    while ($row = $result->fetch_assoc()) {
        $dias[$row['id_dia']] = $row['nombre_dia'];
    }
    return $dias;
}

function obtenerHorasDisponibles($conexion) {
    $query = "SELECT id_hora, nombre_hora FROM hora ORDER BY id_hora";
    $result = $conexion->query($query);
    $horas = [];
    while ($row = $result->fetch_assoc()) {
        $horas[$row['id_hora']] = $row['nombre_hora'];
    }
    return $horas;
}

function obtenerDisponibilidad($conexion) {
    $disponibilidad = [];
    $consulta = "SELECT d.id_dia, d.id_hora, d.id_docente, d.color, m.nombre_materia 
                 FROM disponibilidad_docente d 
                 LEFT JOIN materia m ON d.id_materia = m.id_materia";

    $resultado = $conexion->query($consulta);

    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $dia = $fila['id_dia'];
            $hora = $fila['id_hora'];
            $disponibilidad[$dia][$hora] = [
                'id_docente' => $fila['id_docente'],
                'color' => $fila['color'],
                'nombre' => $fila['nombre_materia'] ?: 'No asignado'
            ];
        }
    }

    return $disponibilidad;
}

// Obtener los días, horas y disponibilidad
$dias = obtenerDiasDeLaSemana($conexion);
$horas = obtenerHorasDisponibles($conexion);
$disponibilidad = obtenerDisponibilidad($conexion);
?>
