<?php
include 'db.php';

// Verificar que se reciban los datos del formulario
if (!isset($_POST['id_docente']) || !isset($_POST['id_materia'])) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Datos del formulario no recibidos.',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'asignar_materia.php'; // Redirigir a la página del formulario
            }
        });
    </script>";
    exit; // Termina la ejecución del script
}

// Obtener datos del formulario
$id_docente = $_POST['id_docente'];
$id_materia = $_POST['id_materia'];

// Insertar en la base de datos
$sql = "INSERT INTO asignacion (id_docente, id_materia) VALUES (?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_docente, $id_materia);

// Agregar script de SweetAlert para mensajes de éxito o error
echo '<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>';

if ($stmt->execute()) {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Asignación exitosa',
            text: 'Materia asignada correctamente.',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'horarios.php'; // Redirige a la página que desees
            }
        });
    </script>";
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al asignar la materia: " . $stmt->error . "',
            confirmButtonText: 'OK'
        });
    </script>";
}

echo '</body>
</html>';

$stmt->close();
$conexion->close();
?>
