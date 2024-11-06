<?php
// Habilitar errores de PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Asegúrate de tener una conexión a la base de datos
include 'db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Comprobar que se reciben los datos necesarios
    if (isset($_POST['celda_id']) && isset($_POST['materia_id'])) {
        $cellId = $_POST['celda_id'];
        $selectedMateriaId = $_POST['materia_id'];

        // Separar el diaId y horaId
        list($diaId, $horaId) = explode('_', $cellId);
        
        // Asegurarse que los IDs son números
        $diaId = (int)$diaId;
        $horaId = (int)$horaId;

        // Asegúrate de que tienes una consulta para obtener el nombre de la materia a partir de su ID
        $materiaQuery = "SELECT nombre_materia FROM materias WHERE id_materia = ?";
        $materiaStmt = $conexion->prepare($materiaQuery);
        $materiaStmt->bind_param('i', $selectedMateriaId);
        
        try {
            $materiaStmt->execute();
            $materiaStmt->bind_result($nombreMateria);
            $materiaStmt->fetch();
            $materiaStmt->close();

            if ($nombreMateria) {
                // Preparar la declaración SQL
                $sql = "INSERT INTO disponibilidad_guardada (id_dia, id_hora, nombre_materia) 
                        VALUES (?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                
                // Ejecutar la inserción
                $stmt->bind_param('iis', $diaId, $horaId, $nombreMateria);
                $stmt->execute();
                
                $response['success'] = true;
                $response['message'] = 'Materia guardada con éxito.';
                $stmt->close();
            } else {
                $response['message'] = 'ID de materia no válido.';
            }
        } catch (mysqli_sql_exception $e) {
            $response['message'] = 'Error en la base de datos: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Los parámetros cell_id o materia_id no fueron recibidos.';
    }
}

// Enviar respuesta JSON
header('Content-Type: application/json');
echo json_encode($response);

// Cerrar la conexión
$conexion->close();
?>
