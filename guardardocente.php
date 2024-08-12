<?php
include 'db.php';

if (mysqli_connect_errno()) {
    echo "Error de conexión: " . mysqli_connect_error();
    exit();
}

// Obtener datos del formulario
$matricula = $_POST['matricula'];
$nombre = $_POST['nombre_d'];
$apellidod = $_POST['apellido_p'];
$apellidom = $_POST['apellido_m'];

// Verificar si ya existe un registro con el mismo nombre
$sql_check = "SELECT * FROM docente WHERE nombre_d = ? AND apellido_p = ? AND apellido_m = ?";
$stmt_check = $conexion->prepare($sql_check);
$stmt_check->bind_param('sss', $nombre, $apellidod, $apellidom);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Hay resultados, el nombre del docente ya existe en otra fila
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
                text: "Ya existe un docente con el mismo nombre.",
                icon: "warning",
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
    // Insertar el nuevo registro
    $sql_insert = "INSERT INTO docente (matricula, nombre_d, apellido_p, apellido_m) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conexion->prepare($sql_insert);
    $stmt_insert->bind_param('ssss', $matricula, $nombre, $apellidod, $apellidom);
    $resultado = $stmt_insert->execute();
    
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
                    text: "El docente se ha guardado exitosamente.",
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
        // Error al insertar
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
                    text: "Hubo un error al guardar el docente: ' . mysqli_error($conexion) . '",
                    icon: "error",
                    confirmButtonText: "OK"
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = "docentes.php";
                    }
                  });
                </script>
              </body>
              </html>';
    }
}

// Cerrar la conexión
mysqli_close($conexion);
?>
