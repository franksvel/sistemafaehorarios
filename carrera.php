<?php
require_once 'db.php';

// Obtén todas las carreras
$sql = "SELECT * FROM carrera";
$result = mysqli_query($conexion, $sql);

// Función para generar colores aleatorios
function generarColorAleatorio() {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}

// Actualiza colores si están vacíos
while ($mostrar = mysqli_fetch_array($result)) {
    if (empty($mostrar['color'])) {
        $color = generarColorAleatorio();
        $id_carrera = $mostrar['id_carrera'];
        $updateColorSql = "UPDATE carrera SET color = '$color' WHERE id_carrera = $id_carrera";
        mysqli_query($conexion, $updateColorSql);
    }
}

// Vuelve a obtener las carreras después de actualizar colores
$result = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrera</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .color-box {
            height: 30px;
            width: 30px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
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

        <h1 class="mt-4 d-flex"><i class="fa-solid fa-school m-2"></i>Carrera</h1>
    </div>
    <div class="container">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Agregar carrera</button> 
    </div>

    <div class="container">
        <table class="table m-2 mt-4">
            <thead>
                <tr>
                    <th>Nombre(s)</th>

                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            <?php
                while ($mostrar = mysqli_fetch_array($result)) {
                    $color = htmlspecialchars($mostrar['color']);
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($mostrar['nombre_c']); ?></td>
                    <!-- <td>
                        <span class="color-box" style="background-color: <?php echo $color; ?>;"></span>
                        <?php echo $color; ?>
                    </td> -->
                    <td>
                        <a class="btn btn-danger" href="borrarcarrera.php?id_carrera=<?php echo urlencode($mostrar['id_carrera']); ?>"><i class="fa-sharp fa-solid fa-trash"></i></a>
                        <button 
                            type="button" 
                            class="btn btn-primary btn-edit" 
                            data-toggle="modal" 
                            data-target="#exampleModal1"
                            data-id="<?php echo htmlspecialchars($mostrar['id_carrera']); ?>" 
                            data-nombre="<?php echo htmlspecialchars($mostrar['nombre_c']); ?>">
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

    <!-- Modal para agregar carrera -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar datos de la Carrera</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="guardarcarrera.php" method="POST">
                        <div class="form-group">
                            <label for="nombre">Carrera*</label>
                            <input type="text" id="nombre" class="form-control" placeholder="Ingrese la carrera" required name="nombre_c">
                            <small class="form-text text-muted">Por favor, ingrese el nombre de la Carrera.</small>
                        </div>
                        <input type="submit" id="mostrarAlerta" name="formulario" class="btn btn-primary" value="Guardar">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar carrera -->
    <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar datos de la Carrera</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="editarcarrera.php" method="POST">
                        <div class="form-group">
                            <label for="nombre">Carrera*</label>
                            <input type="text" id="nombre" class="form-control" placeholder="Ingrese la carrera" required name="nombre_c">
                            <small class="form-text text-muted">Por favor, ingrese el nombre de la carrera.</small>
                        </div>
                        <!-- Campo oculto para el ID -->
                        <input type="hidden" id="id_carrera" name="id_carrera">
                        <input type="submit" id="mostrarAlerta" name="formulario" class="btn btn-primary" value="Actualizar">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    // Convertir a mayúsculas mientras el usuario escribe
    document.getElementById('nombre').addEventListener('input', function() {
        // Cambia el valor del input a mayúsculas
        this.value = this.value.toUpperCase();
    });
</script>

    <script>
        $(document).ready(function() {
            $('.btn-edit').on('click', function() {
                var id_carrera = $(this).data('id');
                var nombre_carrera = $(this).data('nombre');

                // Asigna los valores a los campos del modal
                $('#exampleModal1 #nombre').val(nombre_carrera);
                $('#exampleModal1 #id_carrera').val(id_carrera);
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
