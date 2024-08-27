<?php
require_once 'db.php'; // Incluye tu archivo de conexión a la base de datos

if (mysqli_connect_errno()) {
    echo "Error de conexión: " . mysqli_connect_error();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $materia = $_POST['nombre_materia'];

    // Función para generar un color hexadecimal aleatorio
    function generarColorHexadecimal() {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    // Consultar si la materia ya existe
    $consultar_registro = "SELECT nombre_materia FROM materia WHERE nombre_materia = '$materia'";
    $resultado = mysqli_query($conexion, $consultar_registro);

    if (mysqli_num_rows($resultado) > 0) {
        // La materia ya existe
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
                    text: "Ya existe un registro con ese nombre.",
                    icon: "warning",
                    confirmButtonText: "Aceptar"
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = "materia.php";
                    }
                  });
                </script>
              </body>
              </html>';
    } else {
        // Generar un color aleatorio
        $color = generarColorHexadecimal();

        // Insertar la nueva materia con el color en la base de datos
        $sql = "INSERT INTO materia (nombre_materia, color) VALUES ('$materia', '$color')";
        $resultado = mysqli_query($conexion, $sql);

        if ($resultado) {
            echo '<!DOCTYPE html>
                  <html>
                  <head>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                  </head>
                  <body>
                    <script>
                      Swal.fire({
                        title: "Éxito",
                        text: "La materia se ha guardado exitosamente.",
                        icon: "success",
                        confirmButtonText: "Aceptar"
                      }).then((result) => {
                        if (result.isConfirmed) {
                          window.location.href = "materia.php";
                        }
                      });
                    </script>
                  </body>
                  </html>';
        } else {
            echo "Error: " . mysqli_error($conexion);
        }
    }

    // Cerrar la conexión
    mysqli_close($conexion);
}
?>
