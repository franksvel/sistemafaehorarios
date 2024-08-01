<?php
session_start();

// Verificar si se recibieron los datos del formulario
if (!isset($_POST['usuario']) || !isset($_POST['contra'])) {
    // Redirigir a la página de inicio si los datos del formulario no están presentes
    header("Location: index.php");
    exit();
}

$usuario = $_POST['usuario'];
$contrasena = md5($_POST['contra']); // Encriptar la contraseña usando md5()

include 'db.php';

// Escapar los datos de entrada para evitar inyecciones SQL
$usuario = mysqli_real_escape_string($conexion, $usuario);
$contrasena = mysqli_real_escape_string($conexion, $contrasena);

// Consulta para verificar el usuario y la contraseña
$consulta = "SELECT * FROM usuarios WHERE usuario='$usuario' AND contra='$contrasena'";
$resultado = mysqli_query($conexion, $consulta);

// Verificar si se obtuvo algún resultado
if (mysqli_num_rows($resultado) > 0) {
    // Iniciar la sesión y redirigir a dashboard.php
    $_SESSION['usuario'] = $usuario;
    header("Location: dashboard.php");
    exit();
} else {
    // Redirigir a la página de inicio con un mensaje de error
    $_SESSION['error'] = "El usuario y contraseña son incorrectos";
    header("Location: index.php");
    exit();
}

// Liberar resultados y cerrar la conexión
mysqli_free_result($resultado);
mysqli_close($conexion);
?>
