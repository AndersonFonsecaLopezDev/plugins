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
                $('#upload-response').html(`
                    <div style="color:blue; display:flex; align-items:center; gap:8px;">
                        <div class="spinner" style="border: 4px solid #ccc; border-top: 4px solid blue; border-radius: 50%; width: 20px; height: 20px; animation: spin 1s linear infinite;"></div>
                        <span>Cargando archivo y procesando datos...</span>
                    </div>
                    <style>
                        @keyframes spin {
                            0% { transform: rotate(0deg); }
                            100% { transform: rotate(360deg); }
                        }
                    </style>
                `);
            },
            success: function (response) {
                if (response.success) {
                    $('#upload-response').html('<div style="color:green;">✅ ' + response.data.message + '</div>');

                    if (response.data.rows && response.data.rows.length > 0) {
                        // Si ya existe un datatable, destruirlo primero
                        if ($.fn.DataTable.isDataTable('#importedDataTable')) {
                            importedDataTable.destroy();
                            $('#importedDataTable').empty(); // Vaciar tabla
                        }

                        // Crear encabezados dinámicamente
                        let cols = Object.keys(response.data.rows[0]);
                        let theadHtml = '<thead><tr>';
                        $.each(cols, function (i, col) {
                            theadHtml += '<th>' + col + '</th>';
                        });
                        theadHtml += '</tr></thead>';

                        // Agregar también un tbody vacío para que DataTable lo llene
                        $('#importedDataTable').html(theadHtml + '<tbody></tbody>');

                        // Crear Datatable
                        importedDataTable = $('#importedDataTable').DataTable({
                            data: response.data.rows,
                            columns: cols.map(col => ({ data: col })),
                            language: {
                                url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/es-MX.json'
                            },
                            scrollX: true,
                            pageLength: 10,
                            responsive: true
                        });
                    }

                } else {
                    $('#upload-response').html('<div style="color:red;">❌ ' + response.data.message + '</div>');
                }
            },
            error: function (xhr) {
                $('#upload-response').html('<div style="color:red;">❌ Error en la importación</div>');
                console.error(xhr.responseText);
            }
        });
    });
});
