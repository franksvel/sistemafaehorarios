<?php
include 'db.php';

// Obtener datos del formulario
$id_docente = $_POST['id_docente'];
$id_materia = $_POST['id_materia'];

// Insertar en la base de datos
$sql = "INSERT INTO asignacion (id_docente, id_materia) VALUES (?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_docente, $id_materia);

if ($stmt->execute()) {
    echo "Materia asignada correctamente.";
} else {
    echo "Error al asignar la materia: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
