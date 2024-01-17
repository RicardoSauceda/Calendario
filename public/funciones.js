$(document).ready(function () {

    $.validator.addMethod("fechaMayor", function (value, element, params) {
        var fecha_inicio = new Date($("#fecha_inicio").val());
        var fecha_fin = new Date($("#fecha_fin").val());
        return fecha_fin >= fecha_inicio;
    }, "La fecha de fin debe ser mayor o igual a la fecha de inicio.");

    $("#evento-form").validate({
        rules: {
            nombre: "required",
            fecha_inicio: "required",
            fecha_fin: {
                required: true,
                fechaMayor: true
            },
            num_part: "required",
            min_grupo: {
                required: true,
                min: 1
            },
            num_grupo: "required",
            hora_inicio: "required",
            hora_fin: "required"
        },
        messages: {
            nombre: "Ingresa el nombre del evento",
            fecha_inicio: "Ingrese la fecha de inicio",
            fecha_fin: {
                required: "Ingrese la fecha de fin",
                fechaMayor: "La fecha de fin debe ser mayor o igual a la fecha de inicio."
            },
            num_part: "Ingrese el numero de participantes",
            min_grupo: {
                required: "El campo de minutos es necesario",
                min: "El tiempo debe ser mayor a 0 minutos"
            },
            num_grupo: "Ingrese la cantidad de participantes por grupo",
            hora_inicio: "Establezca una hora de Inicio",
            hora_fin: "Establezca una hora de fin"

        },
        errorPlacement: function (error, element) {
            error.addClass("invalid-feedback");
            error.appendTo("#error-" + element.attr("name"));
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass("is-invalid");
        },
        submitHandler: function (form) {
            form.submit();
        }
    });

    $('#deshabilitar_evento').click(function () {
        const continuar = window.confirm("¿Desea eliminar el evento?");

        if (!continuar) {
            event.preventDefault();
        }
    });

    $('#close-modal').click(function () {
        location.reload();
    });


    $('input[type="checkbox"]').change(function () {
        var eventoId = $(this).attr('id').replace('switch', '');
        var nuevoEstado = this.checked ? 1 : 0;

        $.ajax({
            type: 'POST',
            url: 'Modelos/Evento.php',
            data: {
                'eventoId': eventoId,
                'nuevoEstado': nuevoEstado
            },
            success: function (response) {
                // console.log(response)
                location.reload();
            }
        });
    });


});

function modalFecha(id, fecha, disponibilidad) {

    let id_evento = $("#id_evento").val();

    $.ajax({
        type: "POST",
        url: "Modelos/Participante",
        data: {
            'id_fecha_ajax': id,
            'id_evento_ajax': id_evento,
        },
        success: function (response) {
            console.log(response);
            $("#id_fecha").val(id);
            $('#fecha_p').text('Datos de ' + fecha);
            $('#num_part_p').text('Numero de participantes: ' + response);
            $("#miModal").modal();

            if (disponibilidad == 0) {
                $("#disp_button").addClass('btn btn-success text-white');
                $("#disp_button").text('Habilitar Fecha');
                $("#accion").val(1);
            } else if (disponibilidad == 1) {
                $("#disp_button").addClass('btn btn-warning text-white');
                $("#disp_button").text('Deshabilitar Fecha');
                $("#accion").val(0);
            }

        },
        error: function (xhr, textStatus, errorThrown) {
            console.error("Error en la llamada AJAX:", textStatus, errorThrown);
        }
    });
}

function modalEventos() {
    $("#modalEvento").modal();
}