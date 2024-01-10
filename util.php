<?php
function crear_string_from_fecha():string
{
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
    $segundos_2digitos =  $fmt->formatObject($ahora_dti, "ss", $locale);

    $fecha = sprintf("<p>Tu última visita fue el %s, %s de %s de %s a las %s:%s:%s</p>", $dia_semana, $dia, $mes, $ano, $hora_2digitos, $min_2digitos, $segundos_2digitos);
    return $fecha;
}
