<?php
session_start();
include 'db.php';

// Verificar que los parámetros necesarios están presentes
if (isset($_GET['id_docente'], $_GET['id_materia'])) {
    // Obtener los valores
    $id_docente = intval($_GET['id_docente']); // Asegúrate de que sea un entero
    $id_materia = intval($_GET['id_materia']); // Asegúrate de que sea un entero

    // Consulta para eliminar el registro en la tabla 'asignacion'
    $query = "DELETE FROM asignacion WHERE id_docente = ? AND id_materia = ?";
    $stmt = $conexion->prepare($query);

    if ($stmt) {
        // Vincular los parámetros
        $stmt->bind_param('ii', $id_docente, $id_materia); // Cambiar a 'ii' porque son enteros

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
                        title: "Asignación eliminada",
                        text: "La asignación se ha eliminado exitosamente.",
                        icon: "success",
                        confirmButtonText: "Aceptar"
                      }).then((result) => {
                        if (result.isConfirmed) {
                          window.location.href = "horarios.php"; // Redirigir a la página de disponibilidad
                        }
                      });
                    </script>
                  </body>
                  </html>';
        } else {
            // Notificación de error si no se pudo eliminar
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
                        text: "No se pudo eliminar la asignación. Intente nuevamente.",
                        icon: "error",
                        confirmButtonText: "Aceptar"
                      }).then((result) => {
                        if (result.isConfirmed) {
                          window.location.href = "horarios.php"; // Redirigir a la página de disponibilidad
                        }
                      });
                    </script>
                  </body>
                  </html>';
        }
    } else {
        // Manejo de error si la preparación de la consulta falla
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
                    text: "No se pudo preparar la consulta. Intente nuevamente.",
                    icon: "error",
                    confirmButtonText: "Aceptar"
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = "horarios.php"; // Redirigir a la página de disponibilidad
                    }
                  });
                </script>
              </body>
              </html>';
    }
} else {
    // Redireccionar en caso de que falten parámetros
    header("Location: horarios.php");
    exit();
}

// Cerrar la conexión a la base de datos
$stmt->close();
$conexion->close();
?>
