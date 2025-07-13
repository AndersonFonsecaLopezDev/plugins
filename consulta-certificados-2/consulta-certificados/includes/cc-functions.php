<?php
/*
 * Add my new menu to the Admin Control Panel
 */
// Hook the 'admin_menu' action hook, run the function named 'cc_Add_My_Admin_Link()'
add_action('admin_menu', 'cc_Add_My_Admin_Link');
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
  // Agregar el submenu
  add_submenu_page(
    plugin_dir_path(__FILE__) . 'cc-first-acp-page.php', // Parent slug
    'Ajustes', // Titulo de la pagina
    'Ajustes', // Titulo del menu
    'manage_options', // Capability requirement to see the link
    plugin_dir_path(__FILE__) . 'cc-menu-ajustes.php', // slug
    null
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
 
}
// Cargar JS de Bootstrap
function loadBootstrapJS($hook)
{
  if ($hook != "consulta-certificados/includes/cc-first-acp-page.php" && 
      $hook != "consulta-certificados/includes/cc-menu-cursos-vencidos.php") {
    return;
  }
  wp_enqueue_script('bootstrap_js', plugins_url('../admin/bootstrap/js/bootstrap.min.js', __FILE__), array('jquery'));
}

add_action('admin_enqueue_scripts', 'loadBootstrapJS');

// Cargar CSS de Bootstrap

function loadBootstrapCSS($hook)
{
  // Comprobar si el hook corresponde a una de las páginas especificadas
  if ($hook != "consulta-certificados/includes/cc-first-acp-page.php" && 
      $hook != "consulta-certificados/includes/cc-menu-cursos-vencidos.php") {
    return;
  }

  // Encolar el CSS de Bootstrap
  wp_enqueue_style('bootstrap_css', plugins_url('../admin/bootstrap/css/bootstrap.min.css', __FILE__));
}

// Cargar JS de dataTables
function loadDataTablesJS($hook)
{
  if ($hook != "consulta-certificados/includes/cc-first-acp-page.php" && 
      $hook != "consulta-certificados/includes/cc-menu-cursos-vencidos.php") {
    return;
  }
  wp_enqueue_script('dataTables_js', plugins_url('../admin/js/datatables.min.js', __FILE__), array('jquery'));
}

add_action('admin_enqueue_scripts', 'loadDataTablesJS');

// Cargar CSS de dataTables
function loadDataTablesCSS($hook)
{
  if ($hook != "consulta-certificados/includes/cc-first-acp-page.php" && 
      $hook != "consulta-certificados/includes/cc-menu-cursos-vencidos.php") {
    return;
  }
  wp_enqueue_style('dataTables_css', plugins_url('../admin/css/datatables.min.css', __FILE__));
}

add_action('admin_enqueue_scripts', 'loadDataTablesCSS');

// Cargar JS personalizado
function loadJS($hook)
{
  if ($hook != "consulta-certificados/includes/cc-first-acp-page.php") {
    return;
  }
  wp_enqueue_script('personal_js', plugins_url('../admin/js/cc-first-acp-page.js', __FILE__));
  wp_enqueue_script('personal2_js', plugins_url('../admin/js/cc-search-certificate.js', __FILE__));
  wp_localize_script('personal_js', 'personal_ajax_request', [
    'url' => admin_url('admin-ajax.php'),
    'security' => wp_create_nonce('seg')
  ]);
}

add_action('admin_enqueue_scripts', 'loadJS');

// Cargar CSS personalizado
function loadCSS($hook)
{
  if ($hook != "consulta-certificados/includes/cc-first-acp-page.php" && 
      $hook != "consulta-certificados/includes/cc-menu-cursos-vencidos.php") {
    return;
  }
  wp_enqueue_style('personal_css', plugins_url('../admin/css/styles.css', __FILE__));
}


add_action('admin_enqueue_scripts', 'loadCSS');


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
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE ) {
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
    // Declaración de variables
    $nonce = $_POST['nonce'];
    $contador = 0;

    // Verificación de seguridad
    if (!wp_verify_nonce($nonce, 'seg')) {
        die('No tiene permisos para ejecutar');
    }

    $response = [];
    
    // Abrir el archivo CSV
    $file = plugin_dir_path(__FILE__) . "certificado/listado.csv"; // listado corto.
    if (($handle = fopen($file, "r")) !== FALSE) {
        // Leer la primera línea del archivo (encabezados)
        $encabezados = fgetcsv($handle, 1000, ";");

        // Obtener la fecha actual
        $fecha_actual = new DateTime();

        // Leer los datos de cada fila
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            // Extraer los datos en un array asociativo
            $datosExtraidos = array_combine($encabezados, $data);

            // Convertir "FECHA ACTA" a objeto DateTime para comparar
            if (!empty($datosExtraidos['FECHA_ACTA'])) {
                $fecha_acta_dt = DateTime::createFromFormat('d/m/Y', $datosExtraidos['FECHA_ACTA']); // Ajustar formato según tu CSV
                
                // Verificar si la "FECHA ACTA" es menor a la fecha actual (vencido)
                if ($fecha_acta_dt && $fecha_acta_dt < $fecha_actual) {
                    // Almacenar en el array de respuesta
                    $response[] = [
                        'nombre' => trim($datosExtraidos['NOMBRE1'] . ' ' . $datosExtraidos['NOMBRE2'] . ' ' . $datosExtraidos['APELLIDO1'] . ' ' . $datosExtraidos['APELLIDO2']),
                        'curso' => trim($datosExtraidos['CURSO']),
                        'fecha_acta' => $datosExtraidos['FECHA_ACTA']
                    ];

                    // Incrementar el contador
                    $contador++;
                }
            }
        }

        fclose($handle);

        // Devolver los datos en formato JSON
        wp_send_json($response);
    } else {
        // Manejar el caso en que no se pueda abrir el archivo
        wp_send_json_error("No se pudo abrir el archivo CSV.");
    }
}

add_action('wp_ajax_get_all_cursos_vencidos', 'getAllCursosVencidos');



// Cargar JS para el submenu de cursos vencidos
function loadCursosVencidosJS($hook)
{
    if ($hook != "consulta-certificados/includes/cc-menu-cursos-vencidos.php") {
        return;
    }
    wp_enqueue_script('personal_js', plugins_url('../admin/js/cc-menu-cursos-vencidos.js', __FILE__));
    wp_localize_script('personal_js', 'personal_ajax_request', [
        'url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('seg')
    ]);
}

add_action('admin_enqueue_scripts', 'loadCursosVencidosJS');

// Cargar JS de dataTables
function loadDataTablesCursosVencidosJS($hook)
{
  if ($hook != "consulta-certificados/includes/cc-menu-cursos-vencidos.php") {
    return;
  }
  wp_enqueue_script('dataTables_js', plugins_url('../admin/js/datatables.min.js', __FILE__), array('jquery'));
}
add_action('admin_enqueue_scripts', 'loadDataTablesCursosVencidosJS');