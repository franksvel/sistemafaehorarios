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

    <title>Document</title>
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
<h5 class="text-center">Datos del Semestre y las Materias</h5>
<br>
<!-- Botón para abrir el modal -->
<div class="text-center">
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Agregar Materia
</button> 
<a href="generar.php" class="btn btn-primary">Regresar</a>
</div>
<br>


<div class="container">
    <div class="row">
        <div class="col">
        <table class="table">
    <thead>
      <tr>
        <!--<th>N° de Registro</th>-->
        <th class="text-center">Nombre de la materia</th>
        <th>Accciones</th>
      </tr>

    </thead>
    <tbody>
        <?php
        include 'db.php';
        $sql="SELECT * FROM materia";
        $result=mysqli_query($conexion,$sql);
        while($mostrar=mysqli_fetch_array($result)){

        ?>
        <tr>
            <!--<td><?php echo $mostrar ['id_materia']?></td>-->
            <td><?php echo $mostrar ['nombre_materia']?></td>
            <td>
            <a class="btn btn-danger" href="borrarmate.php?id_materia=<?php echo urlencode($mostrar['id_materia']); ?>">Borrar</a>
           
            </td>
        </tr>
    <?php
        }
    ?>
    </tbody>
  </table>
        </div>
  </table>
        </div>
    </div>
</div>




<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar Materia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form action="disponibilidad.php" method="POST">
    <div class="form-group">
      <label for="nombre">Materia:</label>
      <input type="text" class="form-control"placeholder="Ingrese la materia" required name="nombre_materia">
    </div>
    <div class="modal-footer">
    <input type="submit" class="btn btn-primary" value="Guardar">
    </div>
  </form>
  
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>


</body>
</html>