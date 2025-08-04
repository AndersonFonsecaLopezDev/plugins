<?php

use PhpOffice\PhpSpreadsheet\IOFactory; // Si usas PhpSpreadsheet

/*
 * Add my new menu to the Admin Control Panel
 */

// Add a new top level menu link to the ACP
function cv_Add_My_Admin_Link()
{
  // Agregar el menu principal
  add_menu_page(
    'Consulta de cursos vencidos', // Titulo de la pagina
    'Consulta cursos vencidos', // Texto para mostrar en el menu
    'manage_options', // Capability requirement to see the link
    plugin_dir_path(__FILE__) . 'cv-first-acp-page.php', // The 'slug' - file to display when clicking the link
    null, // Funcion del sitio
    plugin_dir_url(__FILE__) . '../admin/img/icon.png', // Icono del menu
    1
  );

  // Agregar el submenu
  add_submenu_page(
    plugin_dir_path(__FILE__) . 'cv-first-acp-page.php', // Parent slug
    'Ajustes', // Titulo de la pagina
    'Ajustes', // Titulo del menu
    'manage_options', // Capability requirement to see the link
    plugin_dir_path(__FILE__) . 'cv-menu-ajustes.php', // slug
    null
  );
}
// Hook the 'admin_menu' action hook, run the function named 'cc_Add_My_Admin_Link()'
add_action('admin_menu', 'cv_Add_My_Admin_Link');

function cv_load_assets($hook)
{
  // Definir todos los assets aquí
  $assets = [
    'cursos-vencidos/includes/cv-first-acp-page.php' => [
      'scripts' => [
        [
          'handle' => 'cv_first_page_js',
          'src'    => plugins_url('../admin/js/cv-first-acp-page.js', __FILE__),
          'deps'   => ['jquery'],
          'localize' => true,
        ],
        [
          'handle' => 'datatables_js',
          'src'    => plugins_url('../admin/js/datatables.min.js', __FILE__),
          'deps'   => ['jquery'],
          'localize' => false,
        ],
      ],
      'styles' => [
        plugins_url('../admin/css/styles.css', __FILE__),
        plugins_url('../admin/bootstrap/css/bootstrap.min.css', __FILE__),
        plugins_url('../admin/css/datatables.min.css', __FILE__),
      ],
    ],
    'cursos-vencidos/includes/cv-menu-ajustes.php' => [
      'scripts' => [
        [
          'handle' => 'datatables_js',
          'src'    => plugins_url('../admin/js/datatables.min.js', __FILE__),
          'deps'   => ['jquery'],
          'localize' => false,
        ],
      ],
      'styles' => [
        plugins_url('../admin/bootstrap/css/bootstrap.min.css', __FILE__),
        plugins_url('../admin/css/datatables.min.css', __FILE__),
        plugins_url('../admin/css/styles.css', __FILE__),
      ],
    ],
  ];

  // Si la página tiene assets definidos
  if (isset($assets[$hook])) {
    // Cargar estilos
    foreach ($assets[$hook]['styles'] as $style_src) {
      wp_enqueue_style(md5($style_src), $style_src);
    }

    // Cargar scripts
    foreach ($assets[$hook]['scripts'] as $script) {
      wp_enqueue_script($script['handle'], $script['src'], $script['deps'], false, true);

      // Si necesita localize
      if (!empty($script['localize'])) {
        wp_localize_script($script['handle'], 'personal_ajax_request', [
          'url' => admin_url('admin-ajax.php'),
          'security' => wp_create_nonce('seg')
        ]);
      }
    }
  }
}
add_action('admin_enqueue_scripts', 'cv_load_assets');

// Shortcode
function setShortCode($atts)
{

}
add_shortcode("ENC", "setShortCode");

function cv_verify_nonce($nonce)
{
  if (!wp_verify_nonce($nonce, 'seg')) {
    status_header(403);
    wp_send_json_error('Acceso no autorizado', 403);
    wp_die();
  }
  return true;
}

function getAllCursosVencidos()
{
  // Verificación de seguridad
  if (!wp_verify_nonce($_POST['nonce'], 'seg')) {
    wp_send_json_error('Acceso no autorizado', 403);
  }

  $file = plugin_dir_path(__FILE__) . "certificado/listado.csv";
  $response = [];
  $fecha_actual = new DateTime();

  if (($handle = fopen($file, "r")) !== FALSE) {
    $encabezados = fgetcsv($handle, 1000, ";");

    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
      $row = array_combine($encabezados, $data);

      if (!empty($row['FECHA_ACTA'])) {
        $fecha_emision = DateTime::createFromFormat('d/m/Y', $row['FECHA_ACTA']);

        if ($fecha_emision) {
          $fecha_vencimiento = clone $fecha_emision;

          $fecha_vencimiento->add(new DateInterval('P1Y'));


          if ($fecha_actual > $fecha_vencimiento) {
            // Manejo del teléfono (si no existe en el CSV)
            $telefono = 'No registrado';
            if (isset($row['TÉLEFONO']) && !empty($row['TÉLEFONO'])) {
              $telefono = $row['TÉLEFONO'];
            } elseif (isset($row['TELEFONO'])) { // Por si acaso viene sin tilde
              $telefono = $row['TELEFONO'];
            }


            $response[] = [
              'nombre' => trim($row['NOMBRE1'] . ' ' . ($row['NOMBRE2'] ?? '') . ' ' .
                $row['APELLIDO1'] . ' ' . ($row['APELLIDO2'] ?? '')),
              'curso' => $row['CURSO'],
              'telefono' => $telefono,
              'fecha_acta' => $row['FECHA_ACTA'],

            ];
          }
        }
      }
    }
    fclose($handle);

    wp_send_json($response);
  } else {
    wp_send_json_error("No se pudo abrir listado.csv", 500);
  }
}
add_action('wp_ajax_get_all_cursos_vencidos', 'getAllCursosVencidos');

