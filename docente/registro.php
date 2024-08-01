
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Registro de usuario</title>
</head>
<body>

<header>
  <style>
    #intro {
      background-image: url(descarga.jpg);
      height: 100vh;
      background-size:cover;
    }

    @media (min-width: 992px) {
      #intro {
        margin-top: -58.59px;
      }
    }

    .navbar .nav-link {
      color: #fff !important;
    }
  </style>

<div class="contenido-centrado">
  <div id="intro" class="bg-image shadow-2-strong">
    <div class="mask d-flex align-items-center h-100">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-xl-5 col-md-8">

            <form class="bg-white rounded shadow-5-strong p-5" action="registroform.php" method="POST">
              <!-- Email input -->
              <div class="form-outline mb-4" data-mdb-input-init>
              <h1>Registro de Usuario</h1>
                <input type="email" name="usuario" class="form-control" />
                <label class="form-label">Correo Electronico</label>
              </div>

              <!-- Password input -->
              <div class="form-outline mb-4" data-mdb-input-init>
                <input type="password" name="contra" class="form-control" />
                <label class="form-label">Ingresa contraseña</label>
              </div>

              <div class="form-outline mb-4" data-mdb-input-init>
                <input type="password" name="contrac" class="form-control" />
                <label class="form-label">Confirmar contraseña</label>
              </div>

              

              <!--botones de registro e inicio de sesion-->
              <input type="submit" class="btn btn-primary btn-block" value="Registrarse">
              <td></td>
              <a href="index.php" class="btn btn-primary btn-block" data-mdb-ripple-init>Iniciar Sesiòn</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

</body>
</html>