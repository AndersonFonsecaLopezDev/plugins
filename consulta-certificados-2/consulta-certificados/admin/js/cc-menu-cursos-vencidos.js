jQuery(document).ready(function ($) {
    // let table = new DataTable('#principalTable');
    var table = $('#principalTable').DataTable({
        ajax: {
            type: "POST",
            url: personal_ajax_request.url,
            data: {
                action: "get_all_cursos_vencidos",
                nonce: personal_ajax_request.security,
            },
            dataSrc: ""
        },
        columns: [
            { data: 'nombre', title: 'NOMBRE' },
            { data: 'fecha_acta', title: 'TELÉFONO' },
            { data: 'curso', title: 'ULTIMO CURSO REALIZADO' }
           
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/es-MX.json',
        },
         // fixedColumns: true,
         scrollX: true,
         fixedHeader: {
             header: true,
         }, layout: {
            topStart: {
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        download: 'open',
                        customize: function (doc, dt, node, config) { // set the cell colours for the PDF
                            // console.log(doc); // useful if you want to see what the doc object contains
                            let table = doc.content[1].table.body;
                            let data;
                            for (i = 0; i < table.length; i++) // skip table header row (i = 0)
                            {
                                for (let j = 0; j < table[i].length; j++) {
                                    data += table[i][j].text; // couldn't be bothered clearing the Age column sample data above :)
                                }
                            }
                        }
                    },
                    'copy',
                    'colvis',
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

});