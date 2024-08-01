<?php

include 'db.php';

if(isset($_GET['id_docente'])){
    $id=$_GET['id_docente'];
    $query ="DELETE FROM docente WHERE id_docente= $id";
    $result = mysqli_query($conexion,$query);
    if(!$result) {
        die("la consulta ha fallado");
    }
header("Location: docente.php");
}

?>