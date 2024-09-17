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

$docentes_query = "SELECT * FROM docente ORDER BY id_docente";
$docentes_result = mysqli_query($conexion, $docentes_query);

$materias_query = "SELECT * FROM materia ORDER BY id_materia";
$materias_result = mysqli_query($conexion, $materias_query);

// Modificar consulta para obtener la información necesaria
$asignaciones_query = "SELECT a.id_docente, d.nombre_d, d.apellido_p, d.apellido_m, 
                       a.id_materia, m.nombre_materia
                       FROM asignacion a
                       JOIN docente d ON a.id_docente = d.id_docente
                       JOIN materia m ON a.id_materia = m.id_materia";
$asignaciones_result = mysqli_query($conexion, $asignaciones_query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        <h1 class="mt-4"><i class="fa-solid fa-clock m-2"></i>Horarios</h1>
    </div>

    <div class="container">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#asignar">Asignar Materia</button>
    </div>

    <!-- Modal para Asignar Materia -->
    <div class="modal fade" id="asignar" tabindex="-1" role="dialog" aria-labelledby="asignarmateria" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="asignarmateria">Asignar Materia al Docente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="asignarForm" action="asignar_materia.php" method="POST">
                        <div class="form-group">
                            <label for="id_docente">Selecciona el nombre de docente*</label>
                            <select name="id_docente" id="id_docente" class="form-control" required>
                                <?php
                                while ($row = mysqli_fetch_array($docentes_result)) {
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
                            <label for="id_materia">Selecciona la materia*</label>
                            <select name="id_materia" id="id_materia" class="form-control" required>
                                <?php
                                while ($row = mysqli_fetch_array($materias_result)) {
                                    $id = $row['id_materia'];
                                    $nombre = $row['nombre_materia'];
                                    echo "<option value='$id'>$nombre</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">Asignar Materia</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirmar Asignación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Docente:</strong> <span id="confirm-docente"></span></p>
                    <p><strong>Materia:</strong> <span id="confirm-materia"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirm-btn">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalles -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Detalles de la Asignación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Docente:</strong> <span id="modal-docente"></span></p>
                    <p><strong>Materia:</strong> <span id="modal-materia"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <table class="table m-2 mt-4">
            <thead>
                <tr>
                    <th>Docente</th>
                    <th>Materia</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_array($asignaciones_result)) {
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre_d'] . ' ' . $row['apellido_p'] . ' ' . $row['apellido_m']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_materia']); ?></td>
                        <td>
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#detailsModal"
                                    data-docente="<?php echo htmlspecialchars($row['nombre_d'] . ' ' . $row['apellido_p'] . ' ' . $row['apellido_m']); ?>"
                                    data-materia="<?php echo htmlspecialchars($row['nombre_materia']); ?>"
                                Ver Detalles
                            </button>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
            // Cuando se abre el modal de confirmación
            $('#confirmModal').on('show.bs.modal', function (event) {
                var form = $('#asignarForm');
                var docente = $('#id_docente option:selected').text();
                var materia = $('#id_materia option:selected').text();
                var color = $('#id_materia option:selected').data('color') || 'N/A';

                var modal = $(this);
                modal.find('#confirm-docente').text(docente);
                modal.find('#confirm-materia').text(materia);
                modal.find('#confirm-color').text(color);

                modal.find('#confirm-btn').off('click').on('click', function () {
                    form.submit();
                });
            });

            // Cuando se abre el modal de detalles
            $('#detailsModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var docente = button.data('docente');
                var materia = button.data('materia');
                var color = button.data('color');

                var modal = $(this);
                modal.find('#modal-docente').text(docente);
                modal.find('#modal-materia').text(materia);
                modal.find('#modal-color').text(color);
            });
        });
    </script>
</body>
</html>
