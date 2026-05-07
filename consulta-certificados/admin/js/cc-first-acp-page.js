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
                    <button class="btn btn-sm btn-primary btn-edit"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-secondary btn-download"><i class="fas fa-download"></i></button>
                    <button class="btn btn-sm btn-danger btn-hide"><i class="fas fa-eye-slash"></i></button>
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
                    {
                        extend: 'excelHtml5',
                        text: 'Exportar Excel',
                        exportOptions: {
                            columns: ':not(:last-child)', // Omite la última columna (Acciones)
                            modifier: {
                                search: 'applied',
                                order: 'applied',
                                page: 'all' // Exporta todos los registros
                            }
                        },
                        filename: 'listado_certificados',
                        title: null
                    }
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
            fecha_acta: rowData.FECHA_ACTA,
            resolucion: rowData.RESOLUCION,

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

    // Evento para editar certificado
    $('#principalTable').on('click', '.btn-edit', function () {
        var rowData = table.row($(this).closest('tr')).data();
        
        $('#edit_cert_id').val(rowData.ID);
        $('#edit_razon_social').val(rowData['RAZON SOCIAL']);
        $('#edit_nit').val(rowData.NIT);
        $('#edit_resolucion').val(rowData.RESOLUCION);
        $('#edit_fecha_resolucion').val(rowData['FECHA RESOLUCION']);
        $('#edit_apellido1').val(rowData.APELLIDO1);
        $('#edit_apellido2').val(rowData.APELLIDO2);
        $('#edit_nombre1').val(rowData.NOMBRE1);
        $('#edit_nombre2').val(rowData.NOMBRE2);
        $('#edit_cedula').val(rowData.CEDULA);
        $('#edit_numerodiploma').val(rowData.NUMERODIPLOMA);
        $('#edit_curso').val(rowData.CURSO);
        $('#edit_acta').val(rowData.ACTA);
        $('#edit_fecha_acta').val(rowData.FECHA_ACTA);
        $('#editCertFeedback').html('');

        var editModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editCertModal'));
        editModal.show();
    });

    // Evento para guardar edición
    $('#editCertForm').on('submit', function (event) {
        event.preventDefault();
        var formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'cc_edit_certificate' });
        formData.push({ name: 'nonce', value: personal_ajax_request.security });

        $.post(personal_ajax_request.url, formData, function (response) {
            if (response.success) {
                $('#editCertFeedback').html('<div class="alert alert-success">' + response.data + '</div>');
                table.ajax.reload(null, false);
            } else {
                $('#editCertFeedback').html('<div class="alert alert-danger">' + (response.data || 'Error al actualizar el certificado.') + '</div>');
            }
        }, 'json');
    });

    // Evento para ocultar certificado
    $('#principalTable').on('click', '.btn-hide', function () {
        var rowData = table.row($(this).closest('tr')).data();
        
        if (confirm('¿Estás seguro de que deseas ocultar (inactivar) este certificado?')) {
            $.post(personal_ajax_request.url, {
                action: 'cc_toggle_certificate_status',
                nonce: personal_ajax_request.security,
                id: rowData.ID,
                status: 'inactivo'
            }, function (response) {
                if (response.success) {
                    table.ajax.reload(null, false);
                } else {
                    alert(response.data || 'Error al ocultar.');
                }
            }, 'json');
        }
    });
});