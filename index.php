<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Inicio de Sesion</title>
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


  
  <div id="intro" class="bg-image shadow-2-strong">
    <div class="mask d-flex align-items-center h-100">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-xl-5 col-md-8">
            <form class="bg-white rounded shadow-5-strong p-5" action="validar.php" method="POST">
              <!-- Email input -->
              <div class="form-outline mb-4" data-mdb-input-init>
                <p></p>
                <h1 class="text-align-center">Inicio de Sesion FAE</h1>
                <input type="email" name="usuario" class="form-control" required>
                <label class="form-label">Correo Electronico</label>
              </div>

              <!-- Password input -->
              <div class="form-outline mb-4" data-mdb-input-init>
                <input type="password" name="contra" class="form-control" required>
                <label class="form-label">contraseña</label>
              </div>

       
              <!-- Submit button -->
              <a href="registro.php" class="btn btn-primary btn-block" data-mdb-ripple-init>Registrarse</a>
              <td></td>
              <td></td>
              <input type="submit" class="btn btn-primary btn-block" value="Iniciar Sesión">
            </form>
            <p></p>              
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

</body>
</html>