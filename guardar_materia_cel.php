<?php
require_once 'db.php'; 

// Verifica si las variables están definidas
if (!isset($dsn) || !isset($username) || !isset($password)) {
    die("Las variables de conexión no están definidas. Asegúrate de que el archivo db.php esté correctamente configurado.");
}

try {
    // Conectar a la base de datos
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados desde el formulario
    $materias = $_POST['materias'] ?? [];

    // Comenzar una transacción
    $pdo->beginTransaction();
    try {
        // Recorrer cada materia enviada
        foreach ($materias as $diaId => $horas) {
            foreach ($horas as $horaId => $listaMaterias) {
                // Eliminar materias existentes para este día y hora antes de insertar nuevas
                $stmtDelete = $pdo->prepare("DELETE FROM materias WHERE dia_id = ? AND hora_id = ?");
                $stmtDelete->execute([$diaId, $horaId]);
                
                // Guardar las nuevas materias
                foreach ($listaMaterias as $nombreMateria) {
                    $nombreMateria = trim($nombreMateria); // Limpiar espacios
                    if (!empty($nombreMateria)) { // Verificar que no esté vacío
                        $stmtInsert = $pdo->prepare("INSERT INTO materias (nombre_materia, dia_id, hora_id) VALUES (?, ?, ?)");
                        $stmtInsert->execute([$nombreMateria, $diaId, $horaId]);
                    }
                }
            }
        }

        // Confirmar la transacción
        $pdo->commit();

        // Redirigir o mostrar mensaje de éxito
        echo "Materias guardadas exitosamente.";
    } catch (Exception $e) {
        // Si ocurre un error, revertir la transacción
        $pdo->rollBack();
        echo "Error al guardar materias: " . $e->getMessage();
    }
} else {
    echo "No se recibió información para guardar.";
}
?>
