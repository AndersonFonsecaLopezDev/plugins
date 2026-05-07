jQuery(document).ready(function ($) {
    var importedDataTable;
    var previewedData = [];

    function isCSV(fileName) {
        return fileName.toLowerCase().endsWith('.csv');
    }

    function setResponseMessage(html, isError = false) {
        $('#upload-response').html('<div style="color:' + (isError ? '#d63638' : '#007017') + '; font-weight: 500;">' + html + '</div>');
    }

    function resetPreview() {
        if ($.fn.DataTable.isDataTable('#importedDataTable')) {
            importedDataTable.destroy();
            $('#importedDataTable').empty();
        }
        $('#upload_excel_button').prop('disabled', true);
        $('#upload-response').empty();
        $('#progress-container').hide();
        $('#upload-progress-bar').css('width', '0%').text('0%').css('background-color', '#2271b1');
    }

    $('#excel_file').on('change', function () {
        resetPreview();
    });

    // Botón para previsualizar
    $('#preview_excel_button').on('click', function (e) {
        e.preventDefault();

        let fileInput = $('#excel_file')[0];
        if (fileInput.files.length === 0) {
            setResponseMessage('Por favor selecciona un archivo CSV o Excel antes de previsualizar.', true);
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
            setResponseMessage('Debes seleccionar primero un archivo antes de importar.', true);
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
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                        $('#progress-container').show();
                        $('#upload-progress-bar').css('width', percentComplete + '%').text(percentComplete + '%');
                        if (percentComplete === 100) {
                            $('#upload-response').html('<div style="color:#2271b1; font-weight: 500;">⏳ Archivo subido. Procesando e importando datos en el servidor, esto puede tomar unos minutos...</div>');
                            $('#upload-progress-bar').css('background-color', '#ffb900'); // Cambiar a naranja para indicar procesamiento
                        }
                    }
                }, false);
                return xhr;
            },
            beforeSend: function () {
                $('#upload-response').html('<div style="color:#2271b1; font-weight: 500;">⏳ Iniciando subida del archivo...</div>');
                $('#progress-container').show();
                $('#upload-progress-bar').css('width', '0%').text('0%').css('background-color', '#2271b1');
            },
            success: function (response) {
                $('#progress-container').hide();
                if (response.success) {
                    let msg = response.data.message || 'Importación completada con éxito.';
                    let details = '';
                    if (response.data.inserted !== undefined) details += '<br><b>Nuevos insertados:</b> ' + response.data.inserted;
                    if (response.data.updated !== undefined) details += '<br><b>Actualizados:</b> ' + response.data.updated;
                    if (response.data.errors && response.data.errors.length > 0) {
                        details += '<br><br><b style="color:#d63638;">Detalles de errores/advertencias:</b><br><small style="color:#d63638; display:block; max-height:150px; overflow-y:auto; background:#fbeaea; padding:10px; border-radius:4px; border:1px solid #f2cdd0; text-align:left;">' + response.data.errors.join('<br>') + '</small>';
                    }
                    $('#upload-response').html('<div style="color:#007017; font-weight: 500;">✅ ' + msg + details + '</div>');
                    $('#upload_excel_button').prop('disabled', true);
                } else {
                    let errorMsg = response.data.message || response.data || 'Error desconocido al importar.';
                    let details = '';
                    if (response.data.errors && response.data.errors.length > 0) {
                        details = '<br><br><b style="color:#d63638;">Detalles del error:</b><br><small style="color:#d63638; display:block; max-height:150px; overflow-y:auto; background:#fbeaea; padding:10px; border-radius:4px; border:1px solid #f2cdd0; text-align:left;">' + response.data.errors.join('<br>') + '</small>';
                    }
                    $('#upload-response').html('<div style="color:#d63638; font-weight: 500;">❌ ' + errorMsg + details + '</div>');
                }
            },
            error: function (xhr) {
                $('#progress-container').hide();
                let errorMsg = 'Error grave en la importación (Fallo del servidor o archivo muy pesado). ';
                let details = '';
                if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                    details = xhr.responseJSON.data.message;
                } else if (xhr.responseText) {
                    details = xhr.responseText;
                } else if (xhr.statusText) {
                    details = xhr.statusText;
                }

                if (details) {
                    errorMsg += details;
                }

                $('#upload-response').html('<div style="color:#d63638; font-weight: 500;">❌ ' + errorMsg + '</div>');
                console.log('Import error details:', details || xhr);
            }
        });
    });
});
