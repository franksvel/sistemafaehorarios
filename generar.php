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
    // Asegúrate de que los nombres de las columnas sean correctos
    $consulta = "SELECT d.id_docente, d.dia, d.hora, d.color, p.nombre_d AS nombre
                 FROM disponibilidad_docente d
                 INNER JOIN docente p ON d.id_docente = p.id_docente";
    $resultado = $conexion->query($consulta);
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $dia = $fila['dia'];
            $hora = $fila['hora'];
            if (!isset($disponibilidad[$dia])) {
                $disponibilidad[$dia] = [];
            }
            $disponibilidad[$dia][$hora] = [
                'id_docente' => $fila['id_docente'],
                'nombre' => $fila['nombre'],
                'color' => $fila['color']
            ];
        }
    }
    return $disponibilidad;
}



// Obtener los días, horas y disponibilidad
$dias = obtenerDiasDeLaSemana($conexion);
$horas = obtenerHorasDisponibles($conexion);
$disponibilidad = obtenerDisponibilidad($conexion);

// Función para asignar materias aleatoriamente a los espacios seleccionados
function asignarMateriasAleatoriamente($conexion, $horas_seleccionadas, $dias_seleccionados) {
    // Obtener las materias disponibles
    $materias_query = "SELECT id_materia, nombre_materia FROM materia";
    $materias_result = $conexion->query($materias_query);
    $materias = [];
    while ($row = $materias_result->fetch_assoc()) {
        $materias[] = $row;
    }

    // Asignar materias aleatoriamente a los espacios seleccionados
    foreach ($dias_seleccionados as $dia) {
        foreach ($horas_seleccionadas as $hora) {
            $materia = $materias[array_rand($materias)];
            $id_materia = $materia['id_materia'];
            $insert_query = "INSERT INTO espacios (hora, dia, id_materia) VALUES (?, ?, ?)
                             ON DUPLICATE KEY UPDATE id_materia = VALUES(id_materia)";
            $stmt = $conexion->prepare($insert_query);
            $stmt->bind_param('sss', $hora, $dia, $id_materia);
            $stmt->execute();
        }
    }
}

function asignarMateria($conexion, $id_docente, $id_materia, $id_dia, $id_hora, $color) {
    // Verificar si ya existe una asignación en esa hora y día usando INNER JOIN
    $consulta = "
        SELECT g.id_docente, a.id_materia, a.dia, a.hora
        FROM general g
        INNER JOIN asignacion a ON g.id_docente = a.id_docente
        WHERE a.id_docente = ? AND a.dia = ? AND a.hora = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param('sss', $id_docente, $id_dia, $id_hora);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // Actualizar la asignación existente
        $consulta = "UPDATE asignacion SET id_materia = ?, color = ? WHERE id_docente = ? AND dia = ? AND hora = ?";
    } else {
        // Insertar una nueva asignación
        $consulta = "INSERT INTO asignacion (id_docente, id_materia, dia, hora, color) VALUES (?, ?, ?, ?, ?)";
    }

    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param('sssss', $id_materia, $color, $id_docente, $id_dia, $id_hora);
    $stmt->execute();
}
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
        .docente-cell::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: var(--cell-color, #FFFFFF);
            opacity: 0.2;
            z-index: 1;
        }
        .docente-cell {
            background-color: var(--cell-color, #FFFFFF);
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
                                data-id_docente="<?php echo htmlspecialchars($disponibilidad[$id_dia][$id_hora]['id_docente'] ?? ''); ?>"
                                data-dia="<?php echo htmlspecialchars($id_dia); ?>"
                                data-hora="<?php echo htmlspecialchars($id_hora); ?>"
                                class="draggable docente-cell"
                                draggable="true"
                                style="background-color: <?php echo htmlspecialchars($disponibilidad[$id_dia][$id_hora]['color'] ?? '#FFFFFF'); ?>;"
                            >
                                <?php echo htmlspecialchars($disponibilidad[$id_dia][$id_hora]['nombre'] ?? 'No asignado'); ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="modal fade" id="addSubjectModal" tabindex="-1" role="dialog" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSubjectModalLabel">Agregar Materia</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="guardar_materia.php" method="POST">
                            <input type="hidden" id="modal_id_docente" name="id_docente">
                            <input type="hidden" id="modal_id_dia" name="id_dia">
                            <input type="hidden" id="modal_id_hora" name="id_hora">
                            <div class="form-group">
                                <label for="id_materia">Selecciona la materia*</label>
                                <select name="id_materia" id="id_materia" class="form-control" required>
                                    <?php
                                    $query = "SELECT id_materia, nombre_materia FROM materia ORDER BY id_materia";
                                    $result = $conexion->query($query);
                                    while ($row = $result->fetch_assoc()) {
                                        $id = $row['id_materia'];
                                        $nombre = $row['nombre_materia'];
                                        echo "<option value='$id'>$nombre</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="color">Selecciona el color*</label>
                                <input type="color" id="color" name="color" class="form-control" value="#FFFFFF" required>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        let draggedElement = null;

        document.querySelectorAll('.draggable').forEach(element => {
            element.addEventListener('dragstart', (e) => {
                draggedElement = e.target;
                e.dataTransfer.effectAllowed = 'move';
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
                    const draggedContent = draggedElement.innerHTML;
                    const targetContent = e.target.innerHTML;
                    const draggedData = draggedElement.dataset;
                    const targetData = e.target.dataset;

                    e.target.innerHTML = draggedContent;
                    draggedElement.innerHTML = targetContent;

                    // Actualizar base de datos
                    actualizarAsignacion(draggedData, targetData);
                }
            });
        });

        function actualizarAsignacion(draggedData, targetData) {
            fetch('actualizar_asignacion.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_docente_origen: draggedData.id_docente,
                    dia_origen: draggedData.dia,
                    hora_origen: draggedData.hora,
                    id_docente_destino: targetData.id_docente,
                    dia_destino: targetData.dia,
                    hora_destino: targetData.hora
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Asignación actualizada exitosamente.');
                } else {
                    alert('Error al actualizar la asignación.');
                }
            });
        }
    });
    </script>
</body>
</html>
