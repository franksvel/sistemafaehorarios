<?php
session_start();
$usuario = $_POST['usuario'];
$contrasena = md5($_POST['contra']); // Aquí se aplica la función md5() a la contraseña

include 'db.php';

$consulta = "SELECT * FROM usuarios WHERE usuario='$usuario' and contra='$contrasena'";
$resultado = mysqli_query($conexion, $consulta);

$filas = mysqli_fetch_array($resultado);

if ($filas) {
    $_SESSION['usuario'] = $usuario;
    header("location: principal.php");
} else {
    include("index.php");
    echo '<script>
    alert("El usuario y contraseña son incorrectos");
    </script>';
    echo '<h1 class="bad">Error en la autenticacion</h1>';
}

mysqli_free_result($resultado);
mysqli_close($conexion);
?>