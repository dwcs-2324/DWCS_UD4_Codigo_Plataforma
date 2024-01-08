<?php

// Si el usuario no se ha autentificado, pedimos las credenciales
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header("WWW-Authenticate: Basic realm='Contenido restringido'");
    header("HTTP/1.0 401 Unauthorized");
    die();
}
//Conexión a la base de datos proyecto.
$host = "localhost";
$db = "proyecto";
$user = "gestor";
$pass = "secreto";
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
    $conProyecto = new PDO($dsn, $user, $pass);
    $conProyecto->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
    die("Error en la conexión: mensaje: " . $ex->getMessage());
} // Hacemos la consulta
$consulta = "select * from usuarios where usuario=:u and pass=:p";
$stmt = $conProyecto->prepare($consulta);
$password =    hash('sha256', $_SERVER['PHP_AUTH_PW']);
try {
    $stmt->execute([
        ':u' => $_SERVER['PHP_AUTH_USER'],
        ':p' => $password
    ]);
} catch (PDOException $ex) {
    $conProyecto = null;
    die("Error al recuperar las datos de Mysql: " . $ex->getMessage());
}
// Si la Consulta No devuelve ninguna fila las credenciales son erroneas.
if ($stmt->rowCount() == 0) {
    header("WWW-Authenticate: Basic realm='Contenido restringido'");
    header("HTTP/1.0 401 Unauthorized");
    $stmt = null;
    $conProyecto = null;
    die();
}
$stmt = null;
$conProyecto = null;
//Comentamos el uso de strftime, obsoleto:
//Para poner el formato fecha en castellano y recuperar fecha y hora de acceso
// setlocale(
//     LC_ALL,
//     'es_ES.UTF-8'
// );
// date_default_timezone_set('Europe/Madrid');
// $ahora = new DateTime();
// $fecha =
//     strftime("Tu última visita fue el %A, %d de %B de %Y a las %H:%M:%S", date_timestamp_get($ahora));

//++ i18n
//Para trabajar con el módulo de internacionalización
//Modificar  fichero C:\xampp\php\php.ini descomentando (quitando ;) a la línea:
//extension=intl
// Comprobar que el resultado de phpinfo() tiene un apartado de intl
$locale = "es_ES";
$timezone =  'Europe/Madrid';
//Creamos un objeto DataTimeInmutable con la fecha y hora actuales. 
//(DataTimeInmutable no cambia la fecha original y crea un nuevo objeto si se llama a modify. Ejemplo en //https://stackoverflow.com/questions/67536245/datetimeimmutable-vs-datetime)
$ahora_dti = new DateTimeImmutable();
//Creamos un objeto IntlDateFormatter para un locale dado, con un formato de fecha, formato de hora, timezone y el calendario gregoriano
$fmt = new IntlDateFormatter(
    $locale,
    //https://www.php.net/manual/en/class.intldateformatter.php#intl.intldateformatter-constants
    IntlDateFormatter::FULL,  //dateType
    IntlDateFormatter::FULL,  //timeType
    $timezone,
    IntlDateFormatter::GREGORIAN
);
//También se pueden obtener partes de una fecha a través de un patrón
//Los posibles patrones se pueden consultar en https://unicode-org.github.io/icu/userguide/format_parse/datetime/
$dia_semana = $fmt->formatObject($ahora_dti, "EEEE", $locale);
$mes = $fmt->formatObject($ahora_dti, "MMMM", $locale);
$diaSemana = $fmt->formatObject($ahora_dti, "EEEE", $locale);
$dia = $fmt->formatObject($ahora_dti, "dd", $locale);
$ano = $fmt->formatObject($ahora_dti, "y", $locale);
$hora_2digitos = $fmt->formatObject($ahora_dti, "HH", $locale);
$min_2digitos = $fmt->formatObject($ahora_dti, "mm", $locale);
$segundos_2digitos=  $fmt->formatObject($ahora_dti, "ss", $locale);

$fecha = sprintf("<p>Tu última visita fue el %s, %s de %s de %s a las %s:%s:%s</p>", $dia_semana, $dia, $mes, $ano, $hora_2digitos, $min_2digitos, $segundos_2digitos);


// si existe la cookie recupero su valor
if (isset($_COOKIE[$_SERVER['PHP_AUTH_USER']])) {
    $mensaje = $_COOKIE[$_SERVER['PHP_AUTH_USER']];
}
//si no existe es la primera visita para este usuario
else {
    $mensaje = "Es la primera vez que visitas la página.";
}
//Creo o actualizo la cookie con la nueva fecha de acceso, la cookie durara una semana
setcookie($_SERVER['PHP_AUTH_USER'], "$fecha", time() + 7 * 24 * 60 * 60); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https:</stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Cookies</title>
</head>

<body style="background:gainsboro">
    <p class="/loat-left m-3">
        <?php echo $mensaje; ?>
    </p>
    <br><br>
    <h4 class="mt-3 text-center font-weight-bold">Ejercicio Apartado 2 Unidad 4</h4>
    <div class='container mt-3'>
        <div class='row'>
            <div class='col-md-4 font-weight-bold'>
                Nombre Usuario:
            </div>
            <div class='col-md-4'>
                <?php echo $_SERVER['PHP_AUTH_USER']; ?>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-4 font-weight-bold'>
                Password Usuario (sha256):
            </div>
            <div class='col-md-4'>
                <?php echo
                hash('sha256', $_SERVER['PHP_AUTH_PW']); ?>
            </div>
        </div>
    </div>
</body>

</html>