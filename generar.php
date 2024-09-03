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

// Funciones para obtener los días de la semana, las horas disponibles y la disponibilidad de los docentes
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
function obtenerDisponibilidad($conexion) {
    $disponibilidad = [];

    // Consulta SQL para obtener la disponibilidad combinada de las tablas general, docente, asignacion y materia
    $consulta = "SELECT d.id_docente, d.id_dia, d.id_hora, m.nombre_materia, m.color
                 FROM general d
                 INNER JOIN asignacion a ON d.id_docente = a.id_docente
                 INNER JOIN materia m ON a.id_materia = m.id_materia";

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
            // Añadir la disponibilidad a la hora correspondiente, incluyendo la materia asignada
            $disponibilidad[$dia][$hora][] = [
                'id_docente' => $fila['id_docente'],
                'nombre_materia' => $fila['nombre_materia'],
                'color' => $fila['color']
            ];
        }
    }

    return $disponibilidad;
}



$dias = obtenerDiasDeLaSemana($conexion);
$horas = obtenerHorasDisponibles($conexion);
$disponibilidad = obtenerDisponibilidad($conexion);
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
    </style>
</head>
<body>
    <div class="container mt-5">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="dashboard.php">Dashboard</a>
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

        <h1 class="mt-4"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-clock" width="80" height="80" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M10.5 21h-4.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3" />
            <path d="M16 3v4" />
            <path d="M8 3v4" />
            <path d="M4 11h10" />
            <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
            <path d="M18 16.5v1.5l.5 .5" />
        </svg> Generar Horarios</h1>

        <table class="calendar-table">
            <thead>
                <tr>
                    <th>Hora</th>
                    <?php foreach ($dias as $id_dia => $dia): ?>
                        <th><?php echo htmlspecialchars($dia); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($horas as $id_hora => $hora): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($hora); ?></td>
                        <?php foreach ($dias as $id_dia => $dia): ?>
                            <td 
                                data-toggle="modal" 
                                data-target="#addSubjectModal" 
                                data-id_docente="<?php echo htmlspecialchars($disponibilidad[$id_dia][$id_hora][0]['id_docente'] ?? ''); ?>"
                                data-dia="<?php echo htmlspecialchars($id_dia); ?>"
                                data-hora="<?php echo htmlspecialchars($id_hora); ?>"
                                class="docente-cell"
                            >
                                <?php 
                                $materias = $disponibilidad[$id_dia][$id_hora] ?? [];
                                shuffle($materias); // Aleatoriza el array de materias
                                foreach ($materias as $materia) {
                                    echo '<div class="draggable" draggable="true" style="background-color: ' . htmlspecialchars($materia['color']) . ';">' . htmlspecialchars($materia['nombre_materia']) . '</div>';
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

      

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
    document.addEventListener('DOMContentLoaded', () => {
        let draggedElement = null;

        document.querySelectorAll('.draggable').forEach(element => {
            element.addEventListener('dragstart', (e) => {
                draggedElement = e.target;
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', draggedElement.innerHTML); // Guarda el contenido en el DataTransfer
            });

            element.addEventListener('dragend', () => {
                draggedElement = null;
            });

            element.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            });

            element.addEventListener('drop', (e) => {
                e.preventDefault();
                if (draggedElement && draggedElement !== e.target) {
                    // Intercambia el contenido y el estilo del elemento
                    const target = e.target;

                    // Asegúrate de que el target sea un elemento con clase 'draggable' para evitar errores
                    if (target.classList.contains('draggable')) {
                        const draggedContent = draggedElement.innerHTML;
                        const draggedStyle = draggedElement.getAttribute('style');

                        // Intercambia el contenido y el estilo
                        draggedElement.innerHTML = target.innerHTML;
                        draggedElement.setAttribute('style', target.getAttribute('style'));

                        target.innerHTML = draggedContent;
                        target.setAttribute('style', draggedStyle);
                    }
                }
            });
        });

        document.querySelectorAll('.docente-cell').forEach(cell => {
            cell.addEventListener('click', (e) => {
                const target = e.target;
                const docenteId = target.dataset.id_docente || '';
                const dia = target.dataset.dia || '';
                const hora = target.dataset.hora || '';

                document.getElementById('hiddenIdDocente').value = docenteId;
                document.getElementById('hiddenDia').value = dia;
                document.getElementById('hiddenHora').value = hora;
            });
        });
    });
</script>

    </div>
</body>
</html>
