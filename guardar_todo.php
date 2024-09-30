<?php
include 'db.php'; // Conexión a la base de datos

$inputData = json_decode(file_get_contents('php://input'), true);

if ($inputData) {
    foreach ($inputData as $celda) {
        $dia = intval($celda['dia']);
        $hora = intval($celda['hora']);
        $materias = $celda['materias'];

        // Eliminar las materias actuales para esa celda (opcional)
        $deleteQuery = "DELETE FROM disponibilidad WHERE id_dia = $dia AND id_hora = $hora";
        $conexion->query($deleteQuery);

        // Insertar las nuevas materias
        foreach ($materias as $materiaNombre) {
            // Obtener el ID de la materia a partir del nombre
            $result = $conexion->query("SELECT id_materia FROM materia WHERE nombre_materia = '$materiaNombre' LIMIT 1");
            if ($result->num_rows > 0) {
                $materia = $result->fetch_assoc();
                $id_materia = $materia['id_materia'];

                // Insertar la nueva asignación
                $insertQuery = "INSERT INTO disponibilidad (id_dia, id_hora, id_materia) VALUES ($dia, $hora, $id_materia)";
                $conexion->query($insertQuery);
            }
        }
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se recibieron datos.']);
}
?>
