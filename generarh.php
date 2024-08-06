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

/*function horaRepetida($conexion, $obj) {
    $query = "SELECT * FROM general WHERE id_dia = ".$obj['dia']." AND id_hora = ".$obj['hora'];
    $res = mysqli_query($conexion, $query);

    if ( $res->num_rows > 0 ) {
        return true;
    } else {
        return false;
    }
}
*/

function getDocenteName($conexion, $id_docente) {
    $query = "SELECT nombre FROM docente WHERE id_docente = $id_docente";
    $res = mysqli_query($conexion, $query);
    if ($row = mysqli_fetch_assoc($res)) {
        return $row['nombre_docente'];
    }
    return null;
}

function getDiaName($conexion, $id_dia) {
    $query = "SELECT nombre_dia FROM dia WHERE id_dia = $id_dia";
    $res = mysqli_query($conexion, $query);
    if ($row = mysqli_fetch_assoc($res)) {
        return $row['nombre_dia'];
    }
    return null;
}

function getHoraInicio($conexion, $id_hora) {
    $query = "SELECT nombre_hora FROM disponibilidad WHERE id_hora = $id_hora";
    $res = mysqli_query($conexion, $query);
    if ($row = mysqli_fetch_assoc($res)) {
        return $row['nombre_hora'];
    }
    return null;
}

function profesorOcupado($conexion, $obj) { 
    $query = "SELECT * FROM general WHERE id_docente = ".$obj['docente']." AND id_hora = ".$obj['hora']." AND id_dia = ".$obj['dia'];
    $res = mysqli_query($conexion, $query);

    if ($res->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function verificarMateria($conexion, $obj) {
    $query = "SELECT * FROM general WHERE id_dia = ".$obj['dia']." AND id_hora = ".$obj['hora']." AND id_semestre = ".$obj['semestre'];
    $res = mysqli_query($conexion, $query);

    if ($res->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_docente = $_POST['id_docente'];
    $id_semestre = $_POST['id_semestre'];
    $id_materia = $_POST['id_materia'];
    $id_dias = $_POST['id_dia']; 
    $id_horas = $_POST['id_hora'];

    // Obtener nombres
    $nombre_docente = getDocenteName($conexion, $id_docente);
    $errores = [];

    foreach ($id_dias as $id_dia) {
        $nombre_dia = getDiaName($conexion, $id_dia);
        foreach ($id_horas as $id_hora) {
            $hora_inicio = getHoraInicio($conexion, $id_hora);

            $obj = [
                'hora' => $id_hora,
                'semestre' => $id_semestre,
                'materia' => $id_materia,
                'dia' => $id_dia,
                'docente' => $id_docente
            ];

            if (profesorOcupado($conexion, $obj)) {
                $errores[] = "El profesor $nombre_docente está ocupado el día $nombre_dia a la hora $hora_inicio.";
            } else if (verificarMateria($conexion, $obj)) {
                $errores[] = "El semestre $id_semestre ya tiene una clase asignada el día $nombre_dia a la hora $hora_inicio.";
            } else {
                $query = "INSERT INTO general (id_docente, id_semestre, id_materia, id_dia, id_hora) VALUES ('$id_docente', '$id_semestre', '$id_materia', '$id_dia', '$id_hora')";
                if (!mysqli_query($conexion, $query)) {
                    $errores[] = "Error al guardar los datos para el día $nombre_dia a la hora $hora_inicio: " . mysqli_error($conexion);
                }
            }
        }
    }

    if (!empty($errores)) {
        foreach ($errores as $error) {
            echo "<script type='text/javascript'>
                    alert('$error');
                  </script>";
        }
        echo "<script type='text/javascript'>
                window.location.href = 'horario.php';
              </script>";
    } else {
        echo "<script type='text/javascript'>
                alert('Se ha generado la asignación de manera exitosa!.');
                window.location.href = 'horario.php';
              </script>";
    }
}
    // Close the database connection
    mysqli_close($conexion);

?>