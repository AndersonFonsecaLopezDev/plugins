jQuery(document).ready(function ($) {
    var importedDataTable;
    var previewedData = [];

    function isCSV(fileName) {
        return fileName.toLowerCase().endsWith('.csv');
    }

    // Botón para previsualizar
    $('#preview_excel_button').on('click', function (e) {
        e.preventDefault();

        let fileInput = $('#excel_file')[0];
        if (fileInput.files.length === 0) {
            alert('Por favor selecciona un archivo.');
            return;
        }

        let file = fileInput.files[0];
        let reader = new FileReader();

        reader.onload = function (e) {
            let headers = [];
            let bodyData = [];

            if (isCSV(file.name)) {
                // Procesar CSV
                let text = e.target.result;
                let rows = text.split('\n').map(row => row.split(',').map(cell => cell.trim()));

                if (rows.length > 0) {
                    headers = rows[0];
                    bodyData = rows.slice(1).filter(row => row.length > 1);
                }
            } else {
                // Procesar Excel
                let data = new Uint8Array(e.target.result);
                let workbook = XLSX.read(data, { type: 'array' });

                let firstSheetName = workbook.SheetNames[0];
                let worksheet = workbook.Sheets[firstSheetName];

                let jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                if (jsonData.length > 0) {
                    headers = jsonData[0];
                    bodyData = jsonData.slice(1).filter(row => row.length > 0);
                }
            }

            previewedData = { headers, bodyData };

            if (headers.length > 0) {
                if ($.fn.DataTable.isDataTable('#importedDataTable')) {
                    importedDataTable.destroy();
                    $('#importedDataTable').empty();
                }

                // Crear thead dinámico
                let theadHtml = '<thead><tr>';
                $.each(headers, function (i, header) {
                    theadHtml += '<th>' + header + '</th>';
                });
                theadHtml += '</tr></thead>';

                $('#importedDataTable').html(theadHtml);

                // Crear DataTable
                importedDataTable = $('#importedDataTable').DataTable({
                    data: bodyData,
                    columns: headers.map(() => ({ data: null })),
                    language: {
                        url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/es-MX.json'
                    },
                    scrollX: true
                });

                $('#upload-response').html('<div style="color:green;">✅ Archivo cargado y previsualizado, listo para importar.</div>');
                $('#upload_excel_button').prop('disabled', false);
            } else {
                $('#upload-response').html('<div style="color:red;">❌ El archivo está vacío o no tiene datos válidos.</div>');
            }
        };

        if (isCSV(file.name)) {
            reader.readAsText(file);
        } else {
            reader.readAsArrayBuffer(file);
        }
    });

    // Botón para importar al servidor
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
                $('#upload-response').html('<div style="color:blue;">⏳ Subiendo archivo, por favor espera...</div>');
            },
            success: function (response) {
                if (response.success) {
                    $('#upload-response').html('<div style="color:green;">✅ ' + response.data.message + '</div>');
                    $('#upload_excel_button').prop('disabled', true);
                } else {
                    $('#upload-response').html('<div style="color:red;">❌ ' + response.data.message + '</div>');
                }
            },
            error: function (xhr) {
                $('#upload-response').html('<div style="color:red;">❌ Error en la importación.</div>');
                console.log(xhr.responseText);
            }
        });
    });
});
