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

$data = json_decode(file_get_contents('php://input'), true);

$id_docente_origen = $data['id_docente_origen'];
$dia_origen = $data['dia_origen'];
$hora_origen = $data['hora_origen'];
$id_docente_destino = $data['id_docente_destino'];
$dia_destino = $data['dia_destino'];
$hora_destino = $data['hora_destino'];

// Actualizar la base de datos
$query_origen = "UPDATE disponibilidad_docente SET id_docente = NULL WHERE id_docente = ? AND id_dia = ? AND id_hora = ?";
$stmt_origen = $conexion->prepare($query_origen);
$stmt_origen->bind_param("iii", $id_docente_origen, $dia_origen, $hora_origen);
$stmt_origen->execute();

$query_destino = "UPDATE disponibilidad_docente SET id_docente = ? WHERE id_dia = ? AND id_hora = ?";
$stmt_destino = $conexion->prepare($query_destino);
$stmt_destino->bind_param("iii", $id_docente_destino, $dia_destino, $hora_destino);
$stmt_destino->execute();

if ($stmt_origen->affected_rows >= 0 && $stmt_destino->affected_rows >= 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
