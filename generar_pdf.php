<?php
// Incluye el autoload de Composer
require_once __DIR__ . '/../vendor/autoload.php'; // Cambia la ruta si es necesario

// Conectar a la base de datos (asegúrate de personalizar los parámetros)
$servername = "localhost"; // Cambia según tu configuración
$username = "root"; // Cambia según tu configuración
$password = ""; // Cambia según tu configuración
$dbname = "sistemafae"; // Cambia según tu configuración

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Función para obtener la disponibilidad de los docentes con filtros
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
    $consulta = "SELECT d.id_docente, d.id_dia, d.id_hora, m.id_materia, m.nombre_materia, h.nombre_hora, dia.nombre_dia, doc.nombre_d
                 FROM general d
                 INNER JOIN asignacion a ON d.id_docente = a.id_docente
                 INNER JOIN materia m ON a.id_materia = m.id_materia
                 INNER JOIN disponibilidad h ON d.id_hora = h.id_hora
                 INNER JOIN dia dia ON d.id_dia = dia.id_dia
                 INNER JOIN docente doc ON d.id_docente = doc.id_docente
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
                'nombre_materia' => $fila['nombre_materia'],
                'nombre_dia' => $fila['nombre_dia'],
                'nombre_hora' => $fila['nombre_hora'],
                'nombre_d' => $fila['nombre_d'], // Agregar nombre del docente
            ];
        }
    }

    return $disponibilidad;
}

// Obtener filtros desde la solicitud
$filtroMateria = isset($_GET['filtroMateria']) ? $_GET['filtroMateria'] : null;
$filtroDocente = isset($_GET['filtroDocente']) ? $_GET['filtroDocente'] : null;
$filtroCarrera = isset($_GET['filtroCarrera']) ? $_GET['filtroCarrera'] : null;
$filtroSemestre = isset($_GET['filtroSemestre']) ? $_GET['filtroSemestre'] : null;

// Obtener los datos de disponibilidad
$disponibilidad = obtenerDisponibilidad($conn, $filtroMateria, $filtroDocente, $filtroCarrera, $filtroSemestre);

// Crear un nuevo PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Horarios');
$pdf->SetHeaderData('', 0, 'Horario de Ingeniería', 'Generado el: ' . date('Y-m-d H:i:s'));
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();

// Crear contenido para el PDF
$html = '<h1>Horario Escolar</h1>';

// Agregar filtros al PDF
$html .= '<ul>';
if ($filtroMateria) {
    $html .= '<li><strong>Materia:</strong> ' . htmlspecialchars($filtroMateria) . '</li>';
}
if ($filtroDocente) {
    $html .= '<li><strong>Docente:</strong> ' . htmlspecialchars($filtroDocente) . '</li>';
}
if ($filtroCarrera) {
    $html .= '<li><strong>Carrera:</strong> ' . htmlspecialchars($filtroCarrera) . '</li>';
}
if ($filtroSemestre) {
    $html .= '<li><strong>Semestre:</strong> ' . htmlspecialchars($filtroSemestre) . '</li>';
}
$html .= '</ul>';

$html .= '<table border="1" cellpadding="5">';

// Encabezado de la tabla
$html .= '<thead>
            <tr>
                <th>Hora / Día</th>';
// Obtener y agregar encabezados de día
$dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes'];
foreach ($dias as $idDia => $nombreDia) {
    $html .= '<th>' . htmlspecialchars($nombreDia) . '</th>';
}
$html .= '</tr>
          </thead>
          <tbody>';

// Obtener horas de la base de datos
$consultaHoras = "SELECT * FROM disponibilidad"; // Asegúrate de que esta consulta sea la correcta para obtener las horas
$resultadoHoras = $conn->query($consultaHoras);
$horas = [];

if ($resultadoHoras && $resultadoHoras->num_rows > 0) {
    while ($fila = $resultadoHoras->fetch_assoc()) {
        $horas[$fila['id_hora']] = $fila['nombre_hora']; // Asumimos que 'nombre_hora' es el campo que contiene la representación de la hora
    }
}

// Agregar cada fila de datos a la tabla
foreach ($horas as $idHora => $nombreHora) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($nombreHora) . '</td>'; // Nombre de la hora

    foreach ($dias as $idDia => $nombreDia) {
        $html .= '<td>';
        if (isset($disponibilidad[$idDia][$idHora])) {
            foreach ($disponibilidad[$idDia][$idHora] as $materia) {
                $html .= htmlspecialchars($materia['nombre_materia']) . ' (' . htmlspecialchars($materia['nombre_d']) . ')<br>'; // Mostrar nombre del docente
            }
        } else {
            $html .= 'Sin Disponibilidad';
        }
        $html .= '</td>';
    }

    $html .= '</tr>';
}

$html .= '</tbody></table>';

// Escribir el contenido HTML en el PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Cerrar y generar el PDF
$pdf->Output('horarios_disponibilidad.pdf', 'D'); // 'D' para forzar la descarga

// Cerrar conexión
$conn->close();
?>
