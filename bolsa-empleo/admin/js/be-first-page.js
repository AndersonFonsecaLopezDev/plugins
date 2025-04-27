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
            { data: 'ID' }, // Reservado para el ID
            { data: 'Nombres' },
            { data: 'Apellidos' },
            { data: 'Género' },
            { data: 'Edad' },
            { data: 'Ciudad' },
            { data: 'Celular' },
            { data: 'Correo' },
            { data: 'Académico' },
            { data: 'Profesion' },
            { data: 'Estado' },
            {data: 'Libreta_militar'},
            { data: 'Experiencia' },
            { data: 'Diploma' },
            { data: 'Estatura' },
            { data: 'Barrio' },
            { data: 'Dirección' },
            { data: 'Departamento' },
            { data: 'Teléfono' },
            { data: 'Cédula' },

        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/es-MX.json',
        },
        scrollX: true,
        fixedColumns: true,
        fixedHeader: {
            header: true,
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

});