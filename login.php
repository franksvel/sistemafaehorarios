<?php
session_start();

include 'db.php';

if (!isset($_POST['usuario']) || !isset($_POST['contra'])) {
    header("Location: dashboard.php");
    exit();
}

$usuario = $_POST['usuario'];
$contrasena = $_POST['contra'];

$usuario = mysqli_real_escape_string($conexion, $usuario);

$consulta = "SELECT * FROM usuarios WHERE usuario='$usuario'";
$resultado = mysqli_query($conexion, $consulta);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    $fila = mysqli_fetch_assoc($resultado);
    
    if (password_verify($contrasena, $fila['contra'])) {
        $_SESSION['usuario'] = $usuario;
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Contraseña incorrecta.";
    }
} else {
    $_SESSION['error'] = "El usuario no existe.";
}

header("Location: index.php");
exit();
