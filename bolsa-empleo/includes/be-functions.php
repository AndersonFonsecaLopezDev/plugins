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
    'access_bolsa', // Capability requirement to see the link
    plugin_dir_path(__FILE__) . 'be-first-page.php', // The 'slug' - file to display when clicking the link
    null, // Funcion del sitio
    plugin_dir_url(__FILE__) . '../admin/img/icon.png', // Icono del menu
    1
  );
  // Agregar submenu de Cursos Vencidos (solo para secretario y admin)
  add_submenu_page(
    plugin_dir_path(__FILE__) . 'be-first-page.php',
    'Cursos Vencidos',
    'Cursos Vencidos',
    'access_bolsa', // Capability
    'be-cursos-vencidos',
    'be_render_cursos_vencidos'
  );
  add_submenu_page(
    plugin_dir_path(__FILE__) . 'be-first-page.php', // Parent slug
    'Ajustes', // Titulo de la pagina
    'Ajustes', // Titulo del menu
    'manage_options', // Capability requirement to see the link
    'be-menu-ajustes', // slug
    'be_render_ajustes'
  );
}

function be_render_cursos_vencidos() {
    include_once plugin_dir_path(__FILE__) . 'be-cursos-vencidos.php';
}

function be_render_ajustes() {
    include_once plugin_dir_path(__FILE__) . 'be-menu-ajustes.php';
}

function be_register_settings() {
    $settings = [
        'be_login_redirect_url' => 'esc_url_raw',
        'be_login_url' => 'esc_url_raw',
        'be_register_url' => 'esc_url_raw',
        'be_forgot_password_url' => 'esc_url_raw',
        'be_terms_url' => 'esc_url_raw',
        'be_email_remitente' => 'sanitize_email',
        'be_email_password' => 'sanitize_text_field',
        'be_email_nombre_remitente' => 'sanitize_text_field',
        'be_access_bolsa_estudiante' => 'absint',
        'be_access_bolsa_secretario' => 'absint',
        'be_access_bolsa_talento_humano' => 'absint',
        'be_cursos_disponibles' => 'sanitize_text_field',
        'be_ciclos_disponibles' => 'sanitize_text_field',
        'be_profesiones_disponibles' => 'sanitize_text_field',
        'be_politicas' => 'sanitize_textarea_field',
    ];

    foreach ($settings as $option => $callback) {
        register_setting('be_settings_group', $option, [
            'sanitize_callback' => $callback,
        ]);
    }
}
add_action('admin_init', 'be_register_settings');

function be_maybe_upgrade_database() {
    $current_version = get_option('be_db_version', '1.0');
    if (version_compare($current_version, '1.2', '<')) {
        global $wpdb;
        $table = $wpdb->prefix . 'aess_users';
        
        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
        if ($table_exists === $table) {
            $required_columns = [
                'academico' => 'VARCHAR(255) DEFAULT \'\'',
                'profesion' => 'VARCHAR(255) DEFAULT \'\'',
                'estado_laboral' => 'VARCHAR(100) DEFAULT \'\'',
                'libreta_militar' => 'VARCHAR(100) DEFAULT \'\'',
                'experiencia' => 'TEXT',
                'diploma' => 'VARCHAR(255) DEFAULT \'\'',
                'estatura' => 'VARCHAR(50) DEFAULT \'\'',
                'barrio' => 'VARCHAR(255) DEFAULT \'\'',
                'cv_aprobado' => 'TINYINT(1) DEFAULT 0',
                'cv_file' => 'VARCHAR(255) DEFAULT \'\'',
                'curso_realizado' => 'VARCHAR(200) DEFAULT \'\' NOT NULL',
                'ciclo_curso' => 'VARCHAR(100) DEFAULT \'\' NOT NULL',
                'fecha_acta_curso' => 'DATE DEFAULT NULL',
                'fecha_vencimiento_curso' => 'DATE DEFAULT NULL',
                'modalidad' => 'VARCHAR(50) DEFAULT \'\' NOT NULL',
                'sede' => 'VARCHAR(150) DEFAULT \'\' NOT NULL',
            ];

            $existing_columns = $wpdb->get_col("SHOW COLUMNS FROM {$table}");
            if (!empty($existing_columns)) {
                foreach ($required_columns as $col => $def) {
                    if (!in_array($col, $existing_columns, true)) {
                        $wpdb->query("ALTER TABLE {$table} ADD COLUMN {$col} {$def}");
                    }
                }
            }
        }
        update_option('be_db_version', '1.2');
    }
}
add_action('admin_init', 'be_maybe_upgrade_database');
add_action('init', 'be_maybe_upgrade_database');

function be_setup_plugin_defaults() {
    // Asegurar que los roles siempre existan.
    $roles = [
        'estudiante' => 'Estudiante',
        'secretario' => 'Secretario',
        'talento_humano' => 'Talento Humano',
    ];

    foreach ($roles as $role => $name) {
        $capabilities = [
            'read' => true,
            $role => true,
            'access_bolsa' => true,
        ];

        $existing_role = get_role($role);
        if (null === $existing_role) {
            add_role($role, $name, $capabilities);
        } else {
            foreach ($capabilities as $cap => $value) {
                if (!$existing_role->has_cap($cap)) {
                    $existing_role->add_cap($cap, $value);
                }
            }
        }
    }

    $admin_role = get_role('administrator');
    if ($admin_role) {
        $admin_role->add_cap('access_bolsa');
        $admin_role->add_cap('secretario');
        $admin_role->add_cap('talento_humano');
        $admin_role->add_cap('estudiante');
    }

    $defaults = [
        'be_login_redirect_url' => home_url('/'),
        'be_login_url' => site_url('/pagina-login'),
        'be_register_url' => site_url('/pagina-registro'),
        'be_forgot_password_url' => site_url('/recuperacion-contasena'),
        'be_terms_url' => '#',
        'be_email_remitente' => 'juansepulveda8010@gmail.com',
        'be_email_password' => 'ukwv yeqq gphc jzkk',
        'be_email_nombre_remitente' => 'Academia Aess',
        'be_access_bolsa_estudiante' => 1,
        'be_access_bolsa_secretario' => 1,
        'be_access_bolsa_talento_humano' => 1,
        'be_cursos_disponibles' => 'Vigilancia, Supervisor, Escoltas, Medios tecnológicos, Polígono',
        'be_ciclos_disponibles' => 'Fundamentación, Reentrenamiento, Especialización, Profundización, Polígono',
    ];

    foreach ($defaults as $name => $value) {
        if (get_option($name) === false) {
            add_option($name, $value);
        }
    }

    // Actualizar cursos si tenían el valor antiguo
    $curr_cursos = get_option('be_cursos_disponibles');
    if (empty($curr_cursos) || $curr_cursos === 'Vigilante, Escolta, Supervisor, Operador de Medios Tecnológicos') {
        update_option('be_cursos_disponibles', 'Vigilancia, Supervisor, Escoltas, Medios tecnológicos, Polígono');
    }
    if (get_option('be_ciclos_disponibles') === false) {
        update_option('be_ciclos_disponibles', 'Fundamentación, Reentrenamiento, Especialización, Profundización, Polígono');
    }
}
add_action('init', 'be_setup_plugin_defaults');

function be_get_current_custom_role() {
    if (!is_user_logged_in()) {
        return null;
    }

    $current_user = wp_get_current_user();
    if (empty($current_user->user_email)) {
        return null;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'aess_users';
    $role = strtolower(trim($wpdb->get_var($wpdb->prepare("SELECT user_role FROM {$table} WHERE email = %s", $current_user->user_email))));

    if (!empty($role)) {
        return $role;
    }

    if (in_array('secretario', (array) $current_user->roles, true)) {
        return 'secretario';
    }
    if (in_array('talento_humano', (array) $current_user->roles, true)) {
        return 'talento_humano';
    }
    if (in_array('administrator', (array) $current_user->roles, true) || current_user_can('manage_options')) {
        return 'administrator';
    }

    if (in_array('estudiante', (array) $current_user->roles, true) || in_array('subscriber', (array) $current_user->roles, true) || current_user_can('read')) {
        return 'estudiante';
    }

    // Devolver el primer rol de WP si existe.
    if (!empty($current_user->roles)) {
        return strtolower($current_user->roles[0]);
    }

    return 'estudiante';
}

function be_current_user_can_access_feature($feature) {
    if (current_user_can('manage_options')) {
        return true;
    }

    $role = be_get_current_custom_role();
    if (!$role) {
        return false;
    }

    if ($role === 'administrator') {
        return true;
    }

    $option_name = 'be_access_' . $feature . '_' . $role;
    return (bool) get_option($option_name, 1);
}

function be_current_user_can_access_bolsa() {
    if (!is_user_logged_in()) {
        return false;
    }

    $role = be_get_current_custom_role();
    if ($role === 'administrator') {
        return true;
    }

    return be_current_user_can_access_feature('bolsa');
}

function be_is_bolsa_admin_page($hook)
{
  return strpos($hook, 'be-') !== false || strpos($hook, 'bolsa-empleo') !== false;
}

// Cargar JS de Bootstrap
function beLoadBootstrapJS($hook)
{
  if (!be_is_bolsa_admin_page($hook)) {
    return;
  }
  wp_enqueue_script('be_bootstrap_js', plugins_url('../admin/bootstrap/js/bootstrap.min.js', __FILE__), array('jquery'));
}

add_action('admin_enqueue_scripts', 'beLoadBootstrapJS');

// Cargar CSS de Bootstrap
function beLoadBootstrapCSS($hook)
{
  if (!be_is_bolsa_admin_page($hook)) {
    return;
  }
  wp_enqueue_style('be_bootstrap_css', plugins_url('../admin/bootstrap/css/bootstrap.min.css', __FILE__));
}

add_action('admin_enqueue_scripts', 'beLoadBootstrapCSS');

// Cargar JS de dataTables
function beLoadDataTablesJS($hook)
{
  if (!be_is_bolsa_admin_page($hook)) {
    return;
  }
  wp_enqueue_script('dataTables_js', plugins_url('../admin/js/datatables.min.js', __FILE__), array('jquery'));
}

add_action('admin_enqueue_scripts', 'beLoadDataTablesJS');

// Cargar CSS de dataTables
function beLoadDataTablesCSS($hook)
{
  if (!be_is_bolsa_admin_page($hook)) {
    return;
  }
  wp_enqueue_style('dataTables_css', plugins_url('../admin/css/datatables.min.css', __FILE__));
}

add_action('admin_enqueue_scripts', 'beLoadDataTablesCSS');

// Cargar JS personalizado
function beLoadJS($hook)
{
  if (!be_is_bolsa_admin_page($hook)) {
    return;
  }
  wp_enqueue_script('be_personal_js', plugins_url('../admin/js/be-first-page.js', __FILE__));
  wp_localize_script('be_personal_js', 'personal_ajax_request', [
    'url' => admin_url('admin-ajax.php'),
    'security' => wp_create_nonce('seg'),
    'ru_security' => wp_create_nonce('ru_seg'),
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
  
  // Condicional de rol: si es talento_humano, solo ve los aprobados.
  $where_clause = "";
  if (current_user_can('talento_humano') && !current_user_can('secretario')) {
      $where_clause = " WHERE cv_aprobado = 1";
  }
  
  $sql = "SELECT 
    ID,
    DATE_FORMAT(fecha_creacion, '%Y-%m-%d %H:%i') AS 'Fecha_Inscripcion',
    first_name AS 'Nombres',
    last_name AS 'Apellidos',
    modalidad AS 'Modalidad',
    sede AS 'Sede',
    gender AS 'Genero',
    IF(birth_date IS NOT NULL AND birth_date != '0000-00-00', TIMESTAMPDIFF(YEAR, birth_date, CURDATE()), '') AS 'Edad',
    city AS 'Ciudad',
    phone_number AS 'Celular',
    email AS 'Correo',
    curso_realizado AS 'Curso',
    ciclo_curso AS 'Ciclo',
    fecha_acta_curso AS 'Fecha_Acta',
    fecha_vencimiento_curso AS 'Vencimiento',
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
    cv_file AS 'Archivo_CV',
    cv_aprobado AS 'Aprobado'
FROM $table $where_clause ORDER BY ID DESC;";

  $response = $wpdb->get_results($sql);
  
  if ($wpdb->last_error) {
      wp_send_json([['Nombres' => 'Error SQL: ' . $wpdb->last_error]]);
  }

  wp_send_json($response);
}

add_action('wp_ajax_get_cvs', 'beGetCv');

// Shortcode para la hoja de vida del estudiante
function be_student_cv_shortcode() {
    if (!is_user_logged_in()) {
        return '<div class="alert alert-warning">Debes iniciar sesión para ver la Bolsa de Empleo.</div>';
    }

    if (!be_current_user_can_access_bolsa()) {
        return '<div class="alert alert-warning">No tienes permiso para ver la Bolsa de Empleo.</div>';
    }

    ob_start();
    include plugin_dir_path(__FILE__) . 'be-student-cv.php';
    return ob_get_clean();
}
add_shortcode('mi_hoja_de_vida', 'be_student_cv_shortcode');

// Action para aprobar/rechazar CV por el Secretario o Administrador
function be_approve_cv() {
    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce, 'seg') || (!current_user_can('secretario') && !current_user_can('manage_options'))) {
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

