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
  // Agregar el submenu
  add_submenu_page(
    plugin_dir_path(__FILE__) . 'be-first-page.php', // Parent slug
    'Ajustes', // Titulo de la pagina
    'Ajustes', // Titulo del menu
    'manage_options', // Capability requirement to see the link
    plugin_dir_path(__FILE__) . 'be-menu-ajustes.php', // slug
    null
  );
}
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
    'security' => wp_create_nonce('seg')
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
  
  $sql = "SELECT 
    p.ID,
    MAX(CASE WHEN pm.meta_key = 'jobapp_nombre' THEN pm.meta_value END) AS 'Nombres',
    MAX(CASE WHEN pm.meta_key = 'jobapp_apellidos' THEN pm.meta_value END) AS 'Apellidos',
    MAX(CASE WHEN pm.meta_key = 'jobapp_sexo' THEN pm.meta_value END) AS 'Género',
    MAX(CASE WHEN pm.meta_key = 'jobapp_edad' THEN pm.meta_value END) AS 'Edad',
    MAX(CASE WHEN pm.meta_key = 'jobapp_ciudad' THEN pm.meta_value END) AS 'Ciudad',
    MAX(CASE WHEN pm.meta_key = 'jobapp_celular' THEN pm.meta_value END) AS 'Celular',
    MAX(CASE WHEN pm.meta_key = 'jobapp_correo' THEN pm.meta_value END) AS 'Correo',
    MAX(CASE WHEN pm.meta_key = 'jobapp_academico' THEN pm.meta_value END) AS 'Académico',
    MAX(CASE WHEN pm.meta_key = 'jobapp_profesion' THEN pm.meta_value END) AS 'Profesion',
    MAX(CASE WHEN pm.meta_key = 'jobapp_estado' THEN pm.meta_value END) AS 'Estado',
    MAX(CASE WHEN pm.meta_key = 'jobapp_libreta_militar' THEN pm.meta_value END) AS 'Libreta_militar',
    MAX(CASE WHEN pm.meta_key = 'jobapp_experiencia' THEN pm.meta_value END) AS 'Experiencia',
    MAX(CASE WHEN pm.meta_key = 'jobapp_diploma' THEN pm.meta_value END) AS 'Diploma',
    MAX(CASE WHEN pm.meta_key = 'jobapp_estatura' THEN pm.meta_value END) AS 'Estatura',
    MAX(CASE WHEN pm.meta_key = 'jobapp_barrio' THEN pm.meta_value END) AS 'Barrio',
    MAX(CASE WHEN pm.meta_key = 'jobapp_direccion' THEN pm.meta_value END) AS 'Dirección',
    MAX(CASE WHEN pm.meta_key = 'jobapp_departamento' THEN pm.meta_value END) AS 'Departamento',
    MAX(CASE WHEN pm.meta_key = 'jobapp_telefono' THEN pm.meta_value END) AS 'Teléfono',
    MAX(CASE WHEN pm.meta_key = 'jobapp_cedula' THEN pm.meta_value END) AS 'Cédula'
FROM 
    aesswp_posts p
    INNER JOIN aesswp_postmeta pm ON p.ID = pm.post_id
WHERE 
    p.post_type = 'jobpost_applicants'
    AND p.post_status = 'publish'
    AND pm.meta_key IN (
        'jobapp_nombre', 'jobapp_apellidos', 'jobapp_sexo',
        'jobapp_edad', 'jobapp_ciudad', 'jobapp_celular',
        'jobapp_correo', 'jobapp_academico', 'jobapp_profesion',
        'jobapp_estado', 'jobapp_libreta_militar', 'jobapp_experiencia',
        'jobapp_diploma', 'jobapp_estatura', 'jobapp_barrio',
        'jobapp_direccion', 'jobapp_departamento', 'jobapp_telefono',
        'jobapp_cedula'
    )
GROUP BY 
    p.ID;";
  

  $response = $wpdb->get_results($sql);

  wp_send_json($response);
}

add_action('wp_ajax_get_cvs', 'beGetCv');
