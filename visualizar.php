<?php
require_once 'vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setClientId('737255136278-udfv56p46c9u8tqo6l61kt251aodu28p.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/sistemafaehorarios/auth.php');
$client->addScope(Google_Service_Gmail::GMAIL_READONLY);

if (!isset($_SESSION['access_token']) || $_SESSION['access_token'] === null) {
    header('Location: http://localhost/sistemafaehorarios/index.php');
    exit();
}

$client->setAccessToken($_SESSION['access_token']);
$service = new Google_Service_Gmail($client);

// Incluir el archivo de conexión a la base de datos
include 'db.php';

// Función principal para obtener disponibilidad
function obtenerDisponibilidad($conexion, $filtroMateria = null, $filtroDocente = null, $filtroCarrera = null, $filtroSemestre = null) {
    $disponibilidad = [];
    $conditions = [];

    // Agregar condiciones a la consulta
    if ($filtroMateria) {
        $conditions[] = "m.id_materia = " . intval($filtroMateria);
    }
    if ($filtroDocente) {
        $conditions[] = "d.id_docente = " . intval($filtroDocente);
    }
    if ($filtroCarrera) {
        $conditions[] = "a.id_carrera = " . intval($filtroCarrera);
    }
    if ($filtroSemestre) {
        $conditions[] = "d.id_semestre = " . intval($filtroSemestre);
    }

    $conditionString = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

    // Obtener la disponibilidad y asignación del docente con las materias
    $consulta = "SELECT d.id_docente, d.id_dia, d.id_hora, m.id_materia, m.nombre_materia
                 FROM general d
                 INNER JOIN asignacion a ON d.id_docente = a.id_docente
                 INNER JOIN materia m ON a.id_materia = m.id_materia
                 $conditionString";

    $resultado = $conexion->query($consulta);

    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $dia = $fila['id_dia'];
            $hora = $fila['id_hora'];

            // Organizar disponibilidad por día y hora
            $disponibilidad[$dia][$hora][] = [
                'id_docente' => $fila['id_docente'],
                'nombre_materia' => $fila['nombre_materia'],
            ];
        }
    }

    // Aleatorizar las materias en cada celda
    foreach ($disponibilidad as &$dias) {
        foreach ($dias as &$horas) {
            shuffle($horas);
        }
    }

    return $disponibilidad;
}

// Funciones auxiliares
function obtenerMaterias($conexion) {
    $query = "SELECT id_materia, nombre_materia FROM materia ORDER BY nombre_materia";
    return $conexion->query($query)->fetch_all(MYSQLI_ASSOC);
}

function obtenerDocentes($conexion) {
    $query = "SELECT id_docente, nombre_d FROM docente ORDER BY nombre_d";
    return $conexion->query($query)->fetch_all(MYSQLI_ASSOC);
}

function obtenerCarreras($conexion) {
    $query = "SELECT id_carrera, nombre_c FROM carrera ORDER BY nombre_c";
    return $conexion->query($query)->fetch_all(MYSQLI_ASSOC);
}

function obtenerSemestres($conexion) {
    $query = "SELECT DISTINCT id_semestre FROM general ORDER BY id_semestre";
    $result = $conexion->query($query);
    return $result ? $result->fetch_all(MYSQLI_NUM) : [];
}

function obtenerDiasDeLaSemana($conexion) {
    $query = "SELECT id_dia, nombre_dia FROM dia ORDER BY id_dia";
    $result = $conexion->query($query);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function obtenerHorasDisponibles($conexion) {
    $query = "SELECT id_hora, nombre_hora FROM disponibilidad ORDER BY id_hora";
    return $conexion->query($query)->fetch_all(MYSQLI_ASSOC);
}

// Obtener los datos para los filtros
$materias = obtenerMaterias($conexion);
$docentes = obtenerDocentes($conexion);
$carreras = obtenerCarreras($conexion);
$semestres = obtenerSemestres($conexion);

$filtroMateria = $_GET['filtroMateria'] ?? null;
$filtroDocente = $_GET['filtroDocente'] ?? null;
$filtroCarrera = $_GET['filtroCarrera'] ?? null;
$filtroSemestre = $_GET['filtroSemestre'] ?? null;

$dias = obtenerDiasDeLaSemana($conexion);
$horas = obtenerHorasDisponibles($conexion);
$disponibilidad = obtenerDisponibilidad($conexion, $filtroMateria, $filtroDocente, $filtroCarrera, $filtroSemestre);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Horarios</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="vistad.php">Dashboard</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Menú
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="perfil.php">Perfil</a>
                            <a class="dropdown-item" href="configuracion.php">Configuración</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php">Cerrar sesión</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

        <h2>Generar Horarios</h2>
        <form method="get" class="mb-4">
            <div class="form-row">
                <div class="col-md-3">
                    <label for="filtroMateria">Materia</label>
                    <select name="filtroMateria" id="filtroMateria" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($materias as $materia): ?>
                            <option value="<?php echo $materia['id_materia']; ?>" <?php echo ($filtroMateria == $materia['id_materia']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($materia['nombre_materia']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroDocente">Docente</label>
                    <select name="filtroDocente" id="filtroDocente" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($docentes as $docente): ?>
                            <option value="<?php echo $docente['id_docente']; ?>" <?php echo ($filtroDocente == $docente['id_docente']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($docente['nombre_d']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroCarrera">Carrera</label>
                    <select name="filtroCarrera" id="filtroCarrera" class="form-control">
                        <option value="">Todas</option>
                        <?php foreach ($carreras as $carrera): ?>
                            <option value="<?php echo $carrera['id_carrera']; ?>" <?php echo ($filtroCarrera == $carrera['id_carrera']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($carrera['nombre_c']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroSemestre">Semestre</label>
                    <select name="filtroSemestre" id="filtroSemestre" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($semestres as $semestre): ?>
                            <option value="<?php echo $semestre[0]; ?>" <?php echo ($filtroSemestre == $semestre[0]) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($semestre[0]); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Filtrar</button>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Día / Hora</th>
                    <?php foreach ($horas as $hora): ?>
                        <th><?php echo htmlspecialchars($hora['nombre_hora']); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dias as $dia): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($dia['nombre_dia']); ?></td>
                        <?php foreach ($horas as $hora): ?>
                            <td>
                                <?php
                                if (isset($disponibilidad[$dia['id_dia']][$hora['id_hora']])) {
                                    foreach ($disponibilidad[$dia['id_dia']][$hora['id_hora']] as $materia) {
                                        echo htmlspecialchars($materia['nombre_materia']) . '<br>';
                                    }
                                } else {
                                    echo 'Sin Disponibilidad';
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <a href="generar_pdf.php?filtroMateria=<?php echo $filtroMateria; ?>&filtroDocente=<?php echo $filtroDocente; ?>&filtroCarrera=<?php echo $filtroCarrera; ?>&filtroSemestre=<?php echo $filtroSemestre; ?>" class="btn btn-success mt-3">Generar PDF</a>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
