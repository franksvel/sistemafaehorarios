<?php
// Conexión a la base de datos
include 'db.php'; // Asegúrate de que este archivo contiene la conexión correcta a la BD

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica si se han enviado datos de materias
    if (isset($_POST['nombre_materia']) && is_array($_POST['nombre_materia'])) {
        $materias = $_POST['materias']; // Recoge el array de materias

        // Prepara la consulta para insertar o actualizar los datos
        $sql = "INSERT INTO disponibilidad_guardada (dia_id, hora_id, id_materia) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE materia = VALUES(nombre_materia)";
        $stmt = $con->prepare($sql);

        if ($stmt) {
            foreach ($materias as $diaId => $horas) {
                foreach ($horas as $horaId => $materiaArray) {
                    foreach ($materiaArray as $materia) {
                        // Vincula los parámetros y ejecuta la consulta
                        $stmt->bind_param("iis", $diaId, $horaId, $materia);
                        if (!$stmt->execute()) {
                            $response['message'] = 'Error al guardar una materia.';
                            echo json_encode($response);
                            exit;
                        }
                    }
                }
            }
            $stmt->close();
            $response['success'] = true;
            $response['message'] = 'Horario guardado correctamente.';
        } else {
            $response['message'] = 'Error al preparar la consulta.';
        }
    } else {
        $response['message'] = 'No se han recibido materias.';
    }
} else {
    $response['message'] = 'Método no permitido.';
}

$conn->close();

// Enviar respuesta en formato JSON
echo json_encode($response);
?>
