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

        <h1 class="mt-4 d-flex"><i class="fa-solid fa-chalkboard-user m-2"></i>Docentes</h1>
        <ul class="list-group mt-3"></ul>
    </div>

    <div class="container">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
            Agregar Docente
        </button>
    </div>

    <div class="container">
        <table class="table m-2 mt-4">
            <thead>
                <tr>
                    <th>Matricula</th>
                    <th>Nombre(s)</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'db.php';
                $sql = "SELECT * FROM docente";
                $result = mysqli_query($conexion, $sql);
                while($mostrar = mysqli_fetch_array($result)){
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($mostrar['matricula']); ?></td>
                    <td><?php echo htmlspecialchars($mostrar['nombre_d']); ?></td>
                    <td><?php echo htmlspecialchars($mostrar['apellido_p']); ?></td>
                    <td><?php echo htmlspecialchars($mostrar['apellido_m']); ?></td>
                    <td>
                        <a class="btn btn-danger" href="borrardocente.php?id_docente=<?php echo urlencode($mostrar['id_docente']); ?>">
                            <i class="fa-sharp fa-solid fa-trash"></i>
                        </a>
                        <button 
                            type="button" 
                            class="btn btn-primary btn-edit" 
                            data-toggle="modal" 
                            data-target="#exampleModal1"
                            data-id="<?php echo htmlspecialchars($mostrar['id_docente']); ?>" 
                            data-matricula="<?php echo htmlspecialchars($mostrar['matricula']); ?>"
                            data-nombre="<?php echo htmlspecialchars($mostrar['nombre_d']); ?>"
                            data-apellido_p="<?php echo htmlspecialchars($mostrar['apellido_p']); ?>"
                            data-apellido_m="<?php echo htmlspecialchars($mostrar['apellido_m']); ?>">
                            <i class="fa-sharp fa-solid fa-pencil"></i>
                        </button>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para agregar docente -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar datos del docente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="guardardocente.php" method="POST">
                        <div class="form-group">
                            <label for="matricula">Matricula*</label>
                            <input type="text" class="form-control" placeholder="Ingrese la Matricula" required name="matricula">
                            <small class="form-text text-muted">Por favor, ingrese la matricula del docente. Toma en cuenta que se toma el valor de Y desde un inicio</small>
                        </div>
                        <div class="form-group">
                            <label for="nombre_d">Nombre(s)</label>
                            <input type="text" class="form-control" placeholder="Ingresa los Nombre(s)" required name="nombre_d">
                        </div>
                        <div class="form-group">
                            <label for="apellido_p">Apellido Paterno</label>
                            <input type="text" class="form-control" placeholder="Apellido Paterno" required name="apellido_p">
                        </div>
                        <div class="form-group">
                            <label for="apellido_m">Apellido Materno</label>
                            <input type="text" class="form-control" placeholder="Apellido Materno" required name="apellido_m">
                        </div>
                        <input type="submit" class="btn btn-primary" value="Guardar">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar docente -->
    <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar datos del docente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="editardocente.php" method="POST">
                        <div class="form-group">
                            <label for="matricula">Matricula*</label>
                            <input type="text" class="form-control" id="matricula" name="matricula" placeholder="Ingrese la Matricula" required>
                        </div>
                        <div class="form-group">
                            <label for="nombre_d">Nombre(s)</label>
                            <input type="text" class="form-control" id="nombre_d" name="nombre_d" placeholder="Ingresa los Nombre(s)" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido_p">Apellido Paterno</label>
                            <input type="text" class="form-control" id="apellido_p" name="apellido_p" placeholder="Apellido Paterno" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido_m">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellido_m" name="apellido_m" placeholder="Apellido Materno" required>
                        </div>
                        <input type="hidden" id="id_docente" name="id_docente">
                        <input type="submit" class="btn btn-primary" value="Actualizar">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para convertir el texto a mayúsculas
        function convertToUppercase(event) {
            event.target.value = event.target.value.toUpperCase();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Añadir el eventListener a cada campo de entrada
            const inputs = document.querySelectorAll('input[type="text"]');
            inputs.forEach(input => {
                input.addEventListener('input', convertToUppercase);
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.btn-edit').on('click', function() {
                var id_docente = $(this).data('id');
                var matricula = $(this).data('matricula');
                var nombre_d = $(this).data('nombre');
                var apellido_p = $(this).data('apellido_p');
                var apellido_m = $(this).data('apellido_m');

                // Asigna los valores a los campos del modal
                $('#exampleModal1 #matricula').val(matricula);
                $('#exampleModal1 #nombre_d').val(nombre_d);
                $('#exampleModal1 #apellido_p').val(apellido_p);
                $('#exampleModal1 #apellido_m').val(apellido_m);
                $('#exampleModal1 #id_docente').val(id_docente);
            });
        });
    </script>
</body>
</html>
