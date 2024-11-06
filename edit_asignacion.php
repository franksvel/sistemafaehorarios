<?php
require_once 'vendor/autoload.php';
include 'db.php'; // Asegúrate de que este archivo contenga la conexión a la base de datos

session_start();

// Verificar si el usuario está autenticado (ajusta esto según tu lógica de autenticación)
if (!isset($_SESSION['access_token']) || $_SESSION['access_token'] === null) {
    header('Location: http://localhost/sistemafaehorarios/index.php');
    exit();
}

// Variable para el mensaje
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $id_docente = $_POST['id_docente'];
    $id_materia = $_POST['id_materia'];

    // Actualización en la base de datos
    $query = "UPDATE asignacion SET id_materia = ? WHERE id_docente = ?";
    
    if ($stmt = $conexion->prepare($query)) {
        $stmt->bind_param("si", $id_materia, $id_docente);
        if ($stmt->execute()) {
            // Mensaje de éxito
            $message = "Asignación actualizada con éxito";
        } else {
            // Mensaje de error
            $message = "Error al actualizar la asignación: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Error en la preparación de la consulta: " . $conexion->error;
    }
}

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Asignación</title>
    <!-- Incluye la librería de SweetAlert -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
    // Mostrar el mensaje de SweetAlert si existe
    <?php if (!empty($message)) : ?>
        Swal.fire({
            title: 'Notificación',
            text: "<?php echo $message; ?>",
            icon: "<?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>",
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location = 'horarios.php'; // Redirige a la vista deseada
            }
        });
    <?php endif; ?>
</script>

</body>
</html>
