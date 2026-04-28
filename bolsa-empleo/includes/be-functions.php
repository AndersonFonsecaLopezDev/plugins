<?php
/*
 * Add my new menu to the Admin Control Panel
 */
// Hook the 'admin_menu' action hook, run the function named 'cc_Add_My_Admin_Link()'
add_action('admin_menu', 'be_Add_My_Admin_Link');
// Add a new top level menu link to the ACP
function be_Add_My_Admin_Link()
{
  // Agregar el menu principal
  add_menu_page(
    'Bolsa de empleo', // Titulo de la pagina
    'Bolsa de empleo', // Texto para mostrar en el menu
    'manage_options', // Capability requirement to see the link
    plugin_dir_path(__FILE__) . 'be-first-page.php', // The 'slug' - file to display when clicking the link
    null, // Funcion del sitio
    plugin_dir_url(__FILE__) . '../admin/img/icon.png', // Icono del menu
    1
  );
  add_submenu_page(
    plugin_dir_path(__FILE__) . 'be-first-page.php', // Parent slug
    'Ajustes', // Titulo de la pagina
    'Ajustes', // Titulo del menu
    'manage_options', // Capability requirement to see the link
    'be-menu-ajustes', // slug
    'be_render_ajustes'
  );
  
  // Agregar submenu de Cursos Vencidos (solo para secretario y admin)
  add_submenu_page(
    plugin_dir_path(__FILE__) . 'be-first-page.php',
    'Cursos Vencidos',
    'Cursos Vencidos',
    'secretario', // Capability
    'be-cursos-vencidos',
    'be_render_cursos_vencidos'
  );
}

function be_render_cursos_vencidos() {
    include_once plugin_dir_path(__FILE__) . 'be-cursos-vencidos.php';
}

function be_render_ajustes() {
    include_once plugin_dir_path(__FILE__) . 'be-menu-ajustes.php';
}

function be_register_settings() {
    register_setting('be_settings_group', 'be_login_redirect_url');
    register_setting('be_settings_group', 'be_register_url');
    register_setting('be_settings_group', 'be_forgot_password_url');
    register_setting('be_settings_group', 'be_terms_url');
}
add_action('admin_init', 'be_register_settings');

// Cargar JS de Bootstrap
function beLoadBootstrapJS($hook)
{
  if ($hook != "bolsa-empleo/includes/be-first-page.php") {
    return;
  }
  wp_enqueue_script('be_bootstrap_js', plugins_url('../admin/bootstrap/js/bootstrap.min.js', __FILE__), array('jquery'));
}

add_action('admin_enqueue_scripts', 'beLoadBootstrapJS');

// Cargar CSS de Bootstrap
function beLoadBootstrapCSS($hook)
{
  if ($hook != "bolsa-empleo/includes/be-first-page.php") {
    return;
  }
  wp_enqueue_style('be_bootstrap_css', plugins_url('../admin/bootstrap/css/bootstrap.min.css', __FILE__));
}

add_action('admin_enqueue_scripts', 'beLoadBootstrapCSS');

// Cargar JS de dataTables
function beLoadDataTablesJS($hook)
{
  if ($hook != "bolsa-empleo/includes/be-first-page.php") {
    return;
  }
  wp_enqueue_script('dataTables_js', plugins_url('../admin/js/datatables.min.js', __FILE__), array('jquery'));
}

add_action('admin_enqueue_scripts', 'beLoadDataTablesJS');

// Cargar CSS de dataTables
function beLoadDataTablesCSS($hook)
{
  if ($hook != "bolsa-empleo/includes/be-first-page.php") {
    return;
  }
  wp_enqueue_style('dataTables_css', plugins_url('../admin/css/datatables.min.css', __FILE__));
}

add_action('admin_enqueue_scripts', 'beLoadDataTablesCSS');

// Cargar JS personalizado
function beLoadJS($hook)
{
  if ($hook != "bolsa-empleo/includes/be-first-page.php") {
    return;
  }
  wp_enqueue_script('be_personal_js', plugins_url('../admin/js/be-first-page.js', __FILE__));
  wp_localize_script('be_personal_js', 'personal_ajax_request', [
    'url' => admin_url('admin-ajax.php'),
    'security' => wp_create_nonce('seg'),
    'is_secretario' => current_user_can('secretario') || current_user_can('manage_options')
  ]);
}

add_action('admin_enqueue_scripts', 'beLoadJS');

// Cargar CSS personalizado
function beLoadCSS($hook)
{
  if ($hook != "bolsa-empleo/includes/be-first-page.php") {
    return;
  }
  wp_enqueue_style('be_personal_css', plugins_url('../admin/css/styles.css', __FILE__));
}


add_action('admin_enqueue_scripts', 'beLoadCSS');

// Ajax
function beGetCv() {
  $nonce = $_POST['nonce'];
  // Verificacion de seguridad
  if (!wp_verify_nonce($nonce, 'seg')) {
    die('No tiene permisos para ejecutar');
  }
  global $wpdb;

  $table = $wpdb->prefix . 'aess_users';
  
  // ASEGURAR QUE LAS COLUMNAS EXISTAN (Por si no se reactivó el plugin)
  $required_columns = ['academico' => 'VARCHAR(255)', 'profesion' => 'VARCHAR(255)', 'estado_laboral' => 'VARCHAR(100)', 'libreta_militar' => 'VARCHAR(100)', 'experiencia' => 'TEXT', 'diploma' => 'VARCHAR(255)', 'estatura' => 'VARCHAR(50)', 'barrio' => 'VARCHAR(255)', 'cv_aprobado' => 'TINYINT(1) DEFAULT 0'];
  foreach ($required_columns as $col => $def) {
      if (empty($wpdb->get_results("SHOW COLUMNS FROM {$table} LIKE '{$col}'"))) {
          $wpdb->query("ALTER TABLE {$table} ADD COLUMN {$col} {$def}");
      }
  }

  // Condicional de rol: si es talento_humano, solo ve los aprobados.
  $where_clause = "";
  if (current_user_can('talento_humano') && !current_user_can('secretario')) {
      $where_clause = " WHERE cv_aprobado = 1";
  }
  
  $sql = "SELECT 
    ID,
    first_name AS 'Nombres',
    last_name AS 'Apellidos',
    gender AS 'Genero',
    birth_date AS 'Edad',
    city AS 'Ciudad',
    phone_number AS 'Celular',
    email AS 'Correo',
    academico AS 'Academico',
    profesion AS 'Profesion',
    estado_laboral AS 'Estado',
    libreta_militar AS 'Libreta_militar',
    experiencia AS 'Experiencia',
    diploma AS 'Diploma',
    estatura AS 'Estatura',
    barrio AS 'Barrio',
    address AS 'Direccion',
    departamento AS 'Departamento',
    national_id AS 'Cedula',
    cv_aprobado AS 'Aprobado'
FROM $table $where_clause;";

  $response = $wpdb->get_results($sql);
  
  if ($wpdb->last_error) {
      wp_send_json([['Nombres' => 'Error SQL: ' . $wpdb->last_error]]);
  }

  wp_send_json($response);
}

add_action('wp_ajax_get_cvs', 'beGetCv');

// Shortcode para la hoja de vida del estudiante
function be_student_cv_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'be-student-cv.php';
    return ob_get_clean();
}
add_shortcode('mi_hoja_de_vida', 'be_student_cv_shortcode');

// Action para aprobar/rechazar CV por el Secretario
function be_approve_cv() {
    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce, 'seg') || !current_user_can('secretario')) {
        wp_send_json_error('No tiene permisos para ejecutar esta acción.');
    }
    
    global $wpdb;
    $id = intval($_POST['id']);
    $status = intval($_POST['status']);
    
    $table = $wpdb->prefix . 'aess_users';
    $result = $wpdb->update($table, ['cv_aprobado' => $status], ['ID' => $id]);
    
    if ($result !== false) {
        wp_send_json_success('Estado actualizado.');
    } else {
        wp_send_json_error('Error al actualizar.');
    }
}
add_action('wp_ajax_approve_cv', 'be_approve_cv');

