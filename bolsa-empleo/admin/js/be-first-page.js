jQuery(document).ready(function ($) {

    $.ajax({
        type: "POST",
        url: personal_ajax_request.url,
        data: {
            action: "get_cvs",
            nonce: personal_ajax_request.security,
        },
        success: function (response) {
            console.log(response);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log("Status: " + textStatus); alert("Error: " + errorThrown);
        }
    });

    var table = $('#principalTable').DataTable({
        ajax: {
            type: "POST",
            url: personal_ajax_request.url,
            data: {
                action: "get_cvs",
                nonce: personal_ajax_request.security,
            },
            dataSrc: ""
        },
        columns: [
            { data: 'ID' },
            { data: 'Nombres' },
            { data: 'Apellidos' },
            { data: 'Genero' },
            { data: 'Edad' },
            { data: 'Ciudad' },
            { data: 'Celular' },
            { data: 'Correo' },
            { data: 'Academico' },
            { data: 'Profesion' },
            { data: 'Estado' },
            { data: 'Libreta_militar'},
            { data: 'Experiencia' },
            { data: 'Diploma' },
            { data: 'Estatura' },
            { data: 'Barrio' },
            { data: 'Direccion' },
            { data: 'Departamento' },
            { data: 'Cedula' },
            { 
                data: 'Archivo_CV',
                render: function(data) {
                    if (data) {
                        return '<a href="'+data+'" target="_blank" class="button button-secondary">Ver CV</a>';
                    }
                    return '<span style="color:gray">N/A</span>';
                }
            },
            { 
                data: 'Aprobado',
                render: function(data, type, row) {
                    if (data == 1) {
                        return '<span style="color: green; font-weight: bold;">Sí</span>';
                    } else {
                        return '<span style="color: orange; font-weight: bold;">Pendiente</span>';
                    }
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    if (!personal_ajax_request.is_secretario) {
                        return '<span style="color:gray;">Sin permisos</span>';
                    }
                    var html = '';
                    if (row.Aprobado == 1) {
                        html += '<button class="btn-aprobar button button-secondary" data-id="' + row.ID + '" data-status="0" style="margin-right: 5px;">Revocar</button>';
                    } else {
                        html += '<button class="btn-aprobar button button-primary" data-id="' + row.ID + '" data-status="1" style="margin-right: 5px;">Aprobar</button>';
                    }
                    html += '<button type="button" class="btn btn-sm btn-info edit-user-button button" data-user-id="' + row.ID + '"><i class="fa fa-edit"></i> Editar</button>';
                    return html;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/es-MX.json',
        },
        scrollX: true,
        fixedColumns: true,
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
                            modifier: {
                                search: 'applied',
                                order: 'applied',
                                page: 'all' // Exporta todos los registros
                            }
                        },
                        filename: 'listado_de_hojas_de_vida_existentes_cvs',
                        title: null
                    }
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

    // Handle form submission event 
    $('#cvForm').on('submit', function (e) {

        // Default export is a4 paper, portrait, using millimeters for units
        const doc = new jsPDF();

        var rowInPdf = 10;

        doc.setFontSize(22);
        doc.text('Hojas de vida', 10, rowInPdf);
        rowInPdf += 10;

        doc.setFontSize(16);

        var rowsSelected = 0;
        $(table.rows({
            selected: true
        }).$('input[type="checkbox"]').map(function () {
            if ($(this).prop("checked")) {
                rowsSelected++;
                var rowCounter = 0;


                $(this).closest('tr').find('td').each(function () {
                    // This will be the text of each <td>

                    if (titleColumns[rowCounter] !== '') {
                        doc.text(titleColumns[rowCounter] + ": " + $(this).text(), 10, rowInPdf);
                    }

                    rowCounter += 1;
                    rowInPdf += 10;
                });

                doc.addPage();
                rowInPdf = 10;
            }
        }));

        if (rowsSelected === 0) {
            $.bootstrapGrowl("<b>No hay información</b><br>Seleccione una o múltiples filas de la tabla e intente de nuevo", {
                type: 'danger',
                width: 'auto',
                allow_dismiss: false
            });
        } else {
            $.bootstrapGrowl("Pdf generado con éxito", {
                type: 'success'
            });
            doc.save("Hojas de vida - AESS.pdf");

        }

        e.preventDefault(); // Prevent actual form submission
    });

    // Delegación de eventos para botones de aprobación
    $('#principalTable tbody').on('click', '.btn-aprobar', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var status = $(this).data('status');
        var btn = $(this);
        
        btn.prop('disabled', true);
        
        $.ajax({
            type: "POST",
            url: personal_ajax_request.url,
            data: {
                action: "approve_cv",
                nonce: personal_ajax_request.security,
                id: id,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    table.ajax.reload(null, false); // recargar datos sin perder paginación
                } else {
                    alert("Error: " + response.data);
                    btn.prop('disabled', false);
                }
            },
            error: function() {
                alert("Error de conexión");
                btn.prop('disabled', false);
            }
        });
    });

    // Delegación de eventos para botón de editar
    $('#principalTable tbody').on('click', 'button.edit-user-button', function () {
        var userId = $(this).data('user-id');
        loadUserForEdit(userId);
    });

    function loadUserForEdit(userId) {
        $.post(personal_ajax_request.url, {
            action: 'get_user_details',
            nonce: personal_ajax_request.ru_security,
            id: userId,
        }, function (response) {
            if (response.success) {
                var user = response.data;
                $('#edit_user_id').val(user.ID);
                $('#edit_first_name').val(user.first_name);
                $('#edit_middle_name').val(user.middle_name);
                $('#edit_last_name').val(user.last_name);
                $('#edit_second_last_name').val(user.second_last_name);
                $('#edit_national_id').val(user.national_id);
                $('#edit_email').val(user.email);
                $('#edit_phone_number').val(user.phone_number);
                $('#edit_user_role').val(user.user_role);
                $('#edit_address').val(user.address);
                $('#editUserFeedback').html('');

                var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                editModal.show();
            } else {
                alert(response.data || 'No se pudo cargar la información del usuario.');
            }
        }, 'json');
    }

    $('#editUserForm').on('submit', function (event) {
        event.preventDefault();
        var formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'ru_update_user' });
        formData.push({ name: 'nonce', value: personal_ajax_request.ru_security });

        $.post(personal_ajax_request.url, formData, function (response) {
            if (response.success) {
                $('#editUserFeedback').html('<div class="alert alert-success">' + response.data + '</div>');
                table.ajax.reload(null, false);
            } else {
                $('#editUserFeedback').html('<div class="alert alert-danger">' + (response.data || 'Error al actualizar el usuario.') + '</div>');
            }
        }, 'json');
    });

});