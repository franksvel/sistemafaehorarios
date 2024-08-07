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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        

        <h1 class="mt-4 d-flex"><i class="fa-solid fa-book m-2"></i>Materia</h1>
        <ul class="list-group mt-3">
        
        </ul>
    </div>
    <div class="container">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Agregar Materia
</button> 
    </div>


    <div class="container">
    <table class="table m-2 mt-4">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre(s)</th>
        <th>Accion</th>
      </tr>
    </thead>
    <tbody>
    <?php
        include 'db.php';
        $sql="SELECT * FROM asignatura ";
        $result=mysqli_query($conexion,$sql);
        while($mostrar=mysqli_fetch_array($result)){

        ?>
        <tr>
            <td><?php echo $mostrar ['id_registro']?></td>
            <td><?php echo $mostrar ['id_materia']?></td>
            <td><?php echo $mostrar ['id_semestre']?></td>
            <td>
            <a class="btn btn-danger" href="borrarmate.php?id_materia=<?php echo urlencode($mostrar['id_materia']); ?>"><i class="fa-sharp fa-solid fa-trash"></i></a>
            <a class="btn btn-warning" href="borrarmate.php?id_materia=<?php echo urlencode($mostrar['id_materia']); ?>"><i class="fa-sharp fa-solid fa-pencil"></i></a>
           
            </td>
        </tr>
    <?php
        }
    ?>
     
    </tbody>
  </table>
    </div>


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
      <form action="" id="formulario" method="POST">
    <div class="form-group">
      <label for="nombre">Materia*</label>
      <input type="text" id="nombre" class="form-control"placeholder="Ingrese la materia" required name="nombre">
      <small  class="form-text text-muted">Por favor, ingrese el nombre de la materia.</small>
    </div>
    <div class="form-group">
    <label for="semestre">Semestre</label>
                            <select name="id_semestre" id="semestre" class="form-control">
                                <?php
                                $query = "SELECT * FROM semestre ORDER BY id_semestre";
                                $result = mysqli_query($conexion, $query);
                                while ($row = mysqli_fetch_array($result)) {
                                    $id = $row['id_semestre'];
                                    $nombre = $row['nombre'];
                                    echo "<option value='$id'>$nombre</option>";

                                }
                                ?>
                            </select>
      
    </div>
    <div class="form-group">
    </div>
  </form>
  <input type="submit" id="mostrarAlerta" name="formulario" class="btn btn-primary" value="Guardar">

      </div>
      <div class="modal-footer">

      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
    // Manejar el evento de clic en el botón de guardar
    $('#mostrarAlerta').on('click', function(event) {
        event.preventDefault(); // Prevenir el comportamiento por defecto del formulario

        // Obtener el valor del input
        const nombre = $('#nombre').val();

        // Validar si el campo está vacío
        if (nombre.trim() === '') {
            Swal.fire({
                title: 'Error',
                text: 'El campo de nombre no puede estar vacío.',
                icon: 'error',
                confirmButtonText: 'Cerrar'
            });
            return; // Detener la ejecución si el campo está vacío
        }

        // Simular el guardado de datos
        guardarDatos(nombre).then(() => {
            // Mostrar alerta de éxito si los datos se guardaron
            Swal.fire({
                title: '¡Éxito!',
                text: 'Se guardaron los datos exitosamente.',
                icon: 'success',
                confirmButtonText: 'Cerrar'
            }).then(() => {
                // Mostrar mensaje después de cerrar el modal
                mostrarMensaje();
            });
        }).catch(() => {
            // Mostrar alerta de error si los datos no se guardaron
            Swal.fire({
                title: 'Error',
                text: 'Hubo un problema al guardar los datos. Intenta de nuevo.',
                icon: 'error',
                confirmButtonText: 'Cerrar'
            });
        });
    });

    // Función para simular el guardado de datos
    function guardarDatos(nombre) {
        return new Promise((resolve, reject) => {
            // Simulación de un proceso asincrónico (por ejemplo, una llamada AJAX)
            setTimeout(() => {
                const exitoso = true; // Cambiar a false para simular un error
                if (exitoso) {
                    console.log('Datos guardados:', nombre); // Mostrar en la consola
                    resolve(); // Datos guardados correctamente
                } else {
                    reject(); // Error al guardar los datos
                }
            }, 1000); // Tiempo simulado para el proceso de guardado
        });
    }

    // Función para mostrar un mensaje después de cerrar el modal
    function mostrarMensaje() {
        $('#mensaje').removeClass('d-none'); // Muestra el mensaje
    }
});
</script>

    
   
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
