jQuery(document).ready(function ($) {
    $("#search_certificate_button").on("click", function () {
        let container = $("#cc-search-result");
        container.addClass("row overflow-scroll");

        // Crear tabla, thead y tbody
        let table = $("<table id='principalTable' class='table table-striped'></table>");
        let thead = $("<thead></thead>");
        let tbody = $("<tbody></tbody>");
        table.append(thead);
        table.append(tbody);

        var document = $("#document_text_box").val() ? $("#document_text_box").val() : 0;

        $.ajax({
            type: "POST",
            url: personal_ajax_request.url,
            data: {
                action: "get_certificate",
                nonce: personal_ajax_request.security,
                document: document,
            },
            success: function (response) {
                console.log(response);
                container.empty();
                container.append(table);
                container.show(); 

                if (response.length === 0) {
                    container.append("<div class='alert alert-warning mt-3'>No se encontraron datos para ese número de certificado.</div>");
                    return;
                }

                // Get the keys (column names) of the first object in the JSON data
                let cols = Object.keys(response[0]);

                // Crear header
                let headerRow = $("<tr>");
                $.each(cols, function (i, item) {
                    let th = $("<th>").text(item);
                    headerRow.append(th);
                });
                thead.append(headerRow);

                // Crear cuerpo
                $.each(response, function (i, item) {
                    let row = $("<tr>");
                    let vals = Object.values(item);
                    $.each(vals, function (i, elem) {
                        let td = $("<td>").text(elem);
                        row.append(td);
                    });
                    tbody.append(row);
                });

            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("Status: " + textStatus);
                alert("Error: " + errorThrown);
            }
        });
    });
});
