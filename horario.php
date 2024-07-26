<?php
session_start();
error_reporting(0);
$varsesion = $_SESSION['usuario'];

if ($varsesion == null || $varsesion == '') {
    echo '<script>alert("¡Ups!...Acceso denegado, debes iniciar sesión primero...");</script>';
    die();
}

include 'db.php'; // Incluir el archivo de conexión a la base de datos

function ejecutarConsulta($conexion, $query)
{
    $resultado = mysqli_query($conexion, $query);
    if (!$resultado) {
        echo "<script>alert('Error en la consulta: " . mysqli_error($conexion) . "');</script>";
        return false;
    }
    return $resultado;
}

function verificarHorario($conexion, $id_docente, $dias_seleccionados, $horas_seleccionadas)
{
    foreach ($dias_seleccionados as $dia) {
        foreach ($horas_seleccionadas as $hora) {
            $query = "SELECT * FROM horario WHERE id_docente = $id_docente AND id_dia = $dia AND id_hora = $hora";
            $resultado = ejecutarConsulta($conexion, $query);
            if (mysqli_num_rows($resultado) > 0) {
                return "¡Error! El docente ya tiene asignado un horario para el día y hora seleccionados.";
            }
        }
    }
    return ""; // Si no se encuentra ninguna coincidencia, retornar una cadena vacía
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_docente = $_POST['id_docente'];
    $id_semestre = $_POST['id_semestre'];
    $id_materia = $_POST['id_materia'];
    $dias_seleccionados = $_POST['id_dia'];
    $horas_seleccionadas = $_POST['id_hora'];

    $query_verificar = "SELECT * FROM horario WHERE id_docente = $id_docente AND id_semestre = $id_semestre AND id_materia = $id_materia";
    $result_verificar = ejecutarConsulta($conexion, $query_verificar);
    
    if (mysqli_num_rows($result_verificar) > 0) {
        echo "<script>alert('¡Error! Ya existen datos para este docente, semestre y materia.');</script>";
    } else {
        $alerta = verificarHorario($conexion, $id_docente, $dias_seleccionados, $horas_seleccionadas);
        
        if (!$alerta) {
            foreach ($dias_seleccionados as $id_dia) {
                foreach ($horas_seleccionadas as $id_hora) {
                    $query_insertar = "INSERT INTO general (id_docente, id_semestre, id_materia, id_dia, id_hora) VALUES ('$id_docente', '$id_semestre', '$id_materia', '$id_dia', '$id_hora')";
                    $resultado_insertar = ejecutarConsulta($conexion, $query_insertar);
                    
                    if ($resultado_insertar) {
                        echo "<script>alert('Datos guardados exitosamente para id_dia: $id_dia y id_hora: $id_hora');</script>";
                    } else {
                        echo "<script>alert('Error al guardar los datos para id_dia: $id_dia y id_hora: $id_hora - " . mysqli_error($conexion) . "');</script>";
                    }
                }
            }
            // Redirigir a la página de horario después de mostrar las alertas
            echo "<script>setTimeout(function(){ window.location.href = 'horario.php'; }, 1000);</script>";
        } else {
            echo "<script>alert('$alerta');</script>";
        }
    }
}

mysqli_close($conexion); // Cerrar la conexión a la base de datos
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <title>FAE INFINITY</title>
         <script>
        function validateForm() {
            var dayCheckboxes = document.querySelectorAll('input[name="id_dia[]"]');
            var hourCheckboxes = document.querySelectorAll('input[name="id_hora[]"]');

            var dayCheckedOne = Array.prototype.slice.call(dayCheckboxes).some(x => x.checked);
            var hourCheckedOne = Array.prototype.slice.call(hourCheckboxes).some(x => x.checked);

            if (!dayCheckedOne) {
                alert('Debe seleccionar al menos un día.');
                return false;
            }

            if (!hourCheckedOne) {
                alert('Debe seleccionar al menos una hora.');
                return false;
            }

            return true;
        }
    </script>

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

    <div class="text-center">
        <img src="clock-10378_512.gif" width="280" height="280" alt="">
    </div>

    <h5 class="text-center">Crear un horario escolar</h5>
    <div class="container">
        <div class="text-center">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                Crear horario
            </button>
            <a href="prueba1.php" class="btn btn-primary">Visualizar Horario</a><br><br>
            <a href="generar.php" class="btn btn-primary">Regresar</a>
        </div>
    </div>
    

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Horario escolar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="general.php" method="POST" onsubmit="return validateForm()">
                        <div class="form-group">
                            <label>Docente</label>
                            <select name="id_docente" id="id_docente" class="form-control">
                                <?php
                                include 'db.php';
                                $query = "SELECT * FROM docente ORDER BY id_docente";
                                $result = mysqli_query($conexion, $query);
                                while ($row = mysqli_fetch_array($result)) {
                                    $id = $row['id_docente'];
                                    $nombre = $row['nombre'];
                                    $apellido = $row['apellido'];
                                    echo "<option value='$id'>$nombre $apellido</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="semestre">Semestre</label>
                            <select name="id_semestre" id="semestre" class="form-control">
                                <?php
                                $query = "SELECT * FROM semestre ORDER BY id_semestre";
                                $result = mysqli_query($conexion, $query);
                                while ($row = mysqli_fetch_array($result)) {
                                    $id = $row['id_semestre'];
                                    $nombre = $row['nombre'];
                                    echo "<option value='$id'>$nombre</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="materia">Materia</label>
                            <select name="id_materia" id="materia" class="form-control">
                                <?php
                                $query = "SELECT * FROM materia ORDER BY id_materia";
                                $result = mysqli_query($conexion, $query);
                                while ($row = mysqli_fetch_array($result)) {
                                    $id = $row['id_materia'];
                                    $nombre = $row['nombre_materia'];
                                    echo "<option value='$id'>$nombre</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Selecciona el día</label>
                            <?php
                            $query = "SELECT * FROM dia ORDER BY id_dia";
                            $result = mysqli_query($conexion, $query);
                            while ($row = mysqli_fetch_array($result)) {
                                $id = $row['id_dia'];
                                $nombre = $row['nombre_dia'];
                                echo "<div><input type='checkbox' name='id_dia[]' value='$id'>$nombre</div>";
                            }
                            ?>
                        </div>
                        <div class="form-group">
                            <label>Selecciona las horas</label>
                            <?php
                            $query = "SELECT * FROM disponibilidad ORDER BY id_hora";
                            $result = mysqli_query($conexion, $query);
                            while ($row = mysqli_fetch_array($result)) {
                                $id = $row['id_hora'];
                                $nombre = $row['nombre_hora'];
                                echo "<div><input type='checkbox' name='id_hora[]' value='$id'>$nombre</div>";
                            }
                            ?>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Guardar">
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
