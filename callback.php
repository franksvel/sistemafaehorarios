<?php
require_once 'config.php';

if (isset($_GET['code'])) {
    // Intercambiar el código por un token de acceso
    $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();

    // Obtener los datos del perfil del usuario
    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();

    $usuario = $userInfo->email;
    $nombre = $userInfo->name;

    // Verificar si el usuario ya está en la base de datos
    include 'db.php';
    $usuario = mysqli_real_escape_string($conexion, $usuario);

    $consulta = "SELECT * FROM usuarios WHERE usuario='$usuario'";
    $resultado = mysqli_query($conexion, $consulta);

    if (mysqli_num_rows($resultado) > 0) {
        // Usuario ya existe, solo iniciar sesión
        $_SESSION['usuario'] = $usuario;
        header('Location: dashboard.php');
        exit();
    } else {
        // Nuevo usuario, registrar en la base de datos
        $consulta = "INSERT INTO usuarios (usuario, nombre, activo) VALUES ('$usuario', '$nombre', 1)";
        if (mysqli_query($conexion, $consulta)) {
            $_SESSION['usuario'] = $usuario;
            header('Location: dashboard.php');
            exit();
        } else {
            $_SESSION['error'] = "Error al registrar el usuario.";
            header('Location: index.php');
            exit();
        }
    }

    // Liberar resultados y cerrar la conexión
    mysqli_free_result($resultado);
    mysqli_close($conexion);
} else {
    $_SESSION['error'] = "No se pudo completar la autenticación.";
    header('Location: index.php');
    exit();
}
?>
