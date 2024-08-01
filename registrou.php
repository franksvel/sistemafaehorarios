<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card w-100" style="max-width: 400px;">
            <div class="card-header text-center">
                <h4>Registro de Usuario</h4>
            </div>
            <div class="card-body">
                <?php
                session_start();
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
                if (isset($_SESSION['success'])) {
                    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                    unset($_SESSION['success']);
                }
                ?>
                <form action="registrar.php" method="POST">
                    <div class="form-group">
                        <label for="usuario">Correo Electrónico</label>
                        <input type="email" class="form-control" name="usuario" placeholder="Introduce tu correo electrónico" required>
                    </div>
                    <div class="form-group">
                        <label for="contra">Contraseña</label>
                        <input type="password" class="form-control" name="contra" placeholder="Introduce tu contraseña" required>
                    </div>
                    <input class="btn btn-primary btn-block" type="submit" value="Registrarse">
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
