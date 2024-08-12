<?php
include 'db.php';

if (mysqli_connect_errno()) {
    echo "Error de conexión: " . mysqli_connect_error();
    exit();
}

$id_docente = $_POST['id_docente'];
$nombre = $_POST['nombre_d'];
$apellidod = $_POST['apellido_p'];
$apellidom = $_POST['apellido_m'];

// Verificar si el nombre del docente ya existe en otra fila
$sql_check = "SELECT * FROM docente WHERE nombre_d = '$nombre' AND id_docente != '$id_docente'";
$result_check = mysqli_query($conexion, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    // Hay resultados, el nombre del docente ya existe en otra fila
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
    // Si el nombre del docente no existe, actualizar o insertar el registro
    $sql_update = "UPDATE docente SET nombre_d = '$nombre', apellido_p = '$apellidod', apellido_m = '$apellidom' WHERE id_docente = '$id_docente'";
    $resultado = mysqli_query($conexion, $sql_update);
    
    if ($resultado) {
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
                    title: "Éxito",
                    text: "El docente ha sido actualizado exitosamente.",
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
                    text: "Hubo un error al actualizar el docente: ' . mysqli_error($conexion) . '",
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
