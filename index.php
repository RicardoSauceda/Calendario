<?php
include_once("clases/EventoClass.php");
include_once("clases/ConexionClass.php");
include_once("clases/FechaClass.php");
include_once("clases/ParticipanteClass.php");

$conexion = new MySQL();
$evento = new EventoClass();
$fechas = new FechaClass();
$participante = new ParticipanteClass();
$evento->conexion = $conexion;
$fechas->conexion = $conexion;
$participante->conexion = $conexion;

$eventoActivo = $evento->get();
$rowsEvento = $conexion->fetch_assoc($eventoActivo);
$totalEventos = $conexion->num_rows($eventoActivo);

if ($totalEventos > 0) {
    $fechas->id_evento = $rowsEvento['id'];
    $resultados_por_pagina = 15;
    $pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
    $diasPaginados = $fechas->getPaginate($pagina_actual, $resultados_por_pagina);
    $diasDisp = $fechas->get();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/styles.css">

    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> -->


    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- Validator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <title>Calendario</title>
</head>

<body class="bg-[#EFF8F4]">

    <div class="container p-3 h-100">
        <div class="row">
            <div class="col border h-100">
                <div class="p-2 m-2 h-100">
                    <p class="font-weight-bold text-center m-auto rounded p-2">Información del evento</p>
                    <?php
                    if ($totalEventos > 0) {
                        echo '<p class="font-weight-bold text-center m-auto bg-success rounded p-2 text-white"> Evento Activo </p>';
                    } else {
                    ?>
                        <form action="Modelos/Evento.php" method="POST" id="evento-form">
                            <div class="form-group">
                                <label for="nombre">Nombre del evento</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del evento">
                                <div class="error-message" id="error-nombre"></div>
                            </div>
                            <div class="form-group">
                                <label for="fecha_inicio">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                                <div class="error-message" id="error-fecha_inicio"></div>
                            </div>
                            <div class="form-group">
                                <label for="fecha_fin">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                                <div class="error-message" id="error-fecha_fin"></div>
                            </div>
                            <div class="form-group">
                                <label for="num_part">Numero de participantes</label>
                                <input type="text" class="form-control" id="num_part" name="num_part" placeholder="Numero de participantes">
                                <div class="error-message" id="error-num_part"></div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label for="num_grupo">Numero de participantes por grupo</label>
                                        <input type="text" class="form-control" id="num_grupo" name="num_grupo" placeholder="Num. de participantes por grupo">
                                        <div class="error-message" id="error-num_grupo"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="min_grupo">Tiempo por grupo</label>
                                        <input type="number" class="form-control" id="min_grupo" name="min_grupo" placeholder="Minutos por grupo" min="1">
                                        <div class="error-message" id="error-min_grupo"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label for="hora_inicio">Hora de Inicio</label>
                                        <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" />
                                        <div class="error-message" id="error-hora_inicio"></div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="hora_fin">Hora de finalización</label>
                                        <input type="time" id="hora_fin" name="hora_fin" class="form-control" />
                                        <div class="error-message" id="error-hora_fin"></div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Crear y habilitar</button>
                        </form>
                    <?php
                    }
                    ?>
                    <button type="" class="btn btn-warning text-white mt-1" onclick="modalEventos()">Ver lista de eventos</button>

                </div>
                <div class="border p-2 m-2">
                    <?php
                    if ($totalEventos > 0) {
                        $horasTotal = ((($rowsEvento['num_part'] / $rowsEvento['cantidad_grup']) * $rowsEvento['min_grupo']) / 60) . ' horas';

                        $horaI = new DateTime($rowsEvento['hora_inicio']);
                        $horaF = new DateTime($rowsEvento['hora_fin']);

                        $diferenciaHrs = $horaI->diff($horaF);
                        $diferencia_en_minutos = $diferenciaHrs->h * 60 + $diferenciaHrs->i;
                        $gruposTotales = $diferencia_en_minutos / $rowsEvento['min_grupo'];
                        $partDiarios = $gruposTotales * $rowsEvento['cantidad_grup'];

                        $diasHabiles = $conexion->num_rows($diasDisp);
                        $hrsDiarias = $diferenciaHrs->format("%H horas y %i minutos diarios. <br/>");
                        $diasEstimados = ceil(floatval($horasTotal) / floatval($hrsDiarias));
                    ?>
                        <p class="font-weight-bold">Evento en curso: <span class="font-weight-light"><?php echo $rowsEvento['nombre_evento']; ?></span></p>
                        <p class="font-weight-bold">Fecha Inicio: <span class="font-weight-light"><?php echo $rowsEvento['fecha_inicio']; ?></span></p>
                        <p class="font-weight-bold">Fecha Fin: <span class="font-weight-light"><?php echo $rowsEvento['fecha_fin']; ?></span></p>
                        <p class="font-weight-bold">Numero de participantes: <span class="font-weight-light"><?php echo $rowsEvento['num_part']; ?></span></p>
                        <p class="font-weight-bold">Cantidad por grupo: <span class="font-weight-light"><?php echo $rowsEvento['cantidad_grup']; ?></span></p>
                        <p class="font-weight-bold">Hora de inicio: <span class="font-weight-light"><?php echo date("H:i", strtotime($rowsEvento['hora_inicio'])); ?></span></p>
                        <p class="font-weight-bold">Hora fin: <span class="font-weight-light"><?php echo date("H:i", strtotime($rowsEvento['hora_fin'])); ?></span></p>


                        <p class="font-weight-bold m-0">Participantes diarios: <?php echo $partDiarios ?> máximos.</p>
                        <p class="font-weight-bold m-0">Horas diarias: <?php echo $hrsDiarias ?> </p>
                        <br>
                        <p class="font-weight-bold m-0">Tomando ( <?php echo $rowsEvento['num_part'] ?> Participantes / <?php echo $rowsEvento['cantidad_grup'] ?> Participantes por grupo) por  <?php echo $rowsEvento['min_grupo'] ?>  minutos por grupo se tiene un total de: <?php echo ceil($horasTotal) ?> horas.</p>
                        <br>
                        <p class="font-weight-bold m-0">Dias de evento hábiles: <?php echo $diasHabiles ?> dias</p>
                        <p class="font-weight-bold m-0">Tiempo total estimado: <?php echo $diasEstimados . ' dias.' ?> </p>

                    <?php
                    } else {
                    ?>
                        <div class="container p-5">
                            <p class="text-center font-weight-bold bg-warning rounded p-1 text-white"> Esperando fechas </p>
                        </div>
                    <?php
                    }
                    ?>
                    </p>
                    <?php
                    if ($totalEventos > 0) {
                    ?>
                        <form action="Modelos/Evento.php" method="POST">
                            <input type="text" name="estatus-0" id="estatus-0" value="0" hidden>
                            <button id="deshabilitar_evento" name="deshabilitar_evento" type="submit" class="btn btn-danger">Deshabilitar Evento</button>
                        </form>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <div class="col">
                <div class="border h-100">
                    <?php

                    if ($totalEventos > 0) {
                        while ($row = mysqli_fetch_array($diasPaginados)) {
                            // Mostrar los datos de la fila
                            $fechaDiv = $row['dia_mes'] . '-' . $row['mes'] . '-' . $row['anio'];
                            $fecha = "'" . $fechaDiv . "'";

                            $participante->id_fecha = $row['id_fecha'];
                            $participante->id_evento = $row['id_evento'];
                            $participantes = $participante->get();
                            $numParticipantes = $conexion->num_rows($participantes);
                            $porcentaje = (100 / $partDiarios) * $numParticipantes;

                            $style = 'style="background-color: #ccc";'
                    ?>
                            <div class="progress-barra font-weight-bold" <?php echo $row['disponibilidad'] == 0 ? $style : '' ?> onclick="modalFecha(<?php echo $row['id_fecha']; ?>, <?php echo  $fecha; ?>, <?php echo  $row['disponibilidad']; ?>)">
                                <?php
                                if ($row['disponibilidad'] != 0) {
                                ?>
                                    <div id="progress" style="width: <?php echo $porcentaje; ?>%;"></div>
                                <?php
                                }
                                ?>
                                <p class="percentage" id="percentage"><?php echo $fechaDiv; ?></p>
                            </div>
                        <?php
                        }
                        $total_paginas = ceil($diasHabiles / $resultados_por_pagina);
                        ?>

                        <div class="container mt-4">
                            <nav aria-label="Page navigation example" class="text-center">
                                <ul class="pagination justify-content-center">
                                    <?php
                                    for ($i = 1; $i <= $total_paginas; $i++) {
                                        echo "<li class='page-item text-ce'><a class='page-link' href='?pagina=$i'>$i</a></li> ";
                                    }
                                    ?>
                                </ul>
                            </nav>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="container p-5">
                            <p class="text-center font-weight-bold bg-warning rounded p-1 text-white"> Esperando fechas </p>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    $allEventos = $evento->getAll();
    ?>
    <!-- // * Modal Eventos -->
    <div class="modal" id="modalEvento">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Encabezado del modal -->
                <div class="modal-header">
                    <h4 class="modal-title">Eventos</h4>
                    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                </div>

                <!-- Contenido del modal -->
                <div class="modal-body">
                    <!-- Formulario -->
                    <form action="" method="POST">
                        <?php
                        while ($row = $allEventos->fetch_assoc()) {
                            $eventoId = $row['id'];
                            $estado = $row['estatus'];
                        ?>
                            <div class="container d-flex justify-content-between align-items-center">
                                <label for="switch<?php echo $eventoId; ?>"><?php echo $row['nombre_evento']; ?> :</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="switch<?php echo $eventoId; ?>" <?php echo ($estado == 1) ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="switch<?php echo $eventoId; ?>">
                                        <span id="iconoSwitch" class="fas <?php echo ($estado == 1) ? 'fa-toggle-on' : 'fa-toggle-off'; ?>"></span>
                                    </label>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                        <!-- Pie del modal -->
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- // * Modal Dias -->
    <div class="modal" id="miModal">
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
                    <form action="Modelos/Fecha.php" method="POST">
                        <input type="text" hidden name="id_evento" id="id_evento" value="<?php echo $rowsEvento['id'] ?>">
                        <input type="text" hidden name="id_fecha" id="id_fecha" value="">
                        <input type="text" hidden name="accion" id="accion" value="">
                        <div>
                            <p class="text-center font-weight-bold" id="fecha_p"></p>
                        </div>
                        <div>
                            <p class="text-center font-weight-bold"><span id="num_part_p"></span> de <?php echo $rowsEvento['cantidad_grup']; ?></p>
                        </div>
                        <!-- Pie del modal -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="close-modal">Cerrar</button>
                            <button id="disp_button" type="submit" class=""></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="public/funciones.js"></script>

</body>


</html>