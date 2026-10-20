jQuery(document).ready(function ($) {

    // Crear fila de filtros en thead antes de inicializar DataTables
    $('#principalTable thead tr')
        .clone(true)
        .addClass('filters-row')
        .appendTo('#principalTable thead');

    $('#principalTable thead tr.filters-row th').each(function (i) {
        var title = $('#principalTable thead tr:eq(0) th').eq(i).text().trim();
        if (i === 0 || title === 'Acciones' || !title) {
            $(this).html('');
        } else {
            $(this).html('<input type="text" placeholder="' + title + '" />');
        }
    });

    var table = $('#principalTable').DataTable({
        orderCellsTop: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        scrollX: true,
        scrollY: '60vh',
        scrollCollapse: true,
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
            { data: 'Fecha_Inscripcion' },
            { data: 'Nombres' },
            { data: 'Apellidos' },
            { data: 'Modalidad' },
            { data: 'Sede' },
            { data: 'Genero' },
            { data: 'Edad' },
            { data: 'Ciudad' },
            { data: 'Celular' },
            { data: 'Correo' },
            { data: 'Curso' },
            { data: 'Ciclo' },
            { data: 'Fecha_Acta' },
            { data: 'Vencimiento' },
            { data: 'Academico' },
            { data: 'Profesion' },
            { data: 'Estado' },
            { data: 'Libreta_militar' },
            {
                data: 'Experiencia',
                className: 'col-experiencia',
                render: function(data, type, row) {
                    if (type !== 'display') {
                        return data || '';
                    }
                    if (!data) return '<span style="color:gray">N/A</span>';
                    return '<div class="experiencia-clamp" title="Clic para expandir/contraer">' + $('<div>').text(data).html() + '</div>';
                }
            },
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

    // Delegación para filtros en encabezados fijos
    $(table.table().header()).on('keyup change clear', '.filters-row input', function () {
        var colIdx = $(this).closest('th').index();
        if (table.column(colIdx).search() !== this.value) {
            table.column(colIdx).search(this.value).draw();
        }
    });

    // Delegación para expandir/contraer experiencia
    $('#principalTable tbody').on('click', '.experiencia-clamp', function () {
        $(this).toggleClass('expanded');
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
                $('#edit_academico').val(user.academico);
                $('#edit_profesion').val(user.profesion);
                $('#edit_estado_laboral').val(user.estado_laboral);
                $('#edit_libreta_militar').val(user.libreta_militar);
                $('#edit_experiencia').val(user.experiencia);
                $('#edit_diploma').val(user.diploma);
                $('#edit_estatura').val(user.estatura);
                $('#edit_barrio').val(user.barrio);
                $('#edit_modalidad').val(user.modalidad || '');
                $('#edit_sede').val(user.sede || '');
                $('#edit_curso_realizado').val(user.curso_realizado || '');
                $('#edit_ciclo_curso').val(user.ciclo_curso || '');
                $('#edit_fecha_acta_curso').val(user.fecha_acta_curso);
                $('#edit_fecha_vencimiento_curso').val(user.fecha_vencimiento_curso);
                $('#editUserFeedback').html('');

                var editModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editUserModal'));
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