<div class="wrap">
    <?php echo '<h1>' . get_admin_page_title() . '</h1>'; ?>
    <p>Listado de cursos vencidos</p>
    
    <div class="table-container" style="width: 100%; overflow-x: auto;">
        <table class="wp-list-table widefat fixed striped" id="principalTable" style="width: 100%; table-layout: auto;">
            <thead>
                <tr>
                    <th style="width: 25%;">NOMBRE</th>
                    <th style="width: 25%;">ULTIMO CURSO REALIZADO</th>
                    <th style="width: 20%;">TÉLEFONO</th>
                    <th style="width: 15%;">FECHA ACTA</th>
                </tr>
            </thead>
            <tbody id="principalBodyTable" class="the-list"></tbody>
            <tfoot>
                <tr>
                    <th>NOMBRE</th>
                    <th>ULTIMO CURSO REALIZADO</th>
                    <th>TÉLEFONO</th>
                    <th>FECHA ACTA</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>