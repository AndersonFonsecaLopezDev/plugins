jQuery(document).ready(function ($) {
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
                data: null,
                orderable: false,
                searchable: false,
                defaultContent: `
                    <button class="btn btn-sm btn-primary btn-download">Descargar</button>
                `
            }
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

    // Filtros en el footer
    $('#principalTable tfoot th').each(function (i) {
        var title = $('#principalTable thead th').eq($(this).index()).text();
        $(this).html('<input type="text" placeholder="' + title + '" data-index="' + i + '" />');
    });

    $('#principalTable tfoot th input').on('keyup', function () {
        table.column($(this).data('index')).search(this.value).draw();
    });

    // Evento para descargar PDF con los datos de la fila
    $('#principalTable').on('click', '.btn-download', function () {
        var rowData = table.row($(this).closest('tr')).data();

        // Crear formulario de descarga
        var form = $('<form>', {
            method: 'POST',
            action: personal_ajax_request.url,
            target: '_blank', // Abre el PDF en una nueva pestaña
            style: 'display: none;'
        }).appendTo('body');

        // Datos a enviar al backend
        var formData = {
            action: 'generate_diploma',
            nonce: personal_ajax_request.security,
            numero_diploma: rowData.NUMERODIPLOMA,
            nombre_completo: rowData.NOMBRE1 + ' ' + (rowData.NOMBRE2 || '') + ' ' + rowData.APELLIDO1 + ' ' + (rowData.APELLIDO2 || ''),
            cedula: rowData.CEDULA,
            curso: rowData.CURSO,
            fecha_acta: rowData.FECHA_ACTA
        };

        // Añadir inputs al formulario
        $.each(formData, function (key, value) {
            $('<input>')
                .attr({ type: 'hidden', name: key, value: value })
                .appendTo(form);
        });

        // Enviar formulario
        form.submit();

        // Eliminar el formulario después de 1 segundo
        setTimeout(function () {
            form.remove();
        }, 1000);
    });
});