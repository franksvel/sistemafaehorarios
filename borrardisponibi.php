<?php
session_start();
include 'db.php';

// Verificar que el parámetro 'id_general' está presente
if (isset($_GET['id_general'])) {
    $id_general = $_GET['id_general']; // Asegúrate de usar 'id_general' entre comillas

    // Consulta para eliminar todos los registros en la tabla 'general' relacionados con el id_general
    $query = "DELETE FROM general WHERE id_general = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $id_general); // Asumiendo que id_general es un entero

    if ($stmt->execute()) {
        // Notificación de éxito con SweetAlert2
        echo '<!DOCTYPE html>
              <html>
              <head>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
              </head>
              <body>
                <script>
                  Swal.fire({
                    title: "Registros eliminados",
                    text: "Toda la disponibilidad ha sido eliminada exitosamente.",
                    icon: "success",
                    confirmButtonText: "Aceptar"
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = "disponibilidada.php"; // Redirigir a la página de disponibilidad
                    }
                  });
                </script>
              </body>
              </html>';
    } else {
        // Notificación de error con SweetAlert2
        echo '<!DOCTYPE html>
              <html>
              <head>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
              </head>
              <body>
                <script>
                  Swal.fire({
                    title: "Error",
                    text: "No se pudo eliminar la disponibilidad.",
                    icon: "error",
                    confirmButtonText: "Aceptar"
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = "disponibilidada.php"; // Redirigir a la página de disponibilidad
                    }
                  });
                </script>
              </body>
              </html>';
    }
} else {
    // Redireccionar en caso de que falte el parámetro 'id_general'
    header("Location: disponibilidada.php");
    exit();
}

// Cerrar la conexión a la base de datos
$stmt->close();
$conexion->close();
?>
