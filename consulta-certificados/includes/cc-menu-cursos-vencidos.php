<div class="wrap">
    <?php echo '<h1>' . get_admin_page_title() . '</h1>'; ?>
    <p>Listado de cursos vencidos</p>
    <table class="table table-striped wp-list-table widefat col-12 bg-dark" id="principalTable">
        <thead>
            <tr>
                <th>NOMBRE</th>
                <th>TELÉFONO</th>
                <th>ULTIMO CURSO REALIZADO</th>
                <th>FECHA ACTA</th>
            </tr>
        </thead>
        <tbody id="principalBodyTable" class="the-list"></tbody>
        <tfoot>
            <tr>
                <th>NOMBRE</th>
                <th>TELÉFONO</th>
                <th>ULTIMO CURSO REALIZADO</th>
                <th>FECHA ACTA</th>
            </tr>
        </tfoot>
    </table>
</div>
