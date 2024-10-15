<?php
// Asegúrate de tener una conexión a la base de datos
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['materias'])) {
        $materias = $_POST['materias'];  // Recuperamos el array de materias

        foreach ($materias as $diaId => $horas) {
            foreach ($horas as $horaId => $materiasEnHora) {
                foreach ($materiasEnHora as $materia) {
                    // Inserta la materia en la base de datos
                    $sql = "INSERT INTO disponibilidad_guardada (id_dia, id_hora, nombre_materia) 
                            VALUES (?, ?, ?)";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bind_param('iis', $diaId, $horaId, $materia);
                    $stmt->execute();
                }
            }
        }
    }
    // Redirigir o mostrar un mensaje de éxito
    header("Location: generar.php");
    exit();
}
?>
