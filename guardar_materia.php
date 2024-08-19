<?php
require_once 'db.php'; // Asegúrate de tener la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_docente = $_POST['id_docente'];
    $id_dia = $_POST['id_dia'];
    $id_hora = $_POST['id_hora'];
    $id_materia = $_POST['id_materia'];
    $color = $_POST['color'];

    // Verificar si ya existe una entrada para este docente, día y hora
    $query = "SELECT * FROM materia_docente WHERE id_docente = ? AND id_dia = ? AND id_hora = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('iii', $id_docente, $id_dia, $id_hora);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Actualizar la entrada existente
        $query = "UPDATE materia_docente SET id_materia = ?, color = ? WHERE id_docente = ? AND id_dia = ? AND id_hora = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param('isiii', $id_materia, $color, $id_docente, $id_dia, $id_hora);
    } else {
        // Insertar nueva entrada
        $query = "INSERT INTO materia_docente (id_docente, id_dia, id_hora, id_materia, color) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param('iiiis', $id_docente, $id_dia, $id_hora, $id_materia, $color);
    }

    if ($stmt->execute()) {
        header('Location: generar.php'); // Redirigir después de guardar
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
