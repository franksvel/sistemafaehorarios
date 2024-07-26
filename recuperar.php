<?php
$usuario=$_POST['usuario'];
$newcontrasena=$_POST['contra'];
$newcontrasena1=$_POST['contrac'];

include 'db.php';

$sql = "UPDATE usuarios SET usuario='$usuario',contra='$newcontrasena',contrac=$newcontrasena1 WHERE $usuario";

if($conn->query($sql)=== TRUE){
    echo 'Se ha recuperado da contraseña';
}else {
    echo 'Error al recuperar la contraseña'. $conn->error;
}

?>