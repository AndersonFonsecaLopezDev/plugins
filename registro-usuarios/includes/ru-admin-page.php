<?php
// Registrar scripts y estilos solo en la página admin de usuarios
add_action('admin_enqueue_scripts', function($hook) {
    if (strpos($hook, 'registro-usuarios') !== false) {
        wp_enqueue_style('ru_bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
        wp_enqueue_style('ru_datatables', 'https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css');
        wp_enqueue_script('ru_jquery', 'https://code.jquery.com/jquery-3.7.1.min.js');
        wp_enqueue_script('ru_datatables', 'https://cdn.datatables.net/2.0.8/js/jquery.dataTables.min.js', ['ru_jquery']);
        wp_enqueue_script('ru_datatables_bs', 'https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js', ['ru_datatables']);
        wp_enqueue_script('ru_admin_page', plugins_url('../admin/js/ru-admin-page.js', __FILE__), ['ru_datatables_bs'], false, true);
        wp_localize_script('ru_admin_page', 'ru_ajax', [
            'url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('ru_seg')
        ]);
    }
});

// Endpoint AJAX para devolver usuarios
add_action('wp_ajax_get_all_users', function() {
    check_ajax_referer('ru_seg', 'nonce');
    global $wpdb;
    $usuarios = $wpdb->get_results("SELECT last_name, second_last_name, first_name, middle_name, national_id FROM {$wpdb->prefix}aess_users", ARRAY_A);
    wp_send_json($usuarios);
});
?>
<div class="wrap">
  <?php
  echo '
    <h1>' . get_admin_page_title() . '</h1>
  ';
  ?>
  <p>Listado de usuarios registrados</p>
  <table class="table table-striped wp-list-table widefat" id="principalTable">
    <thead>
      <th>APELLIDO1</th>
      <th>APELLIDO2</th>
      <th>NOMBRE1</th>
      <th>NOMBRE2</th>
      <th>CEDULA</th>
    </thead>
    <tbody id="principalBodyTable" class="the-list">
      <!-- Los datos se cargarán por AJAX -->
    </tbody>
    <tfoot>
      <th>APELLIDO1</th>
      <th>APELLIDO2</th>
      <th>NOMBRE1</th>
      <th>NOMBRE2</th>
      <th>CEDULA</th>
    </tfoot>
  </table>
</div>