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
    'access_certificados', // Capability requirement to see the link
    plugin_dir_path(__FILE__) . 'cc-first-acp-page.php', // The 'slug' - file to display when clicking the link
    null, // Funcion del sitio
    plugin_dir_url(__FILE__) . '../admin/img/icon.png', // Icono del menu
    1
  );
  // Agregar submenu de Cargue
  add_submenu_page(
    plugin_dir_path(__FILE__) . 'cc-first-acp-page.php', // Parent slug
    'Cargue', // Titulo de la pagina
    'Cargue', // Titulo del menu
    'access_certificados', // Capability requirement to see the link
    plugin_dir_path(__FILE__) . 'cc-menu-cargue.php', // slug
    null
  );
  // Agregar submenu de ajustes reales
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
    'consulta-certificados/includes/cc-menu-cargue.php' => [
      'scripts' => [
        [
          'handle' => 'cc_menu_cargue_js',
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
    $bolsa_style_path = plugin_dir_path(__FILE__) . '../../bolsa-empleo/admin/css/styles.css';
    if (file_exists($bolsa_style_path)) {
      wp_enqueue_style('cc_bolsa_admin_styles', plugins_url('../../bolsa-empleo/admin/css/styles.css', __FILE__));
    }

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

function cc_register_settings() {
  register_setting('cc_settings_group', 'cc_access_certificados_estudiante');
  register_setting('cc_settings_group', 'cc_access_certificados_secretario');
  register_setting('cc_settings_group', 'cc_access_certificados_talento_humano');
}
add_action('admin_init', 'cc_register_settings');

function cc_ensure_custom_role_caps() {
  $roles = ['secretario', 'talento_humano', 'estudiante'];
  foreach ($roles as $role) {
    $existing_role = get_role($role);
    if ($existing_role) {
      if (!$existing_role->has_cap($role)) {
        $existing_role->add_cap($role, true);
      }
      if (!$existing_role->has_cap('access_certificados')) {
        $existing_role->add_cap('access_certificados', true);
      }
    }
  }

  $admin_role = get_role('administrator');
  if ($admin_role) {
    $admin_role->add_cap('access_certificados');
    $admin_role->add_cap('secretario');
    $admin_role->add_cap('talento_humano');
    $admin_role->add_cap('estudiante');
  }
}
add_action('init', 'cc_ensure_custom_role_caps');

function cc_current_user_can_access_certificados() {
  if (current_user_can('manage_options')) {
    return true;
  }

  $role = null;
  if (current_user_can('secretario')) {
    $role = 'secretario';
  } elseif (current_user_can('talento_humano')) {
    $role = 'talento_humano';
  } elseif (current_user_can('read')) {
    $role = 'estudiante';
  }

  if (!$role) {
    return false;
  }

  $option_name = 'cc_access_certificados_' . $role;
  return (bool) get_option($option_name, true);
}

function cc_render_ajustes() {
  include_once plugin_dir_path(__FILE__) . 'cc-menu-ajustes.php';
}

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

function cc_verify_nonce($nonce)
{
  if (!wp_verify_nonce($nonce, 'seg')) {
    status_header(403);
    wp_send_json_error('Acceso no autorizado', 403);
    wp_die();
  }
  return true;
}

function getCertificate()
{
  // Verificar que existan los parámetros necesarios
  if (!isset($_POST['nonce']) || !isset($_POST['document'])) {
    status_header(400);
    wp_send_json_error('Parámetros incompletos');
    wp_die();
  }

  $nonce = sanitize_text_field($_POST['nonce']);
  $document = sanitize_text_field($_POST['document']);

  // Verificación de seguridad
  cc_verify_nonce($nonce);

  if (empty($document)) {
    wp_send_json_error('Documento vacío', 400);
    wp_die();
  }

  global $wpdb;
  $table = $wpdb->prefix . 'cc_certificados';
  $prepared = $wpdb->prepare(
    "SELECT * FROM $table WHERE cedula = %s OR numerodiploma = %s",
    $document,
    $document
  );

  $results = $wpdb->get_results($prepared, ARRAY_A);
  if (!$results) {
    $results = [];
  }

  wp_send_json($results);
  wp_die();
}

// 3. Registrar hooks CORRECTAMENTE
add_action('wp_ajax_get_certificate', 'getCertificate');
add_action('wp_ajax_nopriv_get_certificate', 'getCertificate');

// Ajax
function getAllCertificates()
{
  $nonce = sanitize_text_field($_POST['nonce'] ?? '');
  if (!wp_verify_nonce($nonce, 'seg')) {
    wp_send_json_error('Acceso no autorizado', 403);
    wp_die();
  }

  global $wpdb;
  $table = $wpdb->prefix . 'cc_certificados';

  $sql = "SELECT 
    razon_social AS 'RAZON SOCIAL',
    nit AS 'NIT',
    resolucion AS 'RESOLUCION',
    DATE_FORMAT(fecha_resolucion, '%Y-%m-%d') AS 'FECHA RESOLUCION',
    apellido1 AS 'APELLIDO1',
    apellido2 AS 'APELLIDO2',
    nombre1 AS 'NOMBRE1',
    nombre2 AS 'NOMBRE2',
    cedula AS 'CEDULA',
    numerodiploma AS 'NUMERODIPLOMA',
    curso AS 'CURSO',
    acta AS 'ACTA',
    DATE_FORMAT(fecha_acta, '%Y-%m-%d') AS 'FECHA_ACTA'
  FROM $table";

  $response = $wpdb->get_results($sql, ARRAY_A);
  if ($wpdb->last_error) {
    wp_send_json([['RAZON SOCIAL' => 'Error SQL: ' . $wpdb->last_error]]);
    wp_die();
  }

  wp_send_json($response);
}
add_action('wp_ajax_get_all_certificates', 'getAllCertificates');

function getAllCursosVencidos()
{
  if (!wp_verify_nonce($_POST['nonce'] ?? '', 'seg')) {
    wp_send_json_error('Acceso no autorizado', 403);
    wp_die();
  }

  global $wpdb;
  $certificados_table = $wpdb->prefix . 'cc_certificados';
  $users_table = $wpdb->prefix . 'aess_users';

  $where = 'WHERE c.fecha_acta IS NOT NULL';
  $params = [];

  $fecha_desde = sanitize_text_field($_POST['fecha_desde'] ?? '');
  $fecha_hasta = sanitize_text_field($_POST['fecha_hasta'] ?? '');

  if (!empty($fecha_desde)) {
    $where .= ' AND c.fecha_acta >= %s';
    $params[] = $fecha_desde;
  }

  if (!empty($fecha_hasta)) {
    $where .= ' AND c.fecha_acta <= %s';
    $params[] = $fecha_hasta;
  }

  $sql = "SELECT c.apellido1, c.apellido2, c.nombre1, c.nombre2, c.cedula, c.curso, c.acta, c.fecha_acta, COALESCE(u.phone_number, 'No registrado') AS telefono, COALESCE(u.email, 'No registrado') AS email FROM {$certificados_table} c LEFT JOIN {$users_table} u ON TRIM(u.national_id) = TRIM(c.cedula) {$where}";

  if (!empty($params)) {
    $rows = $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
  } else {
    $rows = $wpdb->get_results($sql, ARRAY_A);
  }

  $response = [];
  $fecha_actual = new DateTime();

  foreach ($rows as $row) {
    $fecha_emision = DateTime::createFromFormat('Y-m-d', $row['fecha_acta']);
    if (!$fecha_emision) {
      continue;
    }

    $fecha_vencimiento = clone $fecha_emision;
    $fecha_vencimiento->add(new DateInterval('P1Y'));

    if ($fecha_actual > $fecha_vencimiento) {
      $response[] = [
        'nombre' => trim(($row['nombre1'] ?? '') . ' ' . ($row['nombre2'] ?? '') . ' ' . ($row['apellido1'] ?? '') . ' ' . ($row['apellido2'] ?? '')),
        'curso' => $row['curso'],
        'telefono' => $row['telefono'] ?: 'No registrado',
        'email' => $row['email'] ?: 'No registrado',
        'fecha_acta' => $row['fecha_acta'],
        'fecha_vencimiento' => $fecha_vencimiento->format('Y-m-d'),
        'estado' => 'Vencido',
      ];
    }
  }

  wp_send_json($response);
}
add_action('wp_ajax_get_all_cursos_vencidos', 'getAllCursosVencidos');

// function handle_import_excel()
// {
//   check_ajax_referer('seg', 'nonce');

//   if (!isset($_FILES['excel_file'])) {
//     wp_send_json_error(['message' => 'No se recibió ningún archivo']);
//     wp_die();
//   }

//   $file = $_FILES['excel_file']['tmp_name'];

//   if (!file_exists($file)) {
//     wp_send_json_error(['message' => 'Archivo no encontrado']);
//     wp_die();
//   }

//   require_once plugin_dir_path(__FILE__) . '../vendor/autoload.php';

//   try {
//     $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
//     $sheet = $spreadsheet->getActiveSheet();
//     $rows = $sheet->toArray(null, true, true, true); // Mantiene índices tipo 'A', 'B', 'C'...

//     global $wpdb;
//     $table = "{$wpdb->prefix}cc_certificados";

//     $inserted_rows = [];
//     $duplicated_rows = [];

//     foreach ($rows as $index => $row) {
//       if ($index === 1) continue; // Saltar encabezado (fila 1)

//       // Mapear columnas (ajusta según la columna real de tu Excel o CSV)
//       $razon_social    = sanitize_text_field($row['A'] ?? '');
//       $nit             = sanitize_text_field($row['B'] ?? '');
//       $resolucion      = sanitize_text_field($row['C'] ?? '');
//       $fecha_resolucion = !empty($row['D']) ? date('Y-m-d', strtotime($row['D'])) : null;
//       $apellido1       = sanitize_text_field($row['E'] ?? '');
//       $apellido2       = sanitize_text_field($row['F'] ?? '');
//       $nombre1         = sanitize_text_field($row['G'] ?? '');
//       $nombre2         = sanitize_text_field($row['H'] ?? '');
//       $cedula          = sanitize_text_field($row['I'] ?? '');
//       $numerodiploma   = sanitize_text_field($row['J'] ?? '');
//       $curso           = sanitize_text_field($row['K'] ?? '');
//       $acta            = sanitize_text_field($row['L'] ?? '');
//       $fecha_acta      = !empty($row['M']) ? date('Y-m-d', strtotime($row['M'])) : null;

//       // Validar si viene el número de diploma
//       if (empty($numerodiploma)) {
//         continue; // Saltar fila si falta el diploma
//       }

//       // Verificar si ya existe
//       $exists = $wpdb->get_var($wpdb->prepare(
//         "SELECT COUNT(*) FROM $table WHERE numerodiploma = %s",
//         $numerodiploma
//       ));

//       if ($exists) {
//         $duplicated_rows[] = $numerodiploma;
//         continue;
//       }

//       // Insertar el registro
//       $wpdb->insert($table, [
//         'razon_social'     => $razon_social,
//         'nit'              => $nit,
//         'resolucion'       => $resolucion,
//         'fecha_resolucion' => $fecha_resolucion,
//         'apellido1'        => $apellido1,
//         'apellido2'        => $apellido2,
//         'nombre1'          => $nombre1,
//         'nombre2'          => $nombre2,
//         'cedula'           => $cedula,
//         'numerodiploma'    => $numerodiploma,
//         'curso'            => $curso,
//         'acta'             => $acta,
//         'fecha_acta'       => $fecha_acta,
//       ]);

//       // Guardar para previsualización si quieres
//       $inserted_rows[] = [
//         'razon_social' => $razon_social,
//         'nit'          => $nit,
//         'resolucion'   => $resolucion,
//         'numerodiploma' => $numerodiploma,
//         'nombre_completo' => trim("$nombre1 $nombre2 $apellido1 $apellido2"),
//         'curso'        => $curso,
//       ];
//     }

//     wp_send_json_success([
//       'message' => 'Importación completada correctamente.',
//       'inserted' => count($inserted_rows),
//       'duplicated' => count($duplicated_rows),
//       'duplicated_list' => $duplicated_rows,
//       'rows' => $inserted_rows,
//     ]);
//   } catch (Exception $e) {
//     wp_send_json_error(['message' => 'Error al leer el archivo: ' . $e->getMessage()]);
//   }

//   wp_die();
// }

function handle_import_excel() {
    if (!check_ajax_referer('seg', 'nonce', false)) {
        wp_send_json_error(['message' => 'Nonce inválido. Recarga la página e intenta de nuevo.']);
        wp_die();
    }

    if (!isset($_FILES['excel_file'])) {
        wp_send_json_error(['message' => 'No se recibió ningún archivo.']);
        wp_die();
    }

    if ($_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP (upload_max_filesize).',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido por el formulario (post_max_size).',
            UPLOAD_ERR_PARTIAL => 'La subida fue parcial. Intenta de nuevo.',
            UPLOAD_ERR_NO_FILE => 'No se envió ningún archivo.',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal en el servidor.',
            UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el disco.',
            UPLOAD_ERR_EXTENSION => 'La subida fue detenida por una extensión de PHP.',
        ];

        $message = $upload_errors[$_FILES['excel_file']['error']] ?? 'Error desconocido de subida de archivo.';
        $message .= ' upload_max_filesize=' . ini_get('upload_max_filesize') . ', post_max_size=' . ini_get('post_max_size');

        wp_send_json_error([
            'message' => $message,
            'error_code' => $_FILES['excel_file']['error'],
        ]);
        wp_die();
    }

    $file = $_FILES['excel_file']['tmp_name'];
    if (empty($file) || !is_uploaded_file($file)) {
        wp_send_json_error(['message' => 'El archivo temporal no es válido o no se pudo cargar correctamente.']);
        wp_die();
    }

    if (!file_exists($file)) {
        wp_send_json_error(['message' => 'Archivo no encontrado en el servidor.']);
        wp_die();
    }

    require_once plugin_dir_path(__FILE__) . '../vendor/autoload.php';

    try {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        global $wpdb;
        $table = $wpdb->prefix . 'cc_certificados';

        $inserted_rows = [];
        $duplicated_rows = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            if ($index === 1) {
                continue; // Saltar encabezado
            }

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

            if (empty($numerodiploma)) {
                $errors[] = "Fila $index: numerodiploma vacío";
                continue;
            }

            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE numerodiploma = %s", $numerodiploma));
            if ($exists) {
                $duplicated_rows[] = $numerodiploma;
                continue;
            }

            $inserted = $wpdb->insert(
                $table,
                [
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
                ],
                ['%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s']
            );

            if ($inserted === false) {
                $errors[] = "Fila $index: " . $wpdb->last_error;
                continue;
            }

            $inserted_rows[] = [
                'razon_social'   => $razon_social,
                'nit'            => $nit,
                'numerodiploma'  => $numerodiploma,
                'nombre_completo' => trim("$nombre1 $nombre2 $apellido1 $apellido2"),
                'curso'          => $curso,
            ];
        }

        $response = [
            'message' => 'Importación completada en la base de datos.',
            'inserted' => count($inserted_rows),
            'duplicated' => count($duplicated_rows),
            'duplicated_list' => $duplicated_rows,
            'errors' => $errors,
            'rows' => $inserted_rows,
        ];

        if (!empty($errors)) {
            wp_send_json_error($response);
        } else {
            wp_send_json_success($response);
        }
    } catch (Exception $e) {
        wp_send_json_error(['message' => 'Error al procesar el archivo: ' . $e->getMessage()]);
    }

    wp_die();
}

add_action('wp_ajax_import_excel', 'handle_import_excel');
add_action('wp_ajax_generate_diploma', 'generate_diploma_handler');
add_action('wp_ajax_nopriv_generate_diploma', 'generate_diploma_handler');

function generate_diploma_handler()
{
  include plugin_dir_path(__FILE__) . 'pdf-generador.php';
  exit;
}
add_action('wp_ajax_generate_diploma', 'generate_diploma_handler');
add_action('wp_ajax_nopriv_generate_diploma', 'generate_diploma_handler');
