<?php

require_once('../clases/ConexionClass.php');
require_once('../clases/EventoClass.php');
require_once('../clases/FechaClass.php');
require_once('../clases/ParticipanteClass.php');
require_once('../clases/HorarioClass.php');

$conexion = new MySQL();
$evento = new EventoClass();
$fecha = new FechaClass();
$participante = new ParticipanteClass();
$horario = new HorarioClass();

$evento->conexion = $conexion;
$fecha->conexion = $conexion;
$participante->conexion = $conexion;
$horario->conexion = $conexion;


if (isset($_POST['registroV'])) {

    session_start();

    $_SESSION['nombre'] = 'Nombre Apellido';
    $participante->nombre = $_SESSION['nombre'];
    $participante->id_fecha = $_POST['id_fecha_ajax'];
    $participante->id = $_POST['user_id'];

    session_unset();
    session_destroy();

    // * Verificar el estado del evento
    $eventoActivo = $evento->get();
    $rowsEvento = $conexion->fetch_assoc($eventoActivo);

    if ($rowsEvento['id'] == $_POST['id_evento_ajax']) {
        if ($rowsEvento['estatus'] != 1) {
            echo 'Evento no disponible';
            exit();
        }
    } else {
        echo 'Evento no disponible';
        exit();
    }

    $participante->id_evento = $_POST['id_evento_ajax'];        // ? Participante ID evento

    if ($participante->count() >= $rowsEvento['num_part']) {
        echo 'Evento Lleno';
        exit();
    }

    // * Verificar el estado de la fecha (día)
    $horaI = new DateTime($rowsEvento['hora_inicio']);
    $horaF = new DateTime($rowsEvento['hora_fin']);
    $diferenciaHrs = $horaI->diff($horaF);
    $diferencia_en_minutos = $diferenciaHrs->h * 60 + $diferenciaHrs->i;
    $gruposTotales = $diferencia_en_minutos / $rowsEvento['min_grupo'];
    $partDiarios = $gruposTotales * $rowsEvento['cantidad_grup'];
    $fecha->maxPart = $partDiarios;
    $fecha->id_fecha = $_POST['id_fecha_ajax'];
    $fecha->id_evento = $_POST['id_evento_ajax'];

    // ? Verifica de espacio y estado de la fecha
    $disponibilidad = $fecha->disponible();

    if ($disponibilidad == 0) {
        echo 'Fecha llena o no disponible';
        exit();
    }

    // * Verificar Horarios
    $horario->id_evento = $_POST['id_evento_ajax'];
    $horario->id_fecha = $_POST['id_fecha_ajax'];
    $horarios = $horario->get();
    $numRowHorarios = $conexion->num_rows($horarios);
    $rowsHorarios = $conexion->fetch_assoc($horarios);

    if ($numRowHorarios == 0) {
        // ? Crear la relación y registro de Participante / Horario
        $crearHorario = crearHorario($rowsEvento['hora_inicio'], $rowsEvento['min_grupo'],  $_POST['id_evento_ajax'], $_POST['id_fecha_ajax']);
        if (strpos($crearHorario, 'Horario creado') !== false) {
            $horario_id = str_replace('Horario creado, ID: ', '', $crearHorario);
            $participante->id_horario = $horario_id;
            $participante->updateHorario();
        }
    } else {

        $horarioEncontrado = false;

        foreach ($rowsHorarios as $horarioReg) {
            $horarioID = $horarioReg['id'];
            $horario->id = $horarioID;
            $participante = $horario->participantes();
            if ($participante < 25) {
                $horarioDisponibleID = $horarioID;
                $horarioEncontrado = true;
                break;
            }
        }

        if ($horarioEncontrado == false) {

            $ultimoHorario = $conexion->fetch_array($horario->ultimoHorario());
            $horaFinUltimoHorario = strtotime($ultimoHorario['hora_fin']);
            $horaFinEvento = strtotime($rowsEvento['hora_fin']);
            $minGrupo = $rowsEvento['min_grupo'] * 60; // Convertir minutos a segundos

            if ($horaFinUltimoHorario + $minGrupo <= $horaFinEvento - $minGrupo) {
                crearHorario($ultimoHorario['hora_fin'], $rowsEvento['min_grupo'],  $_POST['id_evento_ajax'], $_POST['id_fecha_ajax']);
                // ? Crear la relación y registro de Participante / Horario
                $crearHorario = crearHorario($rowsEvento['hora_inicio'], $rowsEvento['min_grupo'],  $_POST['id_evento_ajax'], $_POST['id_fecha_ajax']);
                if (strpos($crearHorario, 'Horario creado') !== false) {
                    $horario_id = str_replace('Horario creado, ID: ', '', $crearHorario);
                    $participante->id_horario = $horario_id;
                    $participante->updateHorario();
                }
            } else {
                echo 'Sin disponibilidad para asignar un horario';
                exit();
            }
        }

        if ($horarioEncontrado == true) {
            $participante->id_horario = $horarioDisponibleID;
            // ? Crear la relación y registro de Participante / Horario
            $participante->updateHorario();
        }
    }

    $registro = $participante->store();
    echo $registro;
    exit();
}

function crearHorario($inicio, $tiempoGrupo, $id_evento, $id_fecha)
{
    // Instanciar la clase Horario
    $conexion = new MySQL();
    $horario = new HorarioClass();
    $horario->conexion = $conexion;

    // Configurar las propiedades del horario
    $horario->id_evento = $id_evento;
    $horario->id_fecha = $id_fecha;
    $horario->hora_inicio = $inicio;
    $horario->hora_fin = date('H:i:s', strtotime($inicio) + ($tiempoGrupo * 60));

    // Crear el registro del horario y retornar el ID de este.
    $resultado = $horario->store();
    return $resultado;
}


if ((isset($_POST['id_fecha_ajax']) && isset($_POST['id_evento_ajax'])) && ($_POST['id_fecha_ajax'] != null && $_POST['id_evento_ajax'] != null)) {
    $participante->id_fecha = $_POST['id_fecha_ajax'];
    $participante->id_evento = $_POST['id_evento_ajax'];
    $cantidad = $participante->countDate();
    echo $cantidad;
    exit();
}

if ($_POST['nombres'] != null && $_POST['apellidoPaterno'] != null && $_POST['apellidoMaterno'] != null && $_POST['id_evento'] != null && $_POST['id_fecha'] != null) {
    $participante->nombre = $_POST['nombres'] . ' ' . $_POST['apellidoPaterno'] . ' ' . $_POST['apellidoMaterno'];
    $participante->id_evento = $_POST['id_evento'];
    $participante->id_fecha = $_POST['id_fecha'];
    $participante->store();

    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    echo 'vacio';
}
