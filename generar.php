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

// Funciones para obtener los datos necesarios para los filtros
function obtenerMaterias($conexion) {
    $query = "SELECT id_materia, nombre_materia FROM materia ORDER BY nombre_materia";
    $result = $conexion->query($query);
    $materias = [];
    while ($row = $result->fetch_assoc()) {
        $materias[] = $row;
    }
    return $materias;
}

function obtenerDocentes($conexion) {
    $query = "SELECT id_docente, nombre_d FROM docente ORDER BY nombre_d";
    $result = $conexion->query($query);
    $docentes = [];
    while ($row = $result->fetch_assoc()) {
        $docentes[] = $row;
    }
    return $docentes;
}

function obtenerCarreras($conexion) {
    $query = "SELECT id_carrera, nombre_c FROM carrera ORDER BY nombre_c";
    $result = $conexion->query($query);
    $carreras = [];
    while ($row = $result->fetch_assoc()) {
        $carreras[] = $row;
    }
    return $carreras;
}

function obtenerSemestres($conexion) {
    $query = "SELECT DISTINCT id_semestre FROM general ORDER BY id_semestre";
    $result = $conexion->query($query);
    $semestres = [];
    while ($row = $result->fetch_assoc()) {
        $semestres[] = $row['id_semestre'];
    }
    return $semestres;
}

function obtenerDiasDeLaSemana($conexion) {
    $query = "SELECT id_dia, nombre_dia FROM dia ORDER BY id_dia";
    $result = $conexion->query($query);
    $dias = [];
    while ($row = $result->fetch_assoc()) {
        $dias[$row['id_dia']] = $row['nombre_dia'];
    }
    return $dias;
}

function obtenerHorasDisponibles($conexion) {
    $query = "SELECT id_hora, nombre_hora FROM disponibilidad ORDER BY id_hora";
    $result = $conexion->query($query);
    $horas = [];
    while ($row = $result->fetch_assoc()) {
        $horas[$row['id_hora']] = $row['nombre_hora'];
    }
    return $horas;
}
function obtenerDisponibilidad($conexion, $filtroMateria = null, $filtroDocente = null, $filtroCarrera = null, $filtroSemestre = null) {
    $disponibilidad = [];

    $conditions = [];
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
        $conditions[] = "d.semestre = " . intval($filtroSemestre);
    }

    $conditionString = implode(" AND ", $conditions);
    if ($conditionString) {
        $conditionString = "WHERE " . $conditionString;
    }

    // Obtener la disponibilidad y asignación del docente con las materias que puede impartir
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

            if (!isset($disponibilidad[$dia])) {
                $disponibilidad[$dia] = [];
            }
            if (!isset($disponibilidad[$dia][$hora])) {
                $disponibilidad[$dia][$hora] = [];
            }

            // Agregar todas las materias asignadas a esa hora y día
            $disponibilidad[$dia][$hora][] = [
                'id_docente' => $fila['id_docente'],
                'nombre_materia' => $fila['nombre_materia'],
            ];
        }

        // Hacer la asignación aleatoria para cada celda
        foreach ($disponibilidad as $dia => &$horas) {
            foreach ($horas as $hora => &$materias) {
                if (count($materias) > 0) {
                    // Selecciona una materia aleatoria de las asignadas
                    $materiaAleatoria = $materias[array_rand($materias)];
                    $horas[$hora] = [$materiaAleatoria]; // Sobrescribe con la materia seleccionada
                }
            }
        }
    }

    return $disponibilidad;
}



// Obtener los datos para los filtros
$materias = obtenerMaterias($conexion);
$docentes = obtenerDocentes($conexion);
$carreras = obtenerCarreras($conexion);
$semestres = obtenerSemestres($conexion);

$filtroMateria = isset($_GET['filtroMateria']) ? $_GET['filtroMateria'] : null;
$filtroDocente = isset($_GET['filtroDocente']) ? $_GET['filtroDocente'] : null;
$filtroCarrera = isset($_GET['filtroCarrera']) ? $_GET['filtroCarrera'] : null;
$filtroSemestre = isset($_GET['filtroSemestre']) ? $_GET['filtroSemestre'] : null;

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        td {
            vertical-align: top;
        }
        .calendar-table {
            width: 100%;
            border-collapse: collapse;
        }
        .calendar-table th, .calendar-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .calendar-table th {
            background-color: #f2f2f2;
        }
        .calendar-table td {
            height: 100px;
            vertical-align: top;
            cursor: pointer;
        }
        .docente-cell {
            position: relative;
            cursor: pointer;
        }
        .docente-cell div {
            padding: 2px;
            margin: 2px 0;
            color: #000;
            text-align: left;
            border-radius: 4px;
            cursor: move;
            background-color: #FFFFFF; /* Default background */
        }
        .draggable {
            display: inline-block;
        }
        .dragging {
            opacity: 0.5;
        }
        .droppable {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="vistad.php">Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
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
                                <?php echo $materia['nombre_materia']; ?>
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
                                <?php echo $docente['nombre_d']; ?>
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
                                <?php echo $carrera['nombre_c']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtroSemestre">Semestre</label>
                    <select name="filtroSemestre" id="filtroSemestre" class="form-control">
                        <option value="">Todos</option>
                        <?php foreach ($semestres as $semestre): ?>
                            <option value="<?php echo $semestre; ?>" <?php echo ($filtroSemestre == $semestre) ? 'selected' : ''; ?>>
                                <?php echo $semestre; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Generar Horarios</button>
            <button type="submit" name="refrescar" value="1" class="btn btn-secondary mt-3">Refrescar Tabla</button>
        </form>

        <table class="calendar-table">
    <thead>
        <tr>
            <th>Hora/Día</th>
            <?php foreach ($dias as $dia): ?>
                <th><?php echo $dia; ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($horas as $horaId => $horaNombre): ?>
            <tr>
                <td><?php echo $horaNombre; ?></td>
                <?php foreach ($dias as $diaId => $diaNombre): ?>
                    <td class="docente-cell" data-dia="<?php echo $diaId; ?>" data-hora="<?php echo $horaId; ?>">
                        <?php
                        if (isset($disponibilidad[$diaId][$horaId])) {
                            $materias = $disponibilidad[$diaId][$horaId];
                            $numMaterias = count($materias);
                            $visibleMaterias = array_slice($materias, 0, 3); // Muestra solo las primeras 3 materias
                            foreach ($visibleMaterias as $materia): ?>
                                <div class="draggable" draggable="true" style="background-color: <?php echo htmlspecialchars($materia['color']); ?>">
                                    <?php echo htmlspecialchars($materia['nombre_materia']); ?>
                                </div>
                            <?php endforeach;
                            if ($numMaterias > 3): ?>
                                <div class="more-materias" onclick="showMoreMaterias(this, <?php echo $horaId; ?>, <?php echo $diaId; ?>)">
                                    +<?php echo $numMaterias - 3; ?> más
                                </div>
                            <?php endif;
                        } ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const draggables = document.querySelectorAll('.draggable');
            const containers = document.querySelectorAll('.docente-cell');

            draggables.forEach(draggable => {
                draggable.addEventListener('dragstart', () => {
                    draggable.classList.add('dragging');
                });

                draggable.addEventListener('dragend', () => {
                    draggable.classList.remove('dragging');
                });
            });

            containers.forEach(container => {
                container.addEventListener('dragover', e => {
                    e.preventDefault();
                    const afterElement = getDragAfterElement(container, e.clientY);
                    const draggable = document.querySelector('.dragging');
                    if (afterElement == null) {
                        container.appendChild(draggable);
                    } else {
                        container.insertBefore(draggable, afterElement);
                    }
                    container.classList.add('droppable');
                });

                container.addEventListener('dragleave', () => {
                    container.classList.remove('droppable');
                });

                container.addEventListener('drop', () => {
                    container.classList.remove('droppable');
                });
            });

            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.draggable:not(.dragging)')];

                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return { offset: offset, element: child };
                    } else {
                        return closest;
                    }
                }, { offset: Number.NEGATIVE_INFINITY }).element;
            }
        });
    </script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>