<?php
include 'db.php';

$usuario = $_POST['usuario'];
$contrasena = md5($_POST['contra']); // Aplicación de md5() a la contraseña
$contrasena1 = md5($_POST['contrac']);

$consulta = "INSERT INTO usuarios(usuario, contra, contrac) VALUES ('$usuario','$contrasena','$contrasena1')";
$resultado = mysqli_query($conexion, $consulta);

if ($resultado) { // Cambiado de $filas a $resultado
	header("location: index.php");
} else {
	include("index.php");
	?>
	<h1 class="bad">Error en la autenticacion</h1>
	<?php
}

mysqli_close($conexion);
?>
