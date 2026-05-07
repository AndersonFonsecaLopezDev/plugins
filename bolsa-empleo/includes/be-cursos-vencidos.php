<?php
if (!current_user_can('access_bolsa') && !current_user_can('manage_options')) {
    wp_die('No tienes permisos para acceder a esta página.');
}
?>
<div class="wrap">
    <div class="card shadow-sm mb-4" style="max-width: 100%;">
        <div class="card-body">
            <h1 class="card-title mb-2">Cursos Vencidos (Más de 1 año)</h1>
            <p class="text-muted mb-4">Listado de estudiantes cuyos cursos se encuentran vencidos según el registro histórico de certificados.</p>
            
            <form id="cursosVencidosFilterForm" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="fecha_desde" class="form-label">Fecha acta desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde">
                </div>
                <div class="col-md-3">
                    <label for="fecha_hasta" class="form-label">Fecha acta hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta">
                </div>
                <div class="col-md-3">
                    <label for="ciudad_filtro" class="form-label">Ciudad</label>
                    <input type="text" class="form-control" id="ciudad_filtro" name="ciudad_filtro" placeholder="Ej: Bogotá">
                </div>
                <div class="col-md-3">
                    <button type="button" id="applyFechaFilter" class="btn btn-primary w-100">Aplicar filtro</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm" style="max-width: 100%;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover display nowrap" id="cursosVencidosTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>NOMBRE COMPLETO</th>
                            <th>CURSO</th>
                            <th>FECHA ACTA</th>
                            <th>FECHA VENCIMIENTO</th>
                            <th>CORREO</th>
                            <th>TELÉFONO</th>
                            <th>CIUDAD</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>NOMBRE COMPLETO</th>
                            <th>CURSO</th>
                            <th>FECHA ACTA</th>
                            <th>FECHA VENCIMIENTO</th>
                            <th>CORREO</th>
                            <th>TELÉFONO</th>
                            <th>CIUDAD</th>
                            <th>ESTADO</th>
                        </tr>
                    </tfoot>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    var table = $('#cursosVencidosTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/2.0.8/i18n/es-MX.json',
        },
        pageLength: 25,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        scrollX: true,
        fixedHeader: {
            header: true
        },
        ajax: {
            url: personal_ajax_request.url,
            type: 'POST',
            data: function (d) {
                d.action = 'get_all_cursos_vencidos';
                d.nonce = personal_ajax_request.ru_security;
                d.fecha_desde = $('#fecha_desde').val();
                d.fecha_hasta = $('#fecha_hasta').val();
                d.ciudad = $('#ciudad_filtro').val();
            },
            dataSrc: ''
        },
        columns: [
            { data: 'nombre' },
            { data: 'curso' },
            { data: 'fecha_acta' },
            { 
                data: 'fecha_vencimiento',
                render: function(data) {
                    return '<strong style="color:red;">' + data + '</strong>';
                }
            },
            { data: 'correo' },
            { data: 'telefono' },
            { data: 'ciudad' },
            { 
                data: 'estado',
                render: function(data) {
                    return "<span class='badge bg-danger'>Vencido</span>";
                }
            }
        ]
    });

    $('#applyFechaFilter').on('click', function () {
        table.ajax.reload();
    });

    // Agregar filtros por columna en el footer
    $('#cursosVencidosTable tfoot th').each(function (i) {
        var title = $(this).text();
        $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Buscar ' + title + '" data-index="' + i + '" />');
    });

    $('#cursosVencidosTable tfoot th input').on('keyup change clear', function () {
        table.column($(this).data('index')).search(this.value).draw();
    });
});
</script>
