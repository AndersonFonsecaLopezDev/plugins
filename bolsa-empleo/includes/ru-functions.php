<?php



function ru_render_ajustes_registro() {
    include_once plugin_dir_path(__FILE__) . 'ru-menu-ajustes.php';
}

function ru_render_admin_page() {
  include_once plugin_dir_path(__FILE__) . 'ru-admin-page.php';
}


function mostrar_registro_usuarios() {
  ob_start();
  include_once plugin_dir_path(__FILE__) . 'ru-first-page.php';
  return ob_get_clean();
}

// Genera un código de activación aleatorio para usuarios
function generar_codigo_activacion($longitud = 6) {
    $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $codigo = '';
    for ($i = 0; $i < $longitud; $i++) {
        $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    return $codigo;
}

// Valida el código de activación para el email proporcionado
function validar_codigo_activacion($email, $codigo_ingresado, $conn) {
    $sql = "SELECT codigo_activacion FROM wp_aess_users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $codigo_guardado = null;
        $stmt->bind_result($codigo_guardado);
        if ($stmt->fetch()) {
            $stmt->close();
            return $codigo_guardado === $codigo_ingresado;
        }
        $stmt->close();
    }
    return false;
}

// Registrar el shortcode para mostrar el formulario de registro
add_shortcode('registro-usuarios', 'mostrar_registro_usuarios');

// Encola scripts y estilos solo si el shortcode está presente en la página
function ru_enqueue_front_scripts() {
    if (is_admin()) return; // No cargar en el admin
    global $post;
    if (!is_a($post, 'WP_Post')) return;

    $shortcodes = [
        'registro-usuarios',
        'login-usuarios',
        'lu_forgot_password'
    ];

    $has_shortcode = false;
    foreach ($shortcodes as $shortcode) {
        if (has_shortcode($post->post_content, $shortcode)) {
            $has_shortcode = true;
            break;
        }
    }

    if ($has_shortcode) {
        wp_enqueue_style('ru_bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', [], '5.3.2');
        wp_enqueue_style('ru_izitoast', 'https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css', [], '1.4.0');
        wp_enqueue_style('ru_fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', [], '6.5.2');
        wp_enqueue_style('ru-first-page-css', plugin_dir_url(__FILE__) . 'ru-first-page.css', [], '1.0');

        wp_enqueue_script('ru_izitoast', 'https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js', [], '1.4.0', true);
        wp_enqueue_script('ru-first-page-js', plugin_dir_url(__FILE__) . 'ru-first-page.js', ['jquery'], '1.0', true);

        $ciudades = function_exists('ru_get_ciudades_colombia') ? ru_get_ciudades_colombia() : [];
        $js_data = [
            'ciudadesPorDepartamento' => $ciudades,
            'mostrarPasoVerificacion' => isset($_SESSION['codigo_activacion']) && isset($_SESSION['email_registro']),
            'forzarPaso4' => isset($_POST['activar_cuenta']) && isset($_POST['codigo_activacion']) && isset($_POST['email_activacion']) && isset($forzar_paso_4) && $forzar_paso_4,
            'mensajeActivacion' => isset($mensaje_activacion) ? $mensaje_activacion : '',
            'tipoMensajeActivacion' => isset($tipo_mensaje_activacion) ? $tipo_mensaje_activacion : '',
        ];
        wp_localize_script('ru-first-page-js', 'ruData', $js_data);
    }
}
add_action('wp_enqueue_scripts', 'ru_enqueue_front_scripts');

// Encola scripts y estilos para DataTables y Bootstrap solo en las páginas admin del plugin
add_action('admin_enqueue_scripts', function($hook) {
    // Página principal
    if ($hook === 'toplevel_page_registro-usuarios') {
        wp_enqueue_style('ru_bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
        wp_enqueue_style('ru_datatables', 'https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css');
        wp_enqueue_style('ru_fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css');
        wp_enqueue_script('ru_datatables', 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js', ['jquery']);
        wp_enqueue_script('ru_datatables_bs', 'https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js', ['ru_datatables']);
        wp_enqueue_script('ru_bootstrap_admin_js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', ['jquery'], '5.3.2', true);
        wp_enqueue_script('ru_admin_page', plugins_url('../admin/js/ru-admin-page.js', __FILE__), ['ru_datatables_bs', 'ru_bootstrap_admin_js'], false, true);
        wp_localize_script('ru_admin_page', 'ru_ajax', [
            'url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('ru_seg')
        ]);
    }
    // Submenú ajustes
    if ($hook === 'registro-usuarios_page_registro-usuarios-ajustes') {
        wp_enqueue_script('ru_menu_ajustes', plugins_url('../admin/js/ru-menu-ajustes.js', __FILE__), ['jquery'], false, true);
        wp_localize_script('ru_menu_ajustes', 'ru_ajax', [
            'url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('ru_seg')
        ]);
    }
});

// Endpoint AJAX: devuelve la lista de usuarios para DataTables
add_action('wp_ajax_get_all_users', function() {
    check_ajax_referer('ru_seg', 'nonce');
    global $wpdb;
    $usuarios = $wpdb->get_results(
        "SELECT ID, last_name, second_last_name, first_name, middle_name, national_id, email, phone_number, user_role, address FROM {$wpdb->prefix}aess_users",
        ARRAY_A
    );
    wp_send_json($usuarios);
});

add_action('wp_ajax_get_user_details', function() {
    check_ajax_referer('ru_seg', 'nonce');
    global $wpdb;
    $user_id = intval($_POST['id'] ?? 0);
    if (!$user_id) {
        wp_send_json_error('ID de usuario inválido');
    }

    $user = $wpdb->get_row($wpdb->prepare(
        "SELECT ID, first_name, middle_name, last_name, second_last_name, national_id, email, phone_number, address, user_role FROM {$wpdb->prefix}aess_users WHERE ID = %d",
        $user_id
    ), ARRAY_A);

    if (!$user) {
        wp_send_json_error('Usuario no encontrado');
    }

    wp_send_json_success($user);
});

add_action('wp_ajax_ru_update_user', function() {
    check_ajax_referer('ru_seg', 'nonce');
    global $wpdb;

    $user_id = intval($_POST['id'] ?? 0);
    if (!$user_id) {
        wp_send_json_error('Usuario inválido');
    }

    $user_data = [
        'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
        'middle_name' => sanitize_text_field($_POST['middle_name'] ?? ''),
        'last_name' => sanitize_text_field($_POST['last_name'] ?? ''),
        'second_last_name' => sanitize_text_field($_POST['second_last_name'] ?? ''),
        'national_id' => sanitize_text_field($_POST['national_id'] ?? ''),
        'email' => sanitize_email($_POST['email'] ?? ''),
        'phone_number' => sanitize_text_field($_POST['phone_number'] ?? ''),
        'address' => sanitize_text_field($_POST['address'] ?? ''),
        'user_role' => sanitize_text_field($_POST['user_role'] ?? ''),
    ];

    $format = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];

    $updated = $wpdb->update(
        "{$wpdb->prefix}aess_users",
        $user_data,
        ['ID' => $user_id],
        $format,
        ['%d']
    );

    if (false === $updated) {
        wp_send_json_error('No se pudo actualizar el usuario.');
    }

    wp_send_json_success('Usuario actualizado con éxito.');
});

// Endpoint AJAX: importa usuarios desde un archivo CSV
add_action('wp_ajax_ru_import_users_csv', function() {

    // Asegura que la función hash_password esté disponible
    if (!function_exists('hash_password')) {
        include_once plugin_dir_path(__FILE__) . 'ru-password.php';
    }
    
    check_ajax_referer('ru_seg', 'nonce');
    if (!isset($_FILES['file'])) {
        wp_send_json_error(['message' => 'No se recibió ningún archivo.']);
    }
    $file = $_FILES['file']['tmp_name'];
    if (!file_exists($file)) {
        wp_send_json_error(['message' => 'Archivo no encontrado.']);
    }
    $handle = fopen($file, 'r');
    if (!$handle) {
        wp_send_json_error(['message' => 'No se pudo abrir el archivo.']);
    }
    $header = fgetcsv($handle, 0, ';');
    $inserted = 0;
    $duplicated = 0;
    $errors = [];
    global $wpdb;
    while (($row = fgetcsv($handle, 0, ';')) !== false) {
        $data = array_combine($header, $row);
        // Validar correo y cédula únicos (ahora en español)
        $email = trim($data['correo'] ?? '');
        $national_id = trim($data['cedula'] ?? '');
        if (!$email || !$national_id) continue;
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}aess_users WHERE email = %s OR national_id = %s", $email, $national_id));
        if ($exists) {
            $duplicated++;
            continue;
        }
        // Preparar datos usando encabezados en español
        $raw_password = trim($data['contrasena'] ?? '');
        $hashed_password = $raw_password ? (function_exists('hash_password') ? hash_password($raw_password) : $raw_password) : '';
        $user_data = [
            'first_name' => trim($data['nombre'] ?? ''),
            'middle_name' => trim($data['segundo_nombre'] ?? ''),
            'last_name' => trim($data['apellido'] ?? ''),
            'second_last_name' => trim($data['segundo_apellido'] ?? ''),
            'national_id' => $national_id,
            'gender' => trim($data['genero'] ?? ''),
            'birth_date' => trim($data['fecha_nacimiento'] ?? ''),
            'departamento' => trim($data['departamento'] ?? ''),
            'city' => trim($data['ciudad'] ?? ''),
            'phone_number' => trim($data['telefono'] ?? ''),
            'email' => $email,
            'address' => trim($data['direccion'] ?? ''),
            'user_role' => trim($data['rol'] ?? 'estudiante'),
            'contrasena' => $hashed_password,
            'codigo_activacion' => trim($data['codigo_activacion'] ?? '')
        ];
        $format = [
            '%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'
        ];
        $result = $wpdb->insert($wpdb->prefix . 'aess_users', $user_data, $format);
        if ($result) {
            $inserted++;
        } else {
            $errors[] = $wpdb->last_error;
        }
    }
    fclose($handle);
    wp_send_json_success([
        'inserted' => $inserted,
        'duplicated' => $duplicated,
        'errors' => $errors
    ]);
});
