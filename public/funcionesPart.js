$(document).ready(function () {
    $('#close-registro').click(function () {
        location.reload();
    });

});

$('#registro').click(function () {
    let id_fecha = $("#id_fecha_registro").val();
    let id_evento = $("#id_evento").val();
    let user_id = $("#user_id").val();
    if (confirm("¿Realmente desea realizar el registro?")) {
        ajax(id_fecha, id_evento, user_id);
    }
});

function ajax(id_fecha, id_evento, user_id) {

    $.ajax({
        type: "POST",
        url: "Modelos/Participante.php",
        data: {
            'registroV': 1,
            'id_fecha_ajax': id_fecha,
            'id_evento_ajax': id_evento,
            'user_id': user_id,
        },
        success: function (response) {
            console.log(response);
            if (response == 'Fecha llena') {
                Swal.fire({
                    icon: 'info',
                    title: 'Oops...',
                    text: 'Fecha llena',
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                })
            }
            location.reload();
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error("Error en la llamada AJAX:", textStatus, errorThrown);
        }
    });

}


function modalRegistro(id_fecha) {

    $("#modalRegistro").modal();
    $("#id_fecha_registro").val(id_fecha);

}
