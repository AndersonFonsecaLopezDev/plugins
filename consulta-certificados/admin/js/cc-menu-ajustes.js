jQuery(document).ready(function ($) {
    var importedDataTable;

    $('#upload_excel_button').on('click', function (e) {
        e.preventDefault();

        let fileInput = $('#excel_file')[0];
        if (fileInput.files.length === 0) {
            alert('Por favor selecciona un archivo.');
            return;
        }

        let formData = new FormData();
        formData.append('action', 'import_excel');
        formData.append('nonce', personal_ajax_request.security);
        formData.append('excel_file', fileInput.files[0]);

        $.ajax({
            url: personal_ajax_request.url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('#upload-response').html('<div style="color:blue;">⏳ Cargando archivo y procesando datos, por favor espera...</div>');
            },
            success: function (response) {
                if (response.success) {
                    $('#upload-response').html('<div style="color:green;">✅ ' + response.data.message + '</div>');

                    if (response.data.rows && response.data.rows.length > 0) {
                        // Si ya existe un datatable, destruirlo primero
                        if ($.fn.DataTable.isDataTable('#importedDataTable')) {
                            importedDataTable.destroy();
                            $('#importedDataTable').empty(); // Vaciar
                        }

                        // Crear encabezados dinámicamente
                        let cols = Object.keys(response.data.rows[0]);
                        let theadHtml = '<thead><tr>';
                        $.each(cols, function (i, col) {
                            theadHtml += '<th>' + col + '</th>';
                        });
                        theadHtml += '</tr></thead>';

                        $('#importedDataTable').html(theadHtml);

                        // Crear Datatable
                        importedDataTable = $('#importedDataTable').DataTable({
                            data: response.data.rows,
                            columns: cols.map(col => ({ data: col })),
                            language: {
                                url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/es-MX.json'
                            },
                            scrollX: true,
                        });
                    }

                } else {
                    $('#upload-response').html('<div style="color:red;">❌ ' + response.data.message + '</div>');
                }
            },
            error: function (xhr) {
                $('#upload-response').html('<div style="color:red;">❌ Error en la importación</div>');
                console.log(xhr.responseText);
            }
        });
    });
});
