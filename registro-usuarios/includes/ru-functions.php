<?php

add_action('admin_menu', 'ru_Add_My_Admin_Link');

function ru_Add_My_Admin_Link(){
    add_menu_page(
        'Registro usuarios',               // Título de la página
        'Registro usuarios',               // Texto en el menú
        'manage_options',                  // Capacidad requerida
        'registro-usuarios',              // SLUG (clave única)
        'ru_render_admin_page',           // Función callback que imprime la página
        'dashicons-admin-users', // Icono del menú
        1
    );  
    add_submenu_page(
        'registro-usuarios',               // SLUG del menú padre
        'Ajustes',         // Título de la página
        'Ajustes',         // Texto en el menú
        'manage_options',                  // Capacidad requerida
        'registro-usuarios-ajustes',       // SLUG del submenú
        'ru_render_ajustes_registro'   // Función callback que imprime la página del submenú
    );
}


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

// Genera un código de activación aleatorio de 6 dígitos
function generar_codigo_activacion($longitud = 6) {
    $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $codigo = '';
    for ($i = 0; $i < $longitud; $i++) {
        $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    return $codigo;
}

// Valida si el código ingresado coincide con el guardado en la base de datos para el email dado
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

// Registrar el shortcode
add_shortcode('registro-usuarios', 'mostrar_registro_usuarios');

// Encolar scripts y estilos solo cuando el shortcode esté presente
function ru_enqueue_front_scripts() {
    if (is_admin()) return; // No cargar en admin
    global $post;
    if (!is_a($post, 'WP_Post')) return;
    if (has_shortcode($post->post_content, 'registro-usuarios')) {
        wp_enqueue_style(
            'ru-first-page-css',
            plugin_dir_url(__FILE__) . 'ru-first-page.css',
            [],
            '1.0'
        );
        wp_enqueue_script(
            'ru-first-page-js',
            plugin_dir_url(__FILE__) . 'ru-first-page.js',
            ['jquery'],
            '1.0',
            true
        );
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

// Encolar scripts y estilos en el admin SOLO para la página de este plugin

// Encolar scripts y estilos para DataTables y Bootstrap SOLO en la página admin del plugin
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'toplevel_page_registro-usuarios') return;
    // Usar jQuery de WordPress
    wp_enqueue_style('ru_bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
    wp_enqueue_style('ru_datatables', 'https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css');
    wp_enqueue_script('ru_datatables', 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js', ['jquery']);
    wp_enqueue_script('ru_datatables_bs', 'https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js', ['ru_datatables']);
    wp_enqueue_script('ru_admin_page', plugins_url('../admin/js/ru-admin-page.js', __FILE__), ['ru_datatables_bs'], false, true);
    wp_localize_script('ru_admin_page', 'ru_ajax', [
        'url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('ru_seg')
    ]);
});

// Endpoint AJAX para devolver usuarios
add_action('wp_ajax_get_all_users', function() {
    check_ajax_referer('ru_seg', 'nonce');
    global $wpdb;
    $usuarios = $wpdb->get_results("SELECT last_name, second_last_name, first_name, middle_name, national_id FROM {$wpdb->prefix}aess_users", ARRAY_A);
    wp_send_json($usuarios);
});
