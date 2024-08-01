<?php
session_start();
include 'db.php';

if (!isset($_POST['usuario']) || !isset($_POST['contra'])) {
    $_SESSION['error'] = "Por favor, completa todos los campos.";
    header("Location: registrou.php");
    exit();
}

$usuario = $_POST['usuario'];
$contrasena = password_hash($_POST['contra'], PASSWORD_BCRYPT);

// Escapar los datos de entrada
$usuario = mysqli_real_escape_string($conexion, $usuario);

// Verificar si el usuario ya existe
$consulta = "SELECT * FROM usuarios WHERE usuario='$usuario'";
$resultado = mysqli_query($conexion, $consulta);
if (mysqli_num_rows($resultado) > 0) {
    $_SESSION['error'] = "El correo electrónico ya está registrado.";
    header("Location: registrou.php");
    exit();
}

// Generar un token de verificación único
$token_verificacion = bin2hex(random_bytes(16));

// Insertar el nuevo usuario en la base de datos
$consulta = "INSERT INTO usuarios (usuario, contra, token_verificacion, activo) VALUES ('$usuario', '$contrasena', '$token_verificacion', 0)";
if (mysqli_query($conexion, $consulta)) {
    // Enviar correo de verificación
    $asunto = "Verificación de Cuenta";
    $mensaje = "Por favor, haz clic en el siguiente enlace para verificar tu cuenta: ";
    $mensaje .= "http://localhost/sistemafaehorarios/auth.php?token=" . $token_verificacion;
    
    // Enviar correo
    $headers = "From: no-reply@tu-dominio.com\r\n";
    $headers .= "Reply-To: no-reply@tu-dominio.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    if (mail($usuario, $asunto, $mensaje, $headers)) {
        $_SESSION['success'] = "Registro exitoso. Por favor, verifica tu correo electrónico.";
        header("Location: registrou.php");
        exit();
    } else {
        $_SESSION['error'] = "Error al enviar el correo de verificación. Inténtalo de nuevo.";
        header("Location: registrou.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Error al registrar el usuario. Inténtalo de nuevo.";
    header("Location: registrou.php");
    exit();
}

mysqli_close($conexion);
?>
