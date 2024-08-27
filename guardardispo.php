<?php
session_start();
include 'db.php';

// Función para obtener el nombre del docente
function getDocenteName($conexion, $id_docente) {
    $query = "SELECT nombre_d, apellido_p, apellido_m FROM docente WHERE id_docente = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $id_docente);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['nombre_d'] . ' ' . $row['apellido_p'] . ' ' . $row['apellido_m'];
    }
    return "El docente con ID $id_docente no existe.";
}

// Función para verificar si un día existe
function diaExiste($conexion, $id_dia) {
    $query = "SELECT COUNT(*) FROM dia WHERE id_dia = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $id_dia);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_array()[0];
    return $count > 0;
}

// Función para verificar si una hora existe
function horaExiste($conexion, $id_hora) {
    $query = "SELECT COUNT(*) FROM disponibilidad WHERE id_hora = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $id_hora);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_array()[0];
    return $count > 0;
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_docente = $_POST['id_docente'];
    $id_carrera = $_POST['id_carrera'];
    $id_dias = $_POST['id_dia'];
    $id_horas = $_POST['id_hora'];
    $semestre = $_POST['id_semestre'];  // Recibiendo el semestre desde el formulario

    // Obtener nombre del docente
    $nombre_docente = getDocenteName($conexion, $id_docente);
    $errores = [];
    $registro_guardado = false;

    // Verificar que los días y horas existen
    foreach ($id_dias as $id_dia) {
        if (!diaExiste($conexion, $id_dia)) {
            $errores[] = "El día con ID $id_dia no existe.";
            continue;
        }

        foreach ($id_horas as $id_hora) {
            if (!horaExiste($conexion, $id_hora)) {
                $errores[] = "La hora con ID $id_hora no existe.";
                continue;
            }

            // Insertar directamente sin verificar condiciones previas
            $query = "INSERT INTO general (id_docente, id_carrera, id_dia, id_hora, id_semestre) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param('iiiii', $id_docente, $id_carrera, $id_dia, $id_hora, $semestre);

            if (!$stmt->execute()) {
                $errores[] = "Error al guardar los datos para el día $id_dia a la hora $id_hora: " . $stmt->error;
            } else {
                $registro_guardado = true;
            }
        }
    }

    // Mostrar alerta con SweetAlert2
    echo '<!DOCTYPE html>
          <html>
          <head>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
          </head>
          <body>
            <script>
              let errors = ' . json_encode($errores) . ';
              if (errors.length > 0) {
                Swal.fire({
                  title: "¡Error!",
                  text: errors.join("\\n"),
                  icon: "error",
                  confirmButtonText: "Aceptar"
                }).then((result) => {
                  if (result.isConfirmed) {
                    window.location.href = "disponibilidada.php";
                  }
                });
              } else if (' . json_encode($registro_guardado) . ') {
                Swal.fire({
                  title: "Éxito",
                  text: "La disponibilidad se ha guardado exitosamente.",
                  icon: "success",
                  confirmButtonText: "Aceptar"
                }).then((result) => {
                  if (result.isConfirmed) {
                    window.location.href = "disponibilidada.php";
                  }
                });
              }
            </script>
          </body>
          </html>';
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
