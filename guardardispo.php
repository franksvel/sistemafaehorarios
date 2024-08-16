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

// Función para obtener el nombre del día
function getDiaName($conexion, $id_dia) {
    $query = "SELECT nombre_dia FROM dia WHERE id_dia = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $id_dia);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['nombre_dia'];
    }
    return "El día con ID $id_dia no existe.";
}

// Función para obtener el nombre de la hora
function getHoraInicio($conexion, $id_hora) {
    $query = "SELECT nombre_hora FROM disponibilidad WHERE id_hora = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $id_hora);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['nombre_hora'];
    }
    return "La hora con ID $id_hora no existe.";
}

// Función para verificar si ya existe un registro con la misma hora y día en la misma carrera
function existeRegistroEnMismaCarrera($conexion, $id_hora, $id_dia, $id_carrera) {
    $query = "SELECT COUNT(*) FROM general WHERE id_hora = ? AND id_dia = ? AND id_carrera = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('iii', $id_hora, $id_dia, $id_carrera);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_array()[0];
    return $count > 0;
}

// Función para verificar si el docente ya tiene ocupada una hora en otra carrera
function docenteOcupadoEnOtraCarrera($conexion, $docente, $hora, $dia, $carrera) {
    $query = "SELECT COUNT(*) FROM general WHERE id_docente = ? AND id_hora = ? AND id_dia = ? AND id_carrera != ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('iiii', $docente, $hora, $dia, $carrera);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_array()[0];
    return $count > 0;
}

// Función para verificar si un día existe
function diaExiste($conexion, $id_dia) {
    $query = "SELECT COUNT(*) FROM dia WHERE id_dia = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $id_dia);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_array()[0];
    if ($count == 0) {
        return "El día con ID $id_dia no existe.";
    }
    return null;
}

// Función para verificar si una hora existe
function horaExiste($conexion, $id_hora) {
    $query = "SELECT COUNT(*) FROM disponibilidad WHERE id_hora = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $id_hora);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_array()[0];
    if ($count == 0) {
        return "La hora con ID $id_hora no existe.";
    }
    return null;
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_docente = $_POST['id_docente'];
    $id_carrera = $_POST['id_carrera'];
    $id_dias = $_POST['id_dia'];
    $id_horas = $_POST['id_hora'];

    // Obtener nombre del docente
    $nombre_docente = getDocenteName($conexion, $id_docente);
    $errores = [];
    $registro_guardado = false;
    
    foreach ($id_dias as $id_dia) {
        $mensaje_error_dia = diaExiste($conexion, $id_dia);
        if ($mensaje_error_dia) {
            $errores[] = $mensaje_error_dia;
            continue;
        }

        foreach ($id_horas as $id_hora) {
            $mensaje_error_hora = horaExiste($conexion, $id_hora);
            if ($mensaje_error_hora) {
                $errores[] = $mensaje_error_hora;
                continue;
            }

            // Verificar si ya existe un registro en la misma hora, día y carrera
            if (existeRegistroEnMismaCarrera($conexion, $id_hora, $id_dia, $id_carrera)) {
                $errores[] = "Ya existe un registro.";
                break 2; // Salir de ambos bucles si encontramos un error
            }

            if (docenteOcupadoEnOtraCarrera($conexion, $id_docente, $id_hora, $id_dia, $id_carrera)) {
                $errores[] = "El docente $nombre_docente ya está ocupado el día y la hora en otra carrera.";
                break 2; // Salir de ambos bucles si encontramos un error
            }
        }
    }

    if (empty($errores)) {
        $query = "INSERT INTO general (id_docente, id_carrera, id_dia, id_hora) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($query);
        foreach ($id_dias as $id_dia) {
            foreach ($id_horas as $id_hora) {
                if (!existeRegistroEnMismaCarrera($conexion, $id_hora, $id_dia, $id_carrera) && !docenteOcupadoEnOtraCarrera($conexion, $id_docente, $id_hora, $id_dia, $id_carrera)) {
                    $stmt->bind_param('iiii', $id_docente, $id_carrera, $id_dia, $id_hora);
                    if (!$stmt->execute()) {
                        $errores[] = "Error al guardar los datos para el día $id_dia a la hora $id_hora: " . $stmt->error;
                    }
                }
            }
        }
        $registro_guardado = true;
    } elseif (empty($errores)) {
        $errores[] = "No se encontró ninguna combinación válida.";
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
                  text: errors[0],
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
