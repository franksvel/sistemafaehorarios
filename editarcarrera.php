<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_carrera = $_POST['id_carrera'] ?? null;
    $nombre_c = $_POST['nombre_c'] ?? null;

    // Verifica que los campos necesarios no estén vacíos
    if (empty($id_carrera) || empty($nombre_c)) {
        echo '<script>alert("Faltan campos requeridos."); window.history.back();</script>';
        exit();
    }

    // Consulta para verificar si el nombre de la carrera ya existe en otra fila
    $sql_check = "SELECT COUNT(*) as total FROM carrera WHERE nombre_c = ? AND id_carrera != ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param('si', $nombre_c, $id_carrera);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();

    if ($row_check['total'] > 0) {
        // Si ya existe una carrera con ese nombre
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
        <script>
            Swal.fire({
                title: "Error",
                text: "El nombre de la carrera ya está registrado.",
                icon: "error",
                confirmButtonText: "Aceptar"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.history.back();  // Volver al formulario anterior
                }
            });
        </script>
        </body>
        </html>';
    } else {
        // Si el nombre de la carrera no existe, proceder con la actualización
        $sql_update = "UPDATE carrera SET nombre_c = ? WHERE id_carrera = ?";
        $stmt_update = $conexion->prepare($sql_update);
        $stmt_update->bind_param('si', $nombre_c, $id_carrera);

        if ($stmt_update->execute()) {
            // Si la actualización es exitosa, muestra un mensaje de éxito
            echo '
            <!DOCTYPE html>
            <html>
            <head>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
            <script>
                Swal.fire({
                    title: "Editado",
                    text: "El registro se ha editado con éxito.",
                    icon: "success",
                    confirmButtonText: "Aceptar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "carrera.php";
                    }
                });
            </script>
            </body>
            </html>';
        } else {
            // Si hay un error, muestra un mensaje de error
            echo '
            <!DOCTYPE html>
            <html>
            <head>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
            <script>
                Swal.fire({
                    title: "Error",
                    text: "Hubo un error al actualizar la carrera: ' . mysqli_error($conexion) . '",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "carrera.php";
                    }
                });
            </script>
            </body>
            </html>';
        }
    }

    // Cerrar la conexión
    $stmt_check->close();
    $stmt_update->close();
    mysqli_close($conexion);
}
?>
