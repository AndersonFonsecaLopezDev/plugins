jQuery(document).ready(function ($) {
    $("#search_certificate_button").on("click", function () {
        let container = $("#dataContainer");
        container.addClass("row overflow-scroll");
        let table = $("<table id='principalTable'></table>");
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

                let tbody = $("#principalTable tbody");
                let thead = $("#principalTable thead");

                tbody.empty();
                thead.empty();
                // Create the table element
                table.addClass("table col-12");
                // Get the keys (column names) of the first object in the JSON data
                let cols = Object.keys(response[0]);
                // Create the header element
                let tr = $("<tr>");
                // Loop through the column names and create header cells
                $.each(cols, function (i, item) {
                    let th = $("<th>");
                    th.text(item); // Set the column name as the text of the header cell
                    tr.append(th); // Append the header cell to the header row
                });
                thead.append(tr); // Append the header row to the header
                table.append(tr) // Append the header to the table

                // Loop through the JSON data and create table rows
                $.each(response, function (i, item) {
                    let tr = $("<tr>");
                    // Get the values of the current object in the JSON data
                    let vals = Object.values(item);
                    // Loop through the values and create table cells
                    $.each(vals, (i, elem) => {
                        let td = $("<td>");
                        td.text(elem); // Set the value as the text of the table cell
                        tr.append(td); // Append the table cell to the table row
                    });
                    table.append(tr); // Append the table row to the table
                });
                // container.append(table) // Append the table to the container element
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log("Status: " + textStatus); alert("Error: " + errorThrown);
            }
        });
    });
});