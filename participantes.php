<!DOCTYPE html>
<html lang="es">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta charset="UTF-8">
    <title>Calendario - Participantes</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Validator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <style>
        .diaEvento {
            background-color: #1D5DEC;
            color: white;
        }

        .diaMesDisp {
            background-color: #7FB383;
        }

        .diaMesDisp:hover {
            background-color: #658D69;
            cursor: pointer;
        }

        .diaMesNoDisp {
            background-color: #ccc;
        }
    </style>
</head>

<body style="background-color: #ccc;">
    <?php

    // setlocale(LC_TIME, 'es_MX.UTF-8');

    session_start();
    $_SESSION['nombre'] = 'Nombre Apellido';
    $_SESSION['id'] = 1;
    $_SESSION['id_evento'] = 5;

    $nombreDelMes = strftime('%B');
    require_once('clases/ConexionClass.php');
    require_once('clases/EventoClass.php');
    require_once('clases/FechaClass.php');
    require_once('clases/ParticipanteClass.php');
    require_once('clases/DateTimeWithIDClass.php');

    $conexion = new MySQL();
    $evento = new EventoClass();
    $fecha = new FechaClass();
    $participante = new ParticipanteClass();

    $evento->conexion = $conexion;
    $fecha->conexion = $conexion;
    $participante->conexion = $conexion;
    $participante->id = $_SESSION['id'];

    $fechaParticipanteRow = $participante->evento();
    // die($fechaParticipanteRow);
    $fechaParticipanteRow = $conexion->fetch_assoc($fechaParticipanteRow);
    if ($fechaParticipanteRow > 0) {
        if ($fechaParticipanteRow['fecha'] != '') {
            $fechaParticipante = new DateTime($fechaParticipanteRow['fecha']);
        }
    }

    $eventoActivo = $conexion->fetch_assoc($evento->get());

    $fecha->id_evento = $eventoActivo['id'];
    $fechasDispRow = $fecha->get();

    $fechaInicio = new DateTime($eventoActivo['fecha_inicio']);
    $fechaFin = new DateTime($eventoActivo['fecha_fin']);

    $intervalo = $fechaInicio->diff($fechaFin);
    // var_dump($intervalo);
    // die();
    $dias = $intervalo->days;
    $monthI = date('m', strtotime($eventoActivo['fecha_inicio']));
    $monthF = date('m', strtotime($eventoActivo['fecha_fin']));
    $nombreDelMesI = '';
    $nombreDelMesF = '';
    $meses = array(
        '01' => 'Enero',
        '02' => 'Febrero',
        '03' => 'Marzo',
        '04' => 'Abril',
        '05' => 'Mayo',
        '06' => 'Junio',
        '07' => 'Julio',
        '08' => 'Agosto',
        '09' => 'Septiembre',
        '10' => 'Octubre',
        '11' => 'Noviembre',
        '12' => 'Diciembre'
    );

    if (isset($monthI) && array_key_exists($monthI, $meses)) {
        $nombreDelMesI = $meses[$monthI];
    }
    if (isset($monthF) && array_key_exists($monthF, $meses)) {
        $nombreDelMesF = $meses[$monthF];
    }

    $nombreDelMes = $nombreDelMesI == $nombreDelMesF ? $nombreDelMesI : $nombreDelMesI . ' - ' . $nombreDelMesF;

    $fechasDataTime = array();
    $i = 0;
    foreach ($fechasDispRow as $fecha) {
        if ($fecha['disponibilidad'] == 1) {
            $fechasDataTime[$i] = new DateTimeWithID($fecha['amd']);
            $fechasDataTime[$i]->id = $fecha['id_fecha'];
            // var_dump($fechasDataTime[$i]);
            $i++;
        }
    }
    ?>
    <div class="container-fluid mr-5 mt-5 border bg-white">
        <div class="row">
            <div class="col-md-3 p-3">
                <div class="p-3 border">
                    <p class="font-weight-bold text-center">Datos del evento en curso</p>
                    <p class="font-weight-bold">Evento en curso: <span class="font-weight-light"><?php echo $eventoActivo['nombre_evento']; ?></span></p>
                    <p class="font-weight-bold">Fecha Inicio: <span class="font-weight-light"><?php echo $eventoActivo['fecha_inicio']; ?></span></p>
                    <p class="font-weight-bold">Fecha Fin: <span class="font-weight-light"><?php echo $eventoActivo['fecha_fin']; ?></span></p>
                    <p class="font-weight-bold">Numero de participantes: <span class="font-weight-light"><?php echo $eventoActivo['num_part']; ?></span></p>
                    <p class="font-weight-bold">Cantidad por grupo: <span class="font-weight-light"><?php echo $eventoActivo['cantidad_grup']; ?></span></p>
                    <p class="font-weight-bold">Hora de inicio: <span class="font-weight-light"><?php echo date("H:i", strtotime($eventoActivo['hora_inicio'])); ?></span></p>
                    <p class="font-weight-bold">Hora fin: <span class="font-weight-light"><?php echo date("H:i", strtotime($eventoActivo['hora_fin'])); ?></span></p>
                </div>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="text-center mb-4"><?php echo $nombreDelMes; ?></h2>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Lun</th>
                                            <th scope="col">Mar</th>
                                            <th scope="col">Mié</th>
                                            <th scope="col">Jue</th>
                                            <th scope="col">Vie</th>
                                            <th scope="col">Sáb</th>
                                            <th scope="col">Dom</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $fechaActual = new DateTime($eventoActivo['fecha_inicio']);
                                        $firstDay = date('N', strtotime($eventoActivo['fecha_inicio']));

                                        for ($i = 1; $i < $firstDay; $i++) {
                                            echo '<td></td>';
                                        }
                                        // Rellena los días del evento
                                        for ($day = 0; $day <= $dias; $day++) {
                                            // echo $day . '</br>';
                                            if ($day > 0) {
                                                $fechaActual->add(new DateInterval("P1D"));
                                            }

                                            // Verifica si la fecha actual está en el arreglo $fechasDataTime
                                            $class = 'diaMesNoDisp';
                                            $onClick = '';
                                            if ($_SESSION['id_evento'] == $eventoActivo['id']) {
                                                if (isset($fechaParticipante)) {
                                                    if ($fechaParticipante->format('Y-m-d') >= $fechaInicio->format('Y-m-d') && $fechaParticipante->format('Y-m-d') <= $fechaFin->format('Y-m-d')) {
                                                        foreach ($fechasDataTime as $fechaDataTime) {
                                                            if ($fechaDataTime->format('Y-m-d') == $fechaActual->format('Y-m-d')) {
                                                                $fecha = $fechaActual->format('Y-m-d');
                                                                if ($fecha == $fechaParticipante->format('Y-m-d')) {
                                                                    $class = 'diaEvento';
                                                                    $onClick = '';
                                                                } else {
                                                                    $class = '';
                                                                    $onClick = '';
                                                                }
                                                                break;
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    foreach ($fechasDataTime as $fechaDataTime) {
                                                        if ($fechaDataTime->format('Y-m-d') == $fechaActual->format('Y-m-d')) {
                                                            $fecha = $fechaActual->format('Y-m-d');
                                                            $class = 'diaMesDisp';
                                                            $onClick = 'modalRegistro(' . $fechaDataTime->id . ')';
                                                            break;
                                                        }
                                                    }
                                                }
                                            } else {
                                                foreach ($fechasDataTime as $fechaDataTime) {
                                                    if ($fechaDataTime->format('Y-m-d') == $fechaActual->format('Y-m-d')) {
                                                        $fecha = $fechaActual->format('Y-m-d');
                                                        $class = 'diaMesDisp';
                                                        $onClick = 'modalRegistro(' . $fechaDataTime->id . ')';
                                                        break;
                                                    }
                                                }
                                            }

                                            echo "<td class='{$class}' onClick='{$onClick}'>" . $fechaActual->format('j') . "</td>";

                                            // Si es el último día de la semana, cierra la fila y comienza una nueva
                                            if ($fechaActual->format('N') == 7) {
                                                echo '</tr><tr>';
                                            }
                                        }
                                        for ($i = $fechaActual->format('N'); $i < 7; $i++) {
                                            echo '<td></td>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- // * Modal Dias -->
    <div class="modal" id="modalRegistro">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Encabezado del modal -->
                <div class="modal-header">
                    <h4 class="modal-title">Formulario</h4>
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                </div>

                <!-- Contenido del modal -->
                <div class="modal-body">
                    <!-- Formulario -->
                    <form>
                        <input type="text" hidden name="id_evento" id="id_evento" value="<?php echo $eventoActivo['id'] ?>">
                        <input type="text" hidden name="id_fecha_registro" id="id_fecha_registro" value="">
                        <input type="text" hidden name="user_id" id="user_id" value="<?php echo $_SESSION['id']; ?>">
                        <div>
                            <p class="text-center font-weight-bold" id="fecha_p"></p>
                        </div>
                        <div>
                            <p class="text-center font-weight-bold">Fecha Disponible</p>
                            <p class="text-center font-weight-bold">Al seleccionar la fecha se le asignara automáticamente a un grupo y horario</p>
                            <p class="text-center font-weight-bold"><span id="num_part_p">Numero</span> de <?php echo $eventoActivo['cantidad_grup']; ?></p>
                        </div>
                        <!-- Pie del modal -->
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="close-registro">Cerrar</button>
                        <button id="registro" class="btn btn-primary">Registro</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="public/funcionesPart.js"></script>

</body>

</html>