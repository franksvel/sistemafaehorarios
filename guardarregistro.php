<?php

include 'db.php';

if (mysqli_connect_errno()) {
    echo "Error de conexión: " . mysqli_connect_error();
    exit();
}

//$materia = isset($_POST['nombre_materia']) ? $_POST['nombre_materia'] : null;
$materia=$_POST['nombre_materia'];



$mensaje= "¡Ocurrio un error! La materia ya existe vuelve a intentarlo";

$consultar_registro = "SELECT nombre_materia FROM materia WHERE nombre_materia = '$materia'";
$resultado=mysqli_query($conexion,$consultar_registro);

if (mysqli_num_rows($resultado) > 0) {
    // Hay resultados, la materia existe en la base de datos
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
                text: "Ya existe un registro",
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

    $sql = "INSERT INTO materia (nombre_materia) VALUES ('$materia')";
    $resultado= mysqli_query($conexion,$sql);
    
    if($resultado){

    echo '<!DOCTYPE html>
          <html>
          <head>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
          </head>
          <body>
            <script>
              Swal.fire({
                title: "Exito",
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
    } 

}

// Cerrar la conexión
mysqli_close($conexion);

//header("location:materia.php");
//echo '<script>alert("Los datos se han guardado con exito");</script>';

?>
