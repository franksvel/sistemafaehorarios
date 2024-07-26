<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">

    <link rel="stylesheet" href="style.css">
    <title>Registro</title>
</head>
<body>

      <nav>
        <a href="principal.php">Inicio</a>
        <a href="#">Nosotros</a>
        <a href="portafolio.php">Portafolio</a>
        <a href="index.php">Cerrar Sesiòn</a>
      </nav>
<div class="nave">
    <img src="logo.ico">
</div>



<div>
<div id="intro1" class="bg-image shadow-2-strong">
    <div>
      <div class="container">
        <table>
        <div class="col modal">
          <div class="col-xl-8 col-md-8 modal-body">
            <div class="container">
    <table class="table">
   
      
    <?php
// Función para generar un horario escolar aleatorio
function generateSchedule($days, $hours, $db) {
  $schedule = array();

  // Obtener aulas disponibles de la base de datos
  $query_classrooms = "SELECT asignatura FROM docentes";
  $result_classrooms = $db->query($query_classrooms);
  $classrooms = array();
  while ($row = $result_classrooms->fetch_assoc()) {
      $classrooms[] = $row['asignatura'];
  }

  foreach ($days as $day) {
      $schedule[$day] = array();

      foreach ($hours as $hour) {
          // Definir aula específica según el día y la hora
          $classroom = '';

          // Si es martes o jueves y la hora es de 4 a 5 o de 5 a 6
          if (($day == '?') && ($hour == '4:00 a 5:00')) {
              $classroom = 'Aula específica para martes y jueves de 4 a 6';
          }

          // Asignar el aula al horario
          $schedule[$day][$hour] = array(
              "classroom" => $classroom
          );
      }
  }

  return $schedule;
}


// Días de la semana y horas del día
$days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
$hours = ['4:00 a 5:00', '5:00 a 6:00', '6:00 a 7:00', '7:00 a 8:00', '8:00 a 9:00', '9:00 a 10:00'];

// Valores predeterminados (puedes cambiar esto según tus necesidades)
$selectedDays = $days;
$selectedHours = $hours;

// Si se envió el formulario, actualizar los días y horas seleccionados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedDays = $_POST['days'] ?? [];
    $selectedHours = $_POST['hours'] ?? [];
    
    // Si se selecciona toda la columna para los días
    if (in_array("All", $selectedDays)) {
        $selectedDays = $days;
    }
    
    // Si se selecciona toda la columna para las horas
    if (in_array("All", $selectedHours)) {
        $selectedHours = $hours;
    }
}

// Conexión a la base de datos
$db = new mysqli("localhost", "feainfinity1091_feainfinity1091", "dm2Q&pN_Zd)2", "feainfinity1091_db");
if ($db->connect_error) {
    die("Error de conexión: " . $db->connect_error);
}

// Generar horario aleatorio
$schedule = generateSchedule($selectedDays, $selectedHours, $db);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horario Escolar</title>
    <!-- Bootstrap CSS -->
</head>
<body>

<div class="container">
    <h2 class="row justify-content-center">Horario Escolar</h2>
    <form method="post">
        <div class="form-group">
            <label for="days">Seleccionar días:</label>
            <select multiple class="form-control" id="days" name="days[]">
                <option value="All">Toda la columna</option>
                <?php foreach ($days as $day) : ?>
                    <option value="<?php echo $day; ?>" <?php if (in_array($day, $selectedDays)) echo "selected"; ?>><?php echo $day; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="hours">Seleccionar horas:</label>
            <select multiple class="form-control" id="hours" name="hours[]">
                <option value="All">Toda la columna</option>
                <?php foreach ($hours as $hour) : ?>
                    <option value="<?php echo $hour; ?>" <?php if (in_array($hour, $selectedHours)) echo "selected"; ?>><?php echo $hour; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Hora</th>
                    <?php foreach ($selectedDays as $day) : ?>
                        <th scope="col"><?php echo $day; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($selectedHours as $hour) : ?>
                    <tr>
                        <th scope="row"><?php echo $hour; ?></th>
                        <?php foreach ($selectedDays as $day) : ?>
                            <?php if (isset($schedule[$day][$hour])) : ?>
                                <td><?php echo $schedule[$day][$hour]['classroom']; ?></td>
                            <?php else : ?>
                                <td></td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Cerrar conexión a la base de datos
$db->close();
?>

</div>

</div>
<div class="d-grid gap-2 d-md-flex justify-content-md-center">
<input type="submit" class="btn btn-success" value="Imprimir" data-bs-toggle="modal" data-bs-target="#exampleModal">
<a href="prueba.php" class="btn btn-primary btn-block">Registrar Docente</a>
<a href="prueba3.php" class="btn btn-warning btn-block">Generar horario</a>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">¿Imprimir en PDF?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php
            // Ejemplo de contenido generado por PHP en el modal
            echo "¡Hola! Estas seguro de imprimir el horario?.";
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary">Imprimir</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>










      <footer class="bg-light text-lg-start">
  <div class="py-4 text-center">

  </div>
  <!-- Copyright -->
  <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-facebook" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" />
</svg>
<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-instagram" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 4m0 4a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4z" />
  <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
  <path d="M16.5 7.5l0 .01" />
</svg>
  </div>
  <!-- Copyright -->
</footer>
</body>
</html>