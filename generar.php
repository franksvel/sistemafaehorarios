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

// Función principal para obtener disponibilidad con aleatorización
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
        $conditions[] = "d.id_semestre = " . intval($filtroSemestre);
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
    }

    // Aleatorizar las materias en cada celda
    foreach ($disponibilidad as &$dias) {
        foreach ($dias as &$horas) {
            shuffle($horas); // Mezclar aleatoriamente las materias
        }
    }

    return $disponibilidad;
}
// Funciones auxiliares
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

        <!-- <form method="get" class="mb-4">
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
            <button type="submit" class="btn btn-primary mt-3">Filtrar</button> -->
            <button type="submit" name="refrescar" value="1" class="btn btn-secondary mt-3">Generar Horarios</button>
        </form>


        <form action="guardar_materia_cel.php" method="POST">
    <table class="calendar-table">
        <thead>
            <tr>
                <th>Hora/Día</th>
                <?php foreach ($dias as $dia): ?>
                    <th><?php echo htmlspecialchars($dia); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($horas as $horaId => $horaNombre): ?>
            <tr>
                <td><?php echo htmlspecialchars($horaNombre); ?></td>
                <?php foreach ($dias as $diaId => $diaNombre): ?>
                    <td class="docente-cell" 
                        data-dia="<?php echo $diaId; ?>" 
                        data-hora="<?php echo $horaId; ?>" 
                        id="celda_<?php echo $diaId; ?>_<?php echo $horaId; ?>" 
                        onclick="openAgregarMateriaModal('<?php echo $horaId; ?>', '<?php echo $diaId; ?>')">
                        
                        <!-- Mostrar materias actuales aquí (si existen) -->
                        <?php if (isset($disponibilidad[$diaId][$horaId])): ?>
                            <?php foreach ($disponibilidad[$diaId][$horaId] as $materia): ?>
                                <div class="draggable" draggable="true">
                                    <?php echo htmlspecialchars($materia['nombre_materia']); ?>
                                    <!-- Input hidden que contendrá el valor de las materias -->
                                    <input type="hidden" name="materias[<?php echo $diaId; ?>][<?php echo $horaId; ?>][]" value="<?php echo htmlspecialchars($materia['nombre_materia']); ?>">
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    
    <input type="submit" class="btn btn-success" value="Guardar Horario">
</form>



<!-- Modal para agregar materia -->
<div class="modal fade" id="modalAgregarMateria" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar Materia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formAgregarMateria" action="guardar_materia_cel.php" method="post">
                    <p>Selecciona una materia:</p>
                    <select name="id_materia" id="selectMateria" class="form-control" required>
                        <?php foreach ($materias as $materia): ?>
                            <option value="<?php echo $materia['id_materia']; ?>">
                                <?php echo htmlspecialchars($materia['nombre_materia']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p id="materiaSeleccionada" style="margin-top: 10px;">Materia seleccionada: <strong></strong></p>
                    <input type="hidden" name="celda_id" id="celda_id" value="" />
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-warning" id="resetearMateria">Resetear</button>
                        <input type="submit" class="btn btn-primary" value="Guardar" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById("resetearMateria").addEventListener("click", function() {
    // Obtener el ID de la celda seleccionada
    var cellId = document.getElementById('celda_id').value;
    var diaId = cellId.split('_')[0];
    var horaId = cellId.split('_')[1];

    // Eliminar la materia de localStorage
    localStorage.removeItem(`materia_${diaId}_${horaId}`);

    // Restaurar la celda para mostrar todas las materias (si estaban previamente)
    var celda = document.getElementById(`celda_${cellId}`);
    if (celda) {
        // Cargar todas las materias seleccionadas previamente
        const selectMateria = document.getElementById('selectMateria');
        const options = selectMateria.querySelectorAll('option');
        let materias = '';
        
        // Agregar todas las materias al contenido de la celda
        options.forEach(option => {
            materias += `<div class="draggable">${option.text}</div>`;
        });
        
        // Actualizar la celda con todas las materias
        celda.innerHTML = materias;

        // Resaltar el cambio temporalmente
        celda.style.transition = 'background-color 0.5s ease';
        celda.style.backgroundColor = '#FFAAAA'; // Color de fondo rojo claro para resaltar el reset
        setTimeout(() => {
            celda.style.backgroundColor = '';
        }, 500);
    }

    // Cerrar el modal después de resetear
    $('#modalAgregarMateria').modal('hide');
});

</script>

<script>
    function openAgregarMateriaModal(horaId, diaId) {
        // Establecer el valor del campo oculto con el ID de la celda
        document.getElementById('celda_id').value = `${diaId}_${horaId}`;
        
        // Mostrar la materia seleccionada en el párrafo correspondiente
        const selectMateria = document.getElementById('selectMateria');
        const materiaSeleccionada = document.getElementById('materiaSeleccionada').querySelector('strong');

        // Cargar la materia seleccionada desde localStorage
        const storedMateriaId = localStorage.getItem(`materia_${diaId}_${horaId}`);
        if (storedMateriaId) {
            selectMateria.value = storedMateriaId;
            materiaSeleccionada.textContent = selectMateria.options[selectMateria.selectedIndex].text;
        } else {
            materiaSeleccionada.textContent = '';
        }

        selectMateria.addEventListener('change', function () {
            const selectedText = selectMateria.options[selectMateria.selectedIndex].text;
            materiaSeleccionada.textContent = selectedText;
            // Guardar la selección en localStorage
            localStorage.setItem(`materia_${diaId}_${horaId}`, selectMateria.value);
        });
        220
        // Mostrar el modal
        $('#modalAgregarMateria').modal('show');
    }

    // Actualizar celda al enviar el formulario
    document.getElementById("formAgregarMateria").addEventListener("submit", function(event) {
        event.preventDefault(); // Previene el envío estándar del formulario

        // Obtener el ID de la celda desde el campo oculto
        var cellId = document.getElementById('celda_id').value;
        var selectedMateriaId = document.getElementById("selectMateria").value;
        var selectedText = document.getElementById("selectMateria").options[document.getElementById("selectMateria").selectedIndex].text;

        // Actualizar la celda correspondiente
        var celda = document.getElementById(`celda_${cellId}`);
        if (celda) {
            // Limpiar el contenido de la celda antes de agregar la nueva materia
            celda.innerHTML = `<div class="draggable">${selectedText}</div>`;
            
            // Opcional: resaltar la celda para notar el cambio
            celda.style.transition = 'background-color 0.5s ease';
            celda.style.backgroundColor = '#FFFF00'; // Color de fondo amarillo
            setTimeout(() => {
                celda.style.backgroundColor = ''; // Restaurar color original
            }, 500); // Duración del parpadeo
        }

        // Cerrar el modal
        $('#modalAgregarMateria').modal('hide');

        // Enviar la materia seleccionada al servidor para que se guarde en la base de datos
        var formData = new FormData(this);
        formData.append('cell_id', cellId); // Agregar el ID de la celda

        fetch('guardar_materia_cel.php', { // Cambia esto por la ruta a tu script de servidor
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Manejo de la respuesta del servidor
            if (data.success) {
                console.log('Materia guardada con éxito.');
            } else {
                console.error('Error al guardar la materia:', data.message);
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
        });
    });
</script>

<script>
    // Función para cargar las materias guardadas en cada celda al cargar la página
function cargarMateriasGuardadas() {
    const celdas = document.querySelectorAll('td'); // Asume que tus celdas son <td>
    
    celdas.forEach(celda => {
        const cellId = celda.id.replace('celda_', ''); // Obtener ID de la celda (formato diaId_horaId)
        
        const storedMateriaId = localStorage.getItem(`materia_${cellId}`);
        if (storedMateriaId) {
            const selectMateria = document.getElementById('selectMateria'); // Asume que ya tienes un select de materias
            const selectedText = selectMateria.querySelector(`option[value="${storedMateriaId}"]`).text;
            
            celda.innerHTML = `<div class="draggable">${selectedText}</div>`;
        }
    });
}

// Llamar a la función cuando se cargue la página
document.addEventListener('DOMContentLoaded', cargarMateriasGuardadas);

</script>

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