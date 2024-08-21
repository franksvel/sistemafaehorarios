<?php
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

$id_docente_origen = $data['id_docente_origen'];
$dia_origen = $data['dia_origen'];
$hora_origen = $data['hora_origen'];
$id_docente_destino = $data['id_docente_destino'];
$dia_destino = $data['dia_destino'];
$hora_destino = $data['hora_destino'];

$response = ['success' => false];

$conexion->begin_transaction();

try {
    // Intercambiar las asignaciones entre las celdas

    // Cambiar la asignación de origen al destino
    if ($id_docente_destino !== "") {
        $query = "INSERT INTO materia_docente (id_docente, id_dia, id_hora) VALUES (?, ?, ?)
                  ON DUPLICATE KEY UPDATE id_docente = VALUES(id_docente)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("iii", $id_docente_destino, $dia_destino, $hora_destino);
        $stmt->execute();
    }

    // Cambiar la asignación del destino al origen
    if ($id_docente_origen !== "") {
        $query = "INSERT INTO materia_docente (id_docente, id_dia, id_hora) VALUES (?, ?, ?)
                  ON DUPLICATE KEY UPDATE id_docente = VALUES(id_docente)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("iii", $id_docente_origen, $dia_origen, $hora_origen);
        $stmt->execute();
    }

    $conexion->commit();
    $response['success'] = true;
} catch (Exception $e) {
    $conexion->rollback();
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>
