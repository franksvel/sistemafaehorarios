<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_materia = $_POST['id_materia'];
    $nombre_materia = $_POST['nombre_materia'];

    // Verificar si el nombre de la materia ya existe en otra fila
    $sql_check = "SELECT * FROM materia WHERE nombre_materia = '$nombre_materia' AND id_materia != '$id_materia'";
    $result_check = mysqli_query($conexion, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Si ya existe una materia con ese nombre
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
                text: "El nombre de la materia ya está registrado.",
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
        // Si el nombre de la materia no existe, proceder con la actualización
        $sql_update = "UPDATE materia SET nombre_materia = '$nombre_materia' WHERE id_materia = '$id_materia'";
        
        if (mysqli_query($conexion, $sql_update)) {
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
                        window.location.href = "materia.php";
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
                    text: "Hubo un error al actualizar la materia: ' . mysqli_error($conexion) . '",
                    icon: "error",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "materia.php";
                    }
                });
            </script>
            </body>
            </html>';
        }
    }
}
?>
