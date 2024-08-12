<?php

include 'db.php';

// Verifica si se ha proporcionado un ID de materia
if (isset($_GET['id_docente']) && is_numeric($_GET['id_docente'])) {
    $id = intval($_GET['id_docente']); // Asegúrate de que el ID sea un número entero

    // Usa una consulta preparada para evitar SQL Injection
    $stmt = $conexion->prepare("DELETE FROM docente WHERE id_docente = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Si la eliminación es exitosa, redirige con SweetAlert2
        echo '<!DOCTYPE html>
              <html>
              <head>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
              </head>
              <body>
                <script>
                  Swal.fire({
                    title: "Eliminado",
                    text: "El registro se ha eliminado con éxito.",
                    icon: "success",
                    confirmButtonText: "Aceptar"
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = "docentes.php";
                    }
                  });
                </script>
              </body>
              </html>';
    } else {
        // Si la eliminación falla, muestra un error
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
                    text: "La eliminación del registro falló.",
                    icon: "error",
                    confirmButtonText: "Aceptar"
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = "docentes.php";
                    }
                  });
                </script>
              </body>
              </html>';
    }

    // Cierra la consulta y la conexión a la base de datos
    $stmt->close();
    mysqli_close($conexion);
} else {
    // Si no se proporciona un ID, muestra un mensaje de error
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
                text: "No se ha proporcionado ningún ID de docente.",
                icon: "error",
                confirmButtonText: "Aceptar"
              }).then((result) => {
                if (result.isConfirmed) {
                  window.location.href = "docentes.php";
                }
              });
            </script>
          </body>
          </html>';
}

?>
