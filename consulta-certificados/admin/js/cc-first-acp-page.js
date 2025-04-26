jQuery(document).ready(function ($) {
    // let table = new DataTable('#principalTable');
    var table = $('#principalTable').DataTable({
        ajax: {
            type: "POST",
            url: personal_ajax_request.url,
            data: {
                action: "get_all_certificates",
                nonce: personal_ajax_request.security,
            },
            dataSrc: ""
        },
        columns: [
            { data: 'RAZON SOCIAL' },
            { data: 'NIT' },
            { data: 'RESOLUCION' },
            { data: "FECHA RESOLUCION" },
            { data: 'APELLIDO1' },
            { data: 'APELLIDO2' },
            { data: 'NOMBRE1' },
            { data: 'NOMBRE2' },
            { data: 'CEDULA' },
            { data: 'NUMERODIPLOMA' },
            { data: "CURSO" },
            { data: "ACTA" },
            { data: "FECHA_ACTA" },
            {
                data: null, // No viene del servidor
                orderable: false, // No se puede ordenar
                searchable: false, // No se puede buscar
                defaultContent: `
                    <button class="btn btn-sm btn-primary btn-edit">Descargar</button>
                `
            }
        ],
        
        language: {
            url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/es-MX.json',
        },
        // fixedColumns: true,
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
        var title = $('#principalTable thead th')
            .eq($(this).index())
            .text();
        $(this).html(
            '<input type="text" placeholder="' + title + '" data-index="' + i + '" />'
        );
    });

    // Filter event handler
    $('#principalTable tfoot th input').on('keyup', function () {
        table
            .column($(this).data('index'))
            .search(this.value)
            .draw();
    });

});