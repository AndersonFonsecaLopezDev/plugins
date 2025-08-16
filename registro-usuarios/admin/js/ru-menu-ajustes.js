jQuery(document).ready(function($) {
    // Habilitar el botón de importar solo si hay archivo
    $('#excel_file').on('change', function() {
        $('#upload_excel_button').prop('disabled', !this.files.length);
    });

    // Previsualizar el archivo CSV
    $('#preview_excel_button').on('click', function(e) {
        e.preventDefault();
        var fileInput = $('#excel_file')[0];
        if (!fileInput.files.length) {
            $('#upload-response').html('<div class="notice notice-error">Selecciona un archivo primero.</div>');
            return;
        }
        var file = fileInput.files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            var text = e.target.result;
            var rows = text.split(/\r?\n/).filter(function(row) { return row.trim() !== ''; });
            if (rows.length < 2) {
                $('#importedDataTable').html('<div class="notice notice-error">El archivo no tiene datos.</div>');
                return;
            }
            var header = rows[0].split(';');
            var html = '<thead><tr>';
            header.forEach(function(col) { html += '<th>' + col + '</th>'; });
            html += '</tr></thead><tbody>';
            for (var i = 1; i < rows.length; i++) {
                var cols = rows[i].split(';');
                if (cols.length !== header.length) continue; // Saltar filas incompletas
                html += '<tr>';
                cols.forEach(function(col) { html += '<td>' + col + '</td>'; });
                html += '</tr>';
            }
            html += '</tbody>';
            $('#importedDataTable').html(html);
        };
        reader.readAsText(file);
    });

    // Subir el archivo CSV y crear usuarios
    $('#upload_excel_button').on('click', function(e) {
        e.preventDefault();
        var fileInput = $('#excel_file')[0];
        if (!fileInput.files.length) {
            $('#upload-response').html('<div class="notice notice-error">Selecciona un archivo primero.</div>');
            return;
        }
        var formData = new FormData();
        formData.append('action', 'ru_import_users_csv');
        formData.append('nonce', ru_ajax.security);
        formData.append('file', fileInput.files[0]);
        $('#upload_excel_button').prop('disabled', true);
        $('#upload-response').html('<div class="notice notice-info">Procesando archivo...</div>');
        $.ajax({
            url: ru_ajax.url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                $('#upload_excel_button').prop('disabled', false);
                if (res.success) {
                    $('#upload-response').html('<div class="notice notice-success">Usuarios importados: ' + res.data.inserted + '<br>Duplicados: ' + res.data.duplicated + (res.data.errors.length ? '<br>Errores: ' + res.data.errors.join('<br>') : '') + '</div>');
                } else {
                    $('#upload-response').html('<div class="notice notice-error">Error: ' + (res.data && res.data.message ? res.data.message : 'Error desconocido') + '</div>');
                }
            },
            error: function(xhr) {
                $('#upload_excel_button').prop('disabled', false);
                $('#upload-response').html('<div class="notice notice-error">Error de red o servidor.</div>');
            }
        });
    });
});
