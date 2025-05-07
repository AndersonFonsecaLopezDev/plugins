<div class="wrap">
    <?php echo '<h1>' . get_admin_page_title() . '</h1>'; ?>
    <p>Listado de cursos vencidos</p>
    <table class="table table-striped wp-list-table widefat" id="principalTable">
        <thead>
            <tr>
                <th>NOMBRE</th>
                <th>TELÉFONO</th>
                <th>ULTIMO CURSO REALIZADO</th>
            </tr>
        </thead>
        <tbody id="principalBodyTable" class="the-list"></tbody>
        <tfoot>
            <tr>
                <th>NOMBRE</th>
                <th>TELÉFONO</th>
                <th>ULTIMO CURSO REALIZADO</th>
            </tr>
        </tfoot>
    </table>
</div>
