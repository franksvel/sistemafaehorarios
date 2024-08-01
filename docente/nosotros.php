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

    <title>Nosotros</title>
</head>
<body>

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
<h1 class="text-center">¿Quienes somos Nosotros?</h1>
<br>
<br>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="nosotros">
            <p>FAE Infinity Schedules es una plataforma en línea que permite a sus usuarios crear, gestionar y visualizar horarios de manera intuitiva</p>
            <p>Interfaz Intuitiva y Amigable: La página web está diseñada con una interfaz fácil de usar, permitiendo que cualquier persona, independientemente de su nivel de habilidad tecnológica, pueda crear y gestionar horarios sin complicaciones.</p>
            <p>Acceso en Cualquier Momento y Lugar: Al ser una plataforma web, FAE Infinity Schedules es accesible desde cualquier dispositivo con conexión a internet, permitiendo a los usuarios revisar y actualizar sus horarios en cualquier momento y lugar.</p>
            <p>FAE Infinity Schedules es más que una simple herramienta de gestión de horarios; es tu aliado para alcanzar una organización y productividad óptimas en tu día a día.</p>
            </div>
        </div>
        <div class="col">
        <img src="logo.ico">
        </div>
    </div>
</div>












<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>




</body>
</html>