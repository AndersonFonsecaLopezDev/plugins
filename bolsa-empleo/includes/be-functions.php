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
    plugin_dir_path(__FILE__) . 'be-firstpage.php', // Parent slug
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
function beGetCv()
{
  $nonce = $_POST['nonce'];
  // Verificacion de seguridad
  if (!wp_verify_nonce($nonce, 'seg')) {
    die('No tiene permisos para ejecutar');
  }
  global $wpdb;
  $sql = "SELECT CONCAT(a.meta_value,' ',b.meta_value)  AS 'Nombres', c.meta_value AS 'Género', 
  d.meta_value AS 'Edad'
  FROM aesswp_postmeta a 
  LEFT JOIN aesswp_postmeta b ON a.post_id=b.post_id 
  LEFT JOIN aesswp_postmeta c ON a.post_id=c.post_id 
  LEFT JOIN aesswp_postmeta d ON a.post_id=d.post_id 
  LEFT JOIN aesswp_postmeta e ON a.post_id=e.post_id 
  LEFT JOIN aesswp_postmeta f ON a.post_id=f.post_id 

  WHERE a.meta_key = 'jobapp_nombre' 
  AND b.meta_key = 'jobapp_apellidos'
  AND c.meta_key = 'jobapp_sexo'
  AND d.meta_key = 'jobapp_edad'
";
  $response = $wpdb->get_results($sql);

  wp_send_json($response);
}

add_action('wp_ajax_get_cvs', 'beGetCv');
