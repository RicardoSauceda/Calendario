<?php

require_once('../clases/ConexionClass.php');
require_once('../clases/EventoClass.php');
require_once('../clases/FechaClass.php');

$conexion = new MySQL();
$evento = new EventoClass();
$fecha = new FechaClass();
$evento->conexion = $conexion;
$fecha->conexion = $conexion;


if (!is_null($_POST['id_evento']) && !is_null($_POST['id_fecha']) && !is_null($_POST['accion'])) {
    $fecha->disponibilidad = $_POST['accion'];
    $fecha->id_fecha = $_POST['id_fecha'];
    $fecha->update();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
