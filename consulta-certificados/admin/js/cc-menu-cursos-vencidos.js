jQuery(document).ready(function ($) {
    var table = $('#principalTable').DataTable({
        ajax: {
            type: 'POST',
            url: personal_ajax_request.url,
            data: function () {
                return {
                    action: 'get_all_cursos_vencidos',
                    nonce: personal_ajax_request.security,
                    fecha_desde: $('#fecha_desde').val(),
                    fecha_hasta: $('#fecha_hasta').val(),
                };
            },
            dataSrc: ''
        },
        columns: [
            { data: 'nombre', title: 'NOMBRE' },
            { data: 'curso', title: 'ÚLTIMO CURSO REALIZADO' },
            { data: 'acta', title: 'ACTA' },
            { data: 'fecha_acta', title: 'FECHA ACTA' },
            { data: 'fecha_vencimiento', title: 'FECHA VENCIMIENTO' },
            { data: 'estado', title: 'ESTADO' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/es-MX.json',
        },
        scrollX: true,
        fixedHeader: {
            header: true,
        },
        pageLength: 25,
        responsive: true,
    });

    $('#applyFechaFilter').on('click', function () {
        table.ajax.reload();
    });

    $('#fecha_desde, #fecha_hasta').on('change', function () {
        table.ajax.reload();
    });

    $('#principalTable tfoot th').each(function (i) {
        var titles = ['NOMBRE', 'ÚLTIMO CURSO REALIZADO', 'ACTA', 'FECHA ACTA', 'FECHA VENCIMIENTO', 'ESTADO'];
        $(this).html('<input type="text" placeholder="Buscar ' + titles[i] + '" class="form-control form-control-sm" data-index="' + i + '" />');
    });

    $('#principalTable tfoot th input').on('keyup change clear', function () {
        table.column($(this).data('index')).search(this.value).draw();
    });
});