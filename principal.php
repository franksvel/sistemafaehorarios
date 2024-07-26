
<?php
session_start();
error_reporting(0);
$varsesion = $_SESSION['usuario'];

if($varsesion == null || $varsesion = '' ){
  echo '  <script>
alert("!Ups!...Acesso denegado debes de iniciar sesión primero...");
</script>';
  die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <title>FAE INFINITY</title>
</head>
<body>

      <!--<nav class="navbar navbar-expand-lg navbar-light bg-black">
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
          </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">>
                <li class="nav-item">
                    <a class="nav-link" href="principal.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="nosotros.php">Nosotros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" "generar.php">Generar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cerrar_sesion.php">Cerrar Sesiòn</a>
                </li>
        
                <div class="nav-item nave">
                    <img src="logo.ico" alt="">
                </div>
            </ul>
      </nav>-->
      
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
              <a class="nav-item nav-link" href="principal.php">Inicio</a>
              <a class="nav-item nav-link" href="nosotros.php">Nosotros</a>
              <a class="nav-item nav-link" href="generar.php">Generar</a>
              <a class="nav-item nav-link" href="cerrar_sesion.php">Cerrar Sesión</a>
            </div>
          </div>
        </nav>

<h1 class="text-center">Bienvenido a Fae Infinity Schedules</h1>
<br>
<br>
<div class="contenido-centrado">
<div class="container">
    <div class="row">
        <div class="col">
        <iframe src="https://giphy.com/embed/1oBwBVLGoLteCP2kyD" width="350" height="350" frameBorder="0" class="giphy-embed" allowFullScreen></iframe><p><a href="https://giphy.com/gifs/lazy-corgi-1oBwBVLGoLteCP2kyD">via GIPHY</a></p>
        </div>
        <div class="col">
          <div class="fondo">
          <p class="dato">
            ¡Estás en el lugar correcto! Con nuestra herramienta, crear horarios para tus actividades nunca ha sido tan fácil. Simplemente selecciona los días de la semana, elige los horarios disponibles, y listo. Nuestra interfaz intuitiva te guiará paso a paso para que puedas organizar tus actividades de la forma que desees. ¡No esperes más y comienza a planificar tus horarios de manera eficiente hoy mismo!
            </p>
          </div>
        </div>
    </div>
</div>






</div>









<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
</body>
</html>