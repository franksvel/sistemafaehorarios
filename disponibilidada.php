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

include 'db.php';

// Definir el orden preferido de los días
$orden_dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

// Obtención de los datos
$sql = "SELECT g.id_docente, g.id_carrera, 
               d.nombre_d, d.apellido_p, d.apellido_m, 
               c.nombre_c, 
               di.nombre_dia, 
               h.nombre_hora
        FROM general g
        JOIN docente d ON g.id_docente = d.id_docente
        JOIN carrera c ON g.id_carrera = c.id_carrera
        JOIN dia di ON g.id_dia = di.id_dia
        JOIN disponibilidad h ON g.id_hora = h.id_hora
        ORDER BY d.nombre_d, FIELD(di.nombre_dia, '" . implode("','", $orden_dias) . "'), h.nombre_hora";

$result = mysqli_query($conexion, $sql);

// Agrupación de datos
$disponibilidad = [];
$dias_unicos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $docente = $row['nombre_d'] . ' ' . $row['apellido_p'] . ' ' . $row['apellido_m'];
    $carrera = $row['nombre_c'];
    $dia = $row['nombre_dia'];
    $hora = $row['nombre_hora'];

    if (!isset($disponibilidad[$docente])) {
        $disponibilidad[$docente] = [];
    }
    if (!isset($disponibilidad[$docente][$carrera])) {
        $disponibilidad[$docente][$carrera] = [];
    }
    if (!isset($disponibilidad[$docente][$carrera][$dia])) {
        $disponibilidad[$docente][$carrera][$dia] = [];
    }
    $disponibilidad[$docente][$carrera][$dia][] = $hora;

    // Agregar días únicos a la lista
    if (!in_array($dia, $dias_unicos)) {
        $dias_unicos[] = $dia;
    }
}

// Ordenar los días según el orden preferido
usort($dias_unicos, function($a, $b) use ($orden_dias) {
    $indexA = array_search($a, $orden_dias);
    $indexB = array_search($b, $orden_dias);
    return $indexA - $indexB;
});
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
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
        
        <h1 class="mt-4"><i class="fa-solid fa-briefcase m-2"></i>Disponibilidad</h1>
        <div class="container">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Agregar Disponibilidad</button> 
        </div>
        
        <div class="container mt-4">
            <table class="calendar-table">
                <thead>
                    <tr>
                        <th>Docente</th>
                        <th>Carrera</th>
                        <?php foreach ($dias_unicos as $dia): ?>
                            <th><?php echo htmlspecialchars($dia); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($disponibilidad as $docente => $carreras): ?>
                        <?php foreach ($carreras as $carrera => $dias): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($docente); ?></td>
                                <td><?php echo htmlspecialchars($carrera); ?></td>
                                <?php foreach ($dias_unicos as $dia): ?>
                                    <td>
                                        <?php
                                        // Mostrar las horas para el día específico
                                        if (isset($dias[$dia])) {
                                            echo implode(', ', $dias[$dia]);
                                        } else {
                                            echo "No disponible";
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Disponibilidad del docente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="guardardispo.php" method="POST">
                            <div class="form-group">
                                <label for="id_docente">Selecciona el nombre de docente*</label>
                                <select name="id_docente" id="id_docente" class="form-control" required>
                                    <?php
                                    $query = "SELECT * FROM docente ORDER BY id_docente";
                                    $result = mysqli_query($conexion, $query);
                                    while ($row = mysqli_fetch_array($result)) {
                                        $id = $row['id_docente'];
                                        $nombre = $row['nombre_d'];
                                        $apellido = $row['apellido_p'];
                                        $apellidom = $row['apellido_m'];
                                        echo "<option value='$id'>$nombre $apellido $apellidom</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_carrera">Carrera en la que labora</label>
                                <select name="id_carrera" id="id_carrera" class="form-control" required>
                                    <?php
                                    $query = "SELECT * FROM carrera ORDER BY id_carrera";
                                    $result = mysqli_query($conexion, $query);
                                    while ($row = mysqli_fetch_array($result)) {
                                        $id = $row['id_carrera'];
                                        $nombre = $row['nombre_c'];
                                        echo "<option value='$id'>$nombre</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Selecciona el día</label>
                                <div class="d-flex flex-wrap">
                                    <?php
                                    $query = "SELECT * FROM dia ORDER BY FIELD(nombre_dia, '" . implode("','", $orden_dias) . "')";
                                    $result = mysqli_query($conexion, $query);
                                    while ($row = mysqli_fetch_array($result)) {
                                        $id = $row['id_dia'];
                                        $nombre = $row['nombre_dia'];
                                        echo "<div class='mr-4 checkbox-item form-check'>";
                                        echo "<input class='form-check-input' type='checkbox' name='id_dia[]' value='$id' id='checkbox$id'>";
                                        echo "<label class='form-check-label' for='checkbox$id'>";
                                        echo "$nombre";
                                        echo "</label>";
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="id_hora">Selecciona las horas disponibles</label>
                                <div class="container d-flex flex-wrap">
                                    <?php
                                    $query = "SELECT * FROM disponibilidad ORDER BY id_hora";
                                    $result = mysqli_query($conexion, $query);
                                    while ($row = mysqli_fetch_array($result)) {
                                        $id = $row['id_hora'];
                                        $nombre = $row['nombre_hora'];
                                        echo "<div class='form-check mr-4'>";
                                        echo "<input class='form-check-input' type='checkbox' name='id_hora[]' value='$id' id='checkbox$id'>";
                                        echo "<label class='form-check-label' for='checkbox$id'>";
                                        echo "$nombre";
                                        echo "</label>";
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <input type="submit" class="btn btn-primary" value="Guardar">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
