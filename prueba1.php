<?php
session_start();
include 'db.php';

// Verificar si el usuario ha iniciado sesión
$varsesion = $_SESSION['usuario'];
if ($varsesion == null || $varsesion == '') {
    echo '<script>
    alert("¡Ups!...Acceso denegado, debes iniciar sesión primero...");
    window.location.href = "index.php"; // Redireccionar a la página de inicio de sesión
    </script>';
    die();
}

// Variables para los filtros
$selectedDocente = isset($_POST['docente']) ? $_POST['docente'] : '';
$selectedSemestre = isset($_POST['semestre']) ? $_POST['semestre'] : '';
$selectedMateria = isset($_POST['materia']) ? $_POST['materia'] : '';

// Consultar las horas únicas para cada semestre
/*$query = "SELECT DISTINCT h.nombre_hora
          FROM general g
          JOIN semestre s ON g.id_semestre = s.id_semestre
          JOIN disponibilidad h ON g.id_hora = h.id_hora
          WHERE 1";*/
          
$query = "SELECT DISTINCT h.nombre_hora
          FROM general g
          JOIN semestre s ON g.id_semestre = s.id_semestre
          JOIN disponibilidad h ON g.id_hora = h.id_hora
          WHERE 1";

if ($selectedSemestre) {
    $query .= " AND g.id_semestre = '$selectedSemestre'";
}
if ($selectedMateria) {
    $query .= " AND g.id_materia = '$selectedMateria'";
}
// Ordenar las horas
$query .= " ORDER BY TIME(h.nombre_hora)";

$result = mysqli_query($conexion, $query);
if (!$result) {
    die("Error al obtener las horas: " . mysqli_error($conexion));
}

// Inicializar arrays para los datos
$horarios = [];
$dias = ["Lunes", "Martes", "Miercoles", "Jueves", "Viernes"];
$horas = [];

// Estructurar los datos en un array multidimensional
while ($row = mysqli_fetch_assoc($result)) {
    $horas[] = $row['nombre_hora'];
}

// Consultar los datos de la tabla general con los filtros aplicados
$query = "SELECT d.nombre AS nombre_docente, d.apellido AS apellido_docente, 
                 s.nombre AS nombre_semestre, m.nombre_materia, 
                 di.nombre_dia, h.nombre_hora 
          FROM general g
          JOIN docente d ON g.id_docente = d.id_docente
          JOIN semestre s ON g.id_semestre = s.id_semestre
          JOIN materia m ON g.id_materia = m.id_materia
          JOIN dia di ON g.id_dia = di.id_dia
          JOIN disponibilidad h ON g.id_hora = h.id_hora
          WHERE 1";

if ($selectedDocente) {
    $query .= " AND g.id_docente = '$selectedDocente'";
}
if ($selectedSemestre) {
    $query .= " AND g.id_semestre = '$selectedSemestre'";
}
if ($selectedMateria) {
    $query .= " AND g.id_materia = '$selectedMateria'";
}

// Ordenar por nombre_hora para asegurar que las horas estén en orden numérico
$query .= " ORDER BY TIME(h.nombre_hora)";

$result = mysqli_query($conexion, $query);
if (!$result) {
    die("Error al obtener los horarios: " . mysqli_error($conexion));
}

// Estructurar los datos en un array multidimensional
while ($row = mysqli_fetch_assoc($result)) {
    $horarios[$row['nombre_hora']][$row['nombre_dia']] = [
        'docente' => $row['nombre_docente'] . " " . $row['apellido_docente'],
        'materia' => $row['nombre_materia'],
        'semestre' => $row['nombre_semestre']
    ];
}

// Verificar si se ha solicitado la generación de PDF
if (isset($_POST['generar_pdf'])) {
    require('fpdf/fpdf.php');

    class PDF extends FPDF
    {
        // Cabecera de página
        function Header()
        {
            // Logo
            
            // Título
          
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(0, 10, 'Horario Escolar de Ingenieria en Computacion', 0, 1, 'B');
            // Salto de línea
            $this            ->Ln(10);
            
        }

        // Pie de página
        function Footer()
        {
            // Posición a 1.5 cm del final
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial', 'I', 8);
            // Número de página
            $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }
    }

    // Crear instancia de PDF
    $pdf = new PDF();
    $pdf->AliasNbPages();

    // Añadir página
    $pdf->AddPage();

    // Contenido del PDF (tabla de horarios)
    $pdf->SetFont('Arial', 'B', 12);

    // Agregar encabezados de la tabla
    $pdf->Cell(30, 10, 'Hora', 1);
    foreach ($dias as $dia) {
        $pdf->Cell(30, 10, $dia, 1);
    }
    $pdf->Ln();

    // Agregar los datos de los horarios al PDF
    $pdf->SetFont('Arial', 'B', 4);
    foreach ($horas as $hora) {
        $pdf->Cell(30, 20, $hora, 1);
        foreach ($dias as $dia) {
            if (isset($horarios[$hora][$dia])) {
                $datos = $horarios[$hora][$dia];
                // $texto = "{$datos['materia']}\n{$datos['semestre']}\n{$datos['docente']}";
                $texto = "{$datos['materia']}\n- {$datos['semestre']}\n";
                $x = $pdf->GetX();
                $y = $pdf->GetY();
    
                // Establecer una MultiCell
                $pdf->MultiCell(30, 10, $texto, 1);
                $pdf->SetXY($x + 30, $y);
                
                // $pdf->Cell(30, 20, $texto, 1);
            } else {
                // $pdf->Cell(30, 20, '', 1);
                $pdf->Cell(30, 20, '', 1);
            }
            
        }
        $pdf->Ln(20);
    }

    // Salida del PDF
    $pdf->Output();
    exit;
}
?>



                

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <title>Mostrar Horario</title>
</head>
<body>
<?php
    /*if (isset($_SESSION['alert'])) {
        echo '<script>alert("' . $_SESSION['alert'] . '")</script>';
        // Unset the alert variable to prevent the alert from showing again on page refresh
        unset($_SESSION['alert']);
    }*/
    ?>
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
    <div class="contenido-centrado">
        <div class="container">
            <h1 class="text-center">Horario Escolar</h1>
            <form action="prueba1.php" method="POST">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="docente">Docente</label>
                        <select name="docente" id="docente" class="form-control">
                            <option value="">Todos</option>
                            <?php
                            $docenteQuery = "SELECT id_docente, nombre, apellido FROM docente ORDER BY nombre";
                            $docenteResult = mysqli_query($conexion, $docenteQuery);
                            while ($docenteRow = mysqli_fetch_assoc($docenteResult)) {
                                $selected = $docenteRow['id_docente'] == $selectedDocente ? 'selected' : '';
                                echo "<option value='{$docenteRow['id_docente']}' $selected>{$docenteRow['nombre']} {$docenteRow['apellido']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="semestre">Semestre</label>
                        <select name="semestre" id="semestre" class="form-control">
                            <option value="">Todos</option>
                            <?php
                            $semestreQuery = "SELECT id_semestre, nombre FROM semestre ORDER BY nombre";
                            $semestreResult = mysqli_query($conexion, $semestreQuery);
                            while ($semestreRow = mysqli_fetch_assoc($semestreResult)) {
                                $selected = $semestreRow['id_semestre'] == $selectedSemestre ? 'selected' : '';
                                echo "<option value='{$semestreRow['id_semestre']}' $selected>{$semestreRow['nombre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="materia">Materia</label>
                        <select name="materia" id="materia" class="form-control">
                            <option value="">Todas</option>
                            <?php
                            $materiaQuery = "SELECT id_materia, nombre_materia FROM materia ORDER BY nombre_materia";
                            $materiaResult = mysqli_query($conexion, $materiaQuery);
                            while ($materiaRow = mysqli_fetch_assoc($materiaResult)) {
                                $selected = $materiaRow['id_materia'] == $selectedMateria ? 'selected' : '';
                                echo "<option value='{$materiaRow['id_materia']}' $selected>{$materiaRow['nombre_materia']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" name="filtrar">Filtrar</button>
                <button type="submit" class="btn btn-success" name="generar_pdf" formtarget="_blank">Generar PDF</button>
                <a href="horario.php" class="btn btn-warning">Regresar</a>
            </form>
            <table class="table table-striped mt-4">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <?php foreach ($dias as $dia) {
                            echo "<th>$dia</th>";
                        } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($horas as $hora) {
                        echo "<tr>";
                        echo "<td>$hora</td>";
                        foreach ($dias as $dia) {
                            if (isset($horarios[$hora][$dia])) {
                                $datos = $horarios[$hora][$dia];
                                echo "<td>{$datos['docente']}<br>{$datos['materia']}<br>{$datos['semestre']}</td>";
                            } else {
                                echo "<td></td>";
                            }
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>

</body>
</html>
