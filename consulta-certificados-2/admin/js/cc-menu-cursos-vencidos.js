jQuery(document).ready(function ($) {
    // let table = new DataTable('#principalTable');
    var table = $('#principalTable').DataTable({
        ajax: {
            type: "POST",
            url: personal_ajax_request.url,
            data: {
                action: "get_all_cursos_vencidos",
                nonce: personal_ajax_request.security,
            },
            dataSrc: ""
        },
        columns: [
            { data: 'nombre', title: 'NOMBRE' },
            { data: 'curso', title: 'ULTIMO CURSO REALIZADO' },
            { data: 'telefono', title: 'TÉLEFONO' },
            { data: 'fecha_acta', title: 'FECHA' },
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/es-MX.json',
        },
        scrollX: true,
        fixedHeader: {
            header: true,
        },
        layout: {
            topStart: {
                buttons: [
                    'copy',
                    'colvis',
                ]
            }
        },
        select: true
    });
    // Setup - add a text input to each footer cell
    $('#principalTable tfoot th').each(function (i) {
        var titles = ['NOMBRE', 'ULTIMO CURSO REALIZADO', 'TÉLEFONO', 'FECHA ACTA']; // Coincide con columns.title
        $(this).html('<input type="text" placeholder="' + titles[i] + '" data-index="' + i + '" />');
    });

    // Filter event handler
    $('#principalTable tfoot th input').on('keyup', function () {
        table
            .column($(this).data('index'))
            .search(this.value)
            .draw();
    });

});