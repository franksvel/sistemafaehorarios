<?php
include 'db.php';

// Verifica si se ha proporcionado un ID de materia
if (isset($_GET['id_materia']) && is_numeric($_GET['id_materia'])) {
    $id = intval($_GET['id_materia']); // Asegúrate de que el ID sea un número entero

    // Usa una consulta preparada para evitar SQL Injection
    $stmt = $conexion->prepare("DELETE FROM materia WHERE id_materia = ?");
    $stmt->bind_param("i", $id);
    
    // Función para mostrar alertas SweetAlert
    function mostrarAlerta($titulo, $mensaje, $icono, $redireccion) {
        echo '<!DOCTYPE html>
              <html>
              <head>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
              </head>
              <body>
                <script>
                  Swal.fire({
                    title: "' . $titulo . '",
                    text: "' . $mensaje . '",
                    icon: "' . $icono . '",
                    confirmButtonText: "Aceptar"
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = "' . $redireccion . '";
                    }
                  });
                </script>
              </body>
              </html>';
    }

    // Ejecuta la consulta y muestra la alerta adecuada
    if ($stmt->execute()) {
        mostrarAlerta("Eliminado", "El registro se ha eliminado con éxito.", "success", "materia.php");
    } else {
        mostrarAlerta("Error", "La eliminación del registro falló.", "error", "materia.php");
    }

    // Cierra la consulta y la conexión a la base de datos
    $stmt->close();
    $conexion->close();
} else {
    // Si no se proporciona un ID, muestra un mensaje de error
    mostrarAlerta("Error", "No se ha proporcionado ningún ID de materia.", "error", "materia.php");
}
?>
