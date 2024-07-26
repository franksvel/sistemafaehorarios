<?php
include 'db.php';
$docente=$_POST['nombre'];
$apellido=$_POST['apellido'];
$telefono=$_POST['telefono'];
$matricula=$_POST['matricula'];

$mensaje= "¡Ocurrio un error! La matricula ya existe vuelve a intentarlo";

$consultar_registro = "SELECT matricula FROM docente WHERE matricula = '$matricula'";
$resultado=mysqli_query($conexion,$consultar_registro);

if (mysqli_num_rows($resultado) > 0) {
    // Hay resultados, la matricula existe en la base de datos
    echo "<script type='text/javascript'>
        alert('$mensaje');
        window.location.href = 'docente.php';
      </script>";
} else {

    $consulta="INSERT INTO docente(nombre, apellido, telefono, matricula) VALUES ('$docente','$apellido','$telefono','$matricula')";
    $resultado=mysqli_query($conexion,$consulta);
    
    if($resultado){
    	echo "<script type='text/javascript'>
            alert('¡Docente agregado de manera exitosa!');
            window.location.href = 'docente.php';
          </script>";
    }
}
mysqli_free_result($resultado);
mysqli_close($conexion);

?>