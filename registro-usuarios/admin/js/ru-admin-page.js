jQuery(document).ready(function ($) {
    var table = $('#principalTable').DataTable({
        ajax: {
            type: "POST",
            url: ru_ajax.url,
            data: {
                action: "get_all_users",
                nonce: ru_ajax.security,
            },
            dataSrc: ""
        },
        columns: [
            { data: 'last_name' },
            { data: 'second_last_name' },
            { data: 'first_name' },
            { data: 'middle_name' },
            { data: 'national_id' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/es-MX.json',
        },
        scrollX: true,
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
                            columns: ':visible',
                            modifier: {
                                search: 'applied',
                                order: 'applied',
                                page: 'all'
                            }
                        },
                        filename: 'listado_usuarios',
                        title: null
                    }
                ]
            }
        },
        select: true
    });

    // Filtros en el footer
    $('#principalTable tfoot th').each(function (i) {
        var title = $('#principalTable thead th').eq($(this).index()).text();
        $(this).html('<input type="text" placeholder="' + title + '" data-index="' + i + '" />');
    });

    $('#principalTable tfoot th input').on('keyup', function () {
        table.column($(this).data('index')).search(this.value).draw();
    });
});
