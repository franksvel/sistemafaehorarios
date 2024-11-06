<?php
include 'db.php';

$id_general = isset($_POST['id_general']) ? intval($_POST['id_general']) : 0;

if ($id_general <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID no válido']);
    exit();
}

$query = "DELETE FROM general WHERE id_general = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param('i', $id_general);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar el registro']);
}

$stmt->close();
$conexion->close();
?>
