jQuery(document).ready(function ($) {
    var table = $('#principalTable').DataTable({
        ajax: {
            type: 'POST',
            url: ru_ajax.url,
            data: {
                action: 'get_all_users',
                nonce: ru_ajax.security,
            },
            dataSrc: ''
        },
        columns: [
            { data: 'last_name', title: 'APELLIDO1' },
            { data: 'second_last_name', title: 'APELLIDO2' },
            { data: 'first_name', title: 'NOMBRE1' },
            { data: 'middle_name', title: 'NOMBRE2' },
            { data: 'national_id', title: 'CEDULA' },
            { data: 'email', title: 'EMAIL' },
            { data: 'phone_number', title: 'TELÉFONO' },
            { data: 'user_role', title: 'ROL' },
            {
                data: null,
                title: 'ACCIONES',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<button type="button" class="btn btn-sm btn-primary edit-user-button" data-user-id="' + row.ID + '"><i class="fa fa-edit"></i> Editar</button>';
                }
            }
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

    $('#principalTable tfoot th').each(function (i) {
        var title = $('#principalTable thead th').eq($(this).index()).text();
        if (title !== 'ACCIONES') {
            $(this).html('<input type="text" class="form-control form-control-sm" placeholder="' + title + '" data-index="' + i + '" />');
        }
    });

    $('#principalTable tfoot th input').on('keyup change clear', function () {
        table.column($(this).data('index')).search(this.value).draw();
    });

    $('#principalTable tbody').on('click', 'button.edit-user-button', function () {
        var userId = $(this).data('user-id');
        loadUserForEdit(userId);
    });

    function loadUserForEdit(userId) {
        $.post(ru_ajax.url, {
            action: 'get_user_details',
            nonce: ru_ajax.security,
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
        formData.push({ name: 'nonce', value: ru_ajax.security });

        $.post(ru_ajax.url, formData, function (response) {
            if (response.success) {
                $('#editUserFeedback').html('<div class="alert alert-success">' + response.data + '</div>');
                table.ajax.reload(null, false);
            } else {
                $('#editUserFeedback').html('<div class="alert alert-danger">' + (response.data || 'Error al actualizar el usuario.') + '</div>');
            }
        }, 'json');
    });
});
