<?php
// Incluir el archivo de configuración para la conexión a la base de datos
include 'db.php';

// Obtener los datos enviados desde el cliente
$data = json_decode(file_get_contents('php://input'), true);
$nombre = isset($data['nombre']) ? trim($data['nombre']) : '';

// Verificar si el campo está vacío
if (empty($nombre)) {
    echo json_encode(['success' => false, 'message' => 'El campo de nombre es requerido']);
    exit;
}

// Consulta para verificar si el registro ya existe
$sql = "SELECT * FROM materia WHERE nombre_materia = ?";
$stmt = $conn->prepare($sql);

// Verificar si la preparación de la consulta fue exitosa
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta']);
    exit;
}

// Vincular el parámetro y ejecutar la consulta
$stmt->bind_param("s", $nombre);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si el registro ya existe
if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'El nombre ya está registrado']);
} else {
    // Consulta para insertar un nuevo registro
    $insertSql = "INSERT INTO materia (nombre_materia) VALUES (?)";
    $insertStmt = $conn->prepare($insertSql);
    
    if (!$insertStmt) {
        echo json_encode(['success' => false, 'message' => 'Error en la preparación de la consulta de inserción']);
        exit;
    }
    
    // Vincular el parámetro y ejecutar la consulta de inserción
    $insertStmt->bind_param("s", $nombre);
    
    if ($insertStmt->execute()) {
        echo json_encode(['success' => true, 'message' => '¡Materia agregada exitosamente!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar los datos']);
    }
    
    $insertStmt->close();
}

$stmt->close();
$conn->close();
?>
