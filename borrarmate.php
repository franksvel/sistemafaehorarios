<?php

include 'db.php';

if(isset($_GET['id_materia'])){
    $id=$_GET['id_materia'];
    $query ="DELETE FROM materia WHERE id_materia= $id";
    $result = mysqli_query($conexion,$query);
    if(!$result) {
        die("la consulta ha fallado");
    }
header("Location: materia.php");
}

?>