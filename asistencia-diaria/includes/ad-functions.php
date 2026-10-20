

<?php
// Endpoint AJAX: guarda el estado de una asistencia individual
add_action('wp_ajax_ad_guardar_estado_asistencia', function() {
  if (!current_user_can('manage_options') && !current_user_can('secretario')) {
    wp_send_json_error('No autorizado', 403);
  }
  global $wpdb;
  $asistencia_id = intval($_POST['asistencia_id'] ?? 0);
  $nuevo_estado = $_POST['nuevo_estado'] ?? '';
  $estados_validos = ['asistió','faltó','tarde','justificado'];
  if ($asistencia_id && in_array($nuevo_estado, $estados_validos)) {
    $res = $wpdb->update(
      $wpdb->prefix . 'aess_attendance',
      [ 'status' => $nuevo_estado ],
      [ 'id' => $asistencia_id ]
    );
    if ($res !== false) {
      wp_send_json_success();
      return;
    }
  }
  wp_send_json_error();
});

add_action('admin_menu', 'ad_Add_My_Admin_Link');

function ad_Add_My_Admin_Link()
{
  add_menu_page(
    'Asistencia diaria',               // Título de la página
    'Asistencia diaria',               // Texto en el menú
    'manage_options',                  // Capacidad requerida
    'asistencia-diaria',              // SLUG (clave única)
    'ad_render_admin_page',           // Función callback que imprime la página
    'dashicons-list-view',
    28
  );


}



// Renderiza la página principal del plugin en el admin
function ad_render_admin_page() {
  include plugin_dir_path(__FILE__) . 'dashboard.php';
}

function cargar_bootstrap_en_admin($hook) {
  // Solo carga Bootstrap en la página principal del plugin
    if ($hook !== 'toplevel_page_asistencia-diaria') {
        return;
    }

    wp_enqueue_style(
        'bootstrap-css-admin',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'
    );

    wp_enqueue_script(
        'bootstrap-js-admin',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
        array('jquery'),
        null,
        true
    );
}
add_action('admin_enqueue_scripts', 'cargar_bootstrap_en_admin');
// Endpoint AJAX: obtiene estudiantes filtrados para el histórico
add_action('wp_ajax_ad_get_estudiantes_historico', function() {
  if (!current_user_can('manage_options') && !current_user_can('secretario')) {
    wp_send_json_error('No autorizado', 403);
  }
  global $wpdb;
  $grupo_id = intval($_POST['grupo_id'] ?? 0);
  $filtro_nombre = trim($_POST['filtro_nombre'] ?? '');
  $estudiantes = $wpdb->get_results(
    $wpdb->prepare(
      "SELECT u.id, u.national_id AS cedula, CONCAT(u.first_name, ' ', u.last_name) AS nombre_completo
      FROM {$wpdb->prefix}aess_enrollments e
      JOIN {$wpdb->prefix}aess_users u ON u.id = e.user_id
      WHERE e.group_id = %d AND e.status = 'activo'",
      $grupo_id
    ),
    ARRAY_A
  );
  // Aplica filtro por nombre o cédula si se proporciona
  if ($filtro_nombre !== '') {
    $estudiantes = array_values(array_filter($estudiantes, function($est) use ($filtro_nombre) {
      $busqueda = mb_strtolower($filtro_nombre);
      return strpos(mb_strtolower($est['nombre_completo']), $busqueda) !== false
        || strpos(mb_strtolower($est['cedula']), $busqueda) !== false;
    }));
  }
  wp_send_json_success(['estudiantes' => $estudiantes]);
});

add_action('wp_ajax_ad_get_asistencias_estudiante', function() {
  if (!current_user_can('manage_options') && !current_user_can('secretario')) {
    wp_send_json_error('No autorizado', 403);
  }
  global $wpdb;
  $user_id = intval($_POST['user_id'] ?? 0);
  $grupo_id = intval($_POST['grupo_id'] ?? 0);
  $filtro_anio = trim($_POST['filtro_anio'] ?? '');
  $filtro_mes = trim($_POST['filtro_mes'] ?? '');
  $sql = "SELECT id, attendance_date, status FROM {$wpdb->prefix}aess_attendance WHERE user_id = %d AND group_id = %d";
  $params = [$user_id, $grupo_id];
  if ($filtro_anio) {
    $sql .= " AND YEAR(attendance_date) = %d";
    $params[] = $filtro_anio;
  }
  if ($filtro_mes) {
    $sql .= " AND MONTH(attendance_date) = %d";
    $params[] = intval($filtro_mes);
  }
  $sql .= " ORDER BY attendance_date DESC";
  $asistencias = $wpdb->get_results($wpdb->prepare($sql, ...$params), ARRAY_A);
  wp_send_json_success(['asistencias' => $asistencias]);
});

// Endpoint AJAX: guarda en lote los estados modificados de asistencias
add_action('wp_ajax_ad_guardar_estado_asistencia_lote', function() {
  if (!current_user_can('manage_options') && !current_user_can('secretario')) {
    wp_send_json_error('No autorizado', 403);
  }
  global $wpdb;
  $cambios = isset($_POST['cambios']) ? $_POST['cambios'] : [];
  $estados_validos = ['asistió','faltó','tarde','justificado'];
  $ok = 0;
  if (!is_array($cambios)) {
    wp_send_json_error();
    return;
  }
  foreach ($cambios as $c) {
    $id = intval($c['id'] ?? 0);
    $estado = $c['estado'] ?? '';
    if ($id && in_array($estado, $estados_validos)) {
      $res = $wpdb->update(
        $wpdb->prefix . 'aess_attendance',
        [ 'status' => $estado ],
        [ 'id' => $id ]
      );
      if ($res !== false) $ok++;
    }
  }
  if ($ok > 0) {
    wp_send_json_success();
  } else {
    wp_send_json_error();
  }
});