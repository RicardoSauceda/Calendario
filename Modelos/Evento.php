<?php

require_once('../clases/ConexionClass.php');
require_once('../clases/EventoClass.php');
require_once('../clases/FechaClass.php');
require_once('../clases/HorarioClass.php');

$conexion = new MySQL();
$evento = new EventoClass();
$fecha = new FechaClass();
$horario = new HorarioClass();

$evento->conexion = $conexion;
$fecha->conexion = $conexion;
$horario->conexion = $conexion;


if (isset($_POST['deshabilitar_evento']) and isset($_POST['estatus-0'])) {
    $evento->estatus = $_POST['estatus-0'];
    $evento->update();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} elseif (isset($_POST['nuevoEstado'])) {
    $evento->estatus = $_POST['nuevoEstado'];
    $evento->id = $_POST['eventoId'];
    echo $evento->update();
    return;
}

if ($_POST['nombre'] != null && $_POST['fecha_inicio'] != null && $_POST['fecha_fin'] != null && $_POST['num_part'] != null && $_POST['num_grupo'] != null) {

    // * Guardar Evento
    $evento->fecha_inicio = $_POST['fecha_inicio'];
    $evento->fecha_fin = $_POST['fecha_fin'];
    $evento->nombre_evento = $_POST['nombre'];
    $evento->num_part = $_POST['num_part'];
    $evento->cantidad_grup = $_POST['num_grupo'];
    $evento->minutos = $_POST['min_grupo'];
    $evento->hora_inicio = $_POST['hora_inicio'];
    $evento->hora_fin = $_POST['hora_fin'];
    $evento->store();

    // * Guardar Dias del evento
    $fecha->id_evento = $evento->getId();
    $timestampInicio = strtotime($_POST['fecha_inicio']);
    $timestampFin = strtotime($_POST['fecha_fin']);
    // setlocale(LC_TIME, 'es_MX');

    while ($timestampInicio <= $timestampFin) {

        $fecha->fecha = strftime('%Y', $timestampInicio) . '-' . strftime('%m', $timestampInicio) . '-' . strftime('%d', $timestampInicio);
        $fecha->dia_mes = strftime('%e', $timestampInicio);
        $fecha->mes = strftime('%B', $timestampInicio);
        $fecha->anio = strftime('%Y', $timestampInicio);
        $fecha->dia_semana = strftime('%A', $timestampInicio);
        if (strftime('%A', $timestampInicio) == 'Sunday' OR strftime('%A', $timestampInicio) == 'Saturday') {
            $fecha->disponibilidad = 0;
        } else {
            $fecha->disponibilidad = 1;
        }
        $fecha->store();
        $timestampInicio = strtotime('+1 day', $timestampInicio);
    }

    // // * Generar Horarios

    // $diasXevento = (strtotime($_POST['fecha_fin']) - strtotime($_POST['fecha_inicio'])) / (60 * 60 * 24) + 1;
    // $gruposXdia = ((strtotime($_POST['hora_fin']) - strtotime($_POST['hora_inicio'])) / 60) / $partGrupo;
    // $gruposEvento = $diasXevento * $gruposXdia;

    // $horario->id_evento = $evento->getId();
    // $horario->id_fecha = strtotime($_POST['fecha_inicio']);

    // $horaFinGrupo = strtotime($_POST['hora_inicio']);
    // while ($horaFinGrupo <= strtotime($_POST['hora_fin'])) {
    // }



    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
