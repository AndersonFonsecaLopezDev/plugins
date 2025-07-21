<?php

use PhpOffice\PhpSpreadsheet\IOFactory; // Si usas PhpSpreadsheet

/*
 * Add my new menu to the Admin Control Panel
 */

// Add a new top level menu link to the ACP
function cc_Add_My_Admin_Link()
{
  // Agregar el menu principal
  add_menu_page(
    'Consulta de certificados', // Titulo de la pagina
    'Consulta certificados', // Texto para mostrar en el menu
    'manage_options', // Capability requirement to see the link
    plugin_dir_path(__FILE__) . 'cc-first-acp-page.php', // The 'slug' - file to display when clicking the link
    null, // Funcion del sitio
    plugin_dir_url(__FILE__) . '../admin/img/icon.png', // Icono del menu
    1
  );
  // Agregar submenu cursos vencidos
  add_submenu_page(
    plugin_dir_path(__FILE__) . 'cc-first-acp-page.php', // Parent slug
    'Cursos Vencidos', // Titulo de la pagina
    'Cursos Vencidos', // Titulo del menu
    'manage_options', // Capability requirement to see the link
    plugin_dir_path(__FILE__) . 'cc-menu-cursos-vencidos.php', // slug
    null
  );
  // Agregar el submenu
  add_submenu_page(
    plugin_dir_path(__FILE__) . 'cc-first-acp-page.php', // Parent slug
    'Ajustes', // Titulo de la pagina
    'Ajustes', // Titulo del menu
    'manage_options', // Capability requirement to see the link
    plugin_dir_path(__FILE__) . 'cc-menu-ajustes.php', // slug
    null
  );
}
// Hook the 'admin_menu' action hook, run the function named 'cc_Add_My_Admin_Link()'
add_action('admin_menu', 'cc_Add_My_Admin_Link');

function cc_load_assets($hook)
{
  // Definir todos los assets aquí
  $assets = [
    'consulta-certificados/includes/cc-first-acp-page.php' => [
      'scripts' => [
        [
          'handle' => 'cc_first_page_js',
          'src'    => plugins_url('../admin/js/cc-first-acp-page.js', __FILE__),
          'deps'   => ['jquery'],
          'localize' => true,
        ],
        [
          'handle' => 'cc_search_certificate_js',
          'src'    => plugins_url('../admin/js/cc-search-certificate.js', __FILE__),
          'deps'   => ['jquery'],
          'localize' => false,
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
    'consulta-certificados/includes/cc-menu-cursos-vencidos.php' => [
      'scripts' => [
        [
          'handle' => 'cc_cursos_vencidos_js',
          'src'    => plugins_url('../admin/js/cc-menu-cursos-vencidos.js', __FILE__),
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
        plugins_url('../admin/bootstrap/css/bootstrap.min.css', __FILE__),
        plugins_url('../admin/css/datatables.min.css', __FILE__),
        plugins_url('../admin/css/styles.css', __FILE__),
      ],
    ],
    'consulta-certificados/includes/cc-menu-ajustes.php' => [
      'scripts' => [
        [
          'handle' => 'cc_ajustes_js',
          'src'    => plugins_url('../admin/js/cc-menu-ajustes.js', __FILE__),
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
add_action('admin_enqueue_scripts', 'cc_load_assets');

// Shortcode
function setShortCode($atts)
{
  /*if ($hook == "consulta-certificados/includes/cc-first-acp-page.php") {
    return;
  }*/
  $_short = new shortcode;
  $id = $atts['atts'];
  $html = $_short->getCertificateForm();
  return $html;
}
add_shortcode("ENC", "setShortCode");

// Ajax
function getCertificate()
{
  // Declaración de variables
  $document = $_POST['document'];
  $nonce = $_POST['nonce'];
  // Verificacion de seguridad
  if (!wp_verify_nonce($nonce, 'seg')) {
    die('No tiene permisos para ejecutar');
  }
  if (isset($document) && !empty($document)) {
    // Inicializar el array para almacenar los datos filtrados
    $response = [];
    // Abrir el archivo Csv
    $file = plugin_dir_path(__FILE__) . "certificado/listado.csv";
    if (($handle = fopen($file, "r")) !== FALSE) {
      // Leer la primera línea del archivo (encabezados)
      $encabezados = fgetcsv($handle, 1000, ";");
      while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        // Extraer el valor del campo "cédula"
        $cedula = $data[array_search("CEDULA", $encabezados)];
        // Verificar si la cédula coincide con el criterio de búsqueda
        if ($cedula == $document) { // Reemplazar "VALOR_CEDULA" con el valor real
          // Extraer los datos deseados
          $datosExtraidos = array_combine($encabezados, $data);
          // Almacenar los datos extraídos en el array filtrado
          $response[] = $datosExtraidos;
        }
      }
      fclose($handle);
    }
    wp_send_json($response);
  }
}
add_action('wp_ajax_get_certificate', 'getCertificate');

// Ajax
function getAllCertificates()
{
  // Declaración de variables
  $nonce = $_POST['nonce'];
  $contador = 0;
  // Verificacion de seguridad
  if (!wp_verify_nonce($nonce, 'seg')) {
    die('No tiene permisos para ejecutar');
  }
  $response = [];
  // Abrir el archivo Csv
  $file = plugin_dir_path(__FILE__) . "certificado/listado.csv";
  if (($handle = fopen($file, "r")) !== FALSE) {
    // Leer la primera línea del archivo (encabezados)
    $encabezados = fgetcsv($handle, 1000, ";");
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
      // Extraer los datos deseados
      $datosExtraidos = array_combine($encabezados, $data);
      // Almacenar los datos extraídos en el array filtrado
      $response[] = $datosExtraidos;
      $contador++;
    }

    fclose($handle);
    wp_send_json($response);
  }
}
add_action('wp_ajax_get_all_certificates', 'getAllCertificates');

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

function handle_import_excel()
{
  check_ajax_referer('seg', 'nonce');

  if (!isset($_FILES['excel_file'])) {
    wp_send_json_error(['message' => 'No se recibió ningún archivo']);
    wp_die();
  }

  $file = $_FILES['excel_file']['tmp_name'];

  if (!file_exists($file)) {
    wp_send_json_error(['message' => 'Archivo no encontrado']);
    wp_die();
  }

  require_once plugin_dir_path(__FILE__) . '../vendor/autoload.php';

  try {
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true); // Mantiene índices tipo 'A', 'B', 'C'...

    global $wpdb;
    $table = "{$wpdb->prefix}cc_certificados";

    $inserted_rows = [];
    $duplicated_rows = [];

    foreach ($rows as $index => $row) {
      if ($index === 1) continue; // Saltar encabezado (fila 1)

      // Mapear columnas (ajusta según la columna real de tu Excel o CSV)
      $razon_social    = sanitize_text_field($row['A'] ?? '');
      $nit             = sanitize_text_field($row['B'] ?? '');
      $resolucion      = sanitize_text_field($row['C'] ?? '');
      $fecha_resolucion = !empty($row['D']) ? date('Y-m-d', strtotime($row['D'])) : null;
      $apellido1       = sanitize_text_field($row['E'] ?? '');
      $apellido2       = sanitize_text_field($row['F'] ?? '');
      $nombre1         = sanitize_text_field($row['G'] ?? '');
      $nombre2         = sanitize_text_field($row['H'] ?? '');
      $cedula          = sanitize_text_field($row['I'] ?? '');
      $numerodiploma   = sanitize_text_field($row['J'] ?? '');
      $curso           = sanitize_text_field($row['K'] ?? '');
      $acta            = sanitize_text_field($row['L'] ?? '');
      $fecha_acta      = !empty($row['M']) ? date('Y-m-d', strtotime($row['M'])) : null;

      // Validar si viene el número de diploma
      if (empty($numerodiploma)) {
        continue; // Saltar fila si falta el diploma
      }

      // Verificar si ya existe
      $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE numerodiploma = %s",
        $numerodiploma
      ));

      if ($exists) {
        $duplicated_rows[] = $numerodiploma;
        continue;
      }

      // Insertar el registro
      $wpdb->insert($table, [
        'razon_social'     => $razon_social,
        'nit'              => $nit,
        'resolucion'       => $resolucion,
        'fecha_resolucion' => $fecha_resolucion,
        'apellido1'        => $apellido1,
        'apellido2'        => $apellido2,
        'nombre1'          => $nombre1,
        'nombre2'          => $nombre2,
        'cedula'           => $cedula,
        'numerodiploma'    => $numerodiploma,
        'curso'            => $curso,
        'acta'             => $acta,
        'fecha_acta'       => $fecha_acta,
      ]);

      // Guardar para previsualización si quieres
      $inserted_rows[] = [
        'razon_social' => $razon_social,
        'nit'          => $nit,
        'resolucion'   => $resolucion,
        'numerodiploma' => $numerodiploma,
        'nombre_completo' => trim("$nombre1 $nombre2 $apellido1 $apellido2"),
        'curso'        => $curso,
      ];
    }

    wp_send_json_success([
      'message' => 'Importación completada correctamente.',
      'inserted' => count($inserted_rows),
      'duplicated' => count($duplicated_rows),
      'duplicated_list' => $duplicated_rows,
      'rows' => $inserted_rows,
    ]);
  } catch (Exception $e) {
    wp_send_json_error(['message' => 'Error al leer el archivo: ' . $e->getMessage()]);
  }

  wp_die();
}

add_action('wp_ajax_import_excel', 'handle_import_excel');
add_action('wp_ajax_generate_diploma', 'generate_diploma_handler');
add_action('wp_ajax_nopriv_generate_diploma', 'generate_diploma_handler');

function generate_diploma_handler() {
    include plugin_dir_path(__FILE__) . 'pdf-generador.php';
    exit;
}
add_action('wp_ajax_generate_diploma', 'generate_diploma_handler');
add_action('wp_ajax_nopriv_generate_diploma', 'generate_diploma_handler');