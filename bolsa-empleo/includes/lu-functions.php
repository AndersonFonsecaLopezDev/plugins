<?php
// Seguridad: impedir acceso directo
if (!defined('ABSPATH')) {
    exit;
}


// Cargar autoload de Composer para JWT
require_once plugin_dir_path(__DIR__) . 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Incluir funciones de hash de contraseñas
require_once plugin_dir_path(__FILE__) . 'ru-password.php';

/**
 * Crear menú en admin
 */
add_action('admin_menu', 'lu_Add_My_Admin_Link');

function lu_Add_My_Admin_Link()
{
    // add_menu_page removed as per user request to not show in admin
}

/**
 * Renderiza la página de login
 *
 * @return void
 */
function lu_render_admin_page()
{
    include_once plugin_dir_path(__FILE__) . 'lu-login-page.php';
}

/**
 * Shortcode [login-usuarios]
 */
function mostrar_login_usuarios()
{
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'lu-login-page.php';
    return ob_get_clean();
}
add_shortcode('login-usuarios', 'mostrar_login_usuarios');

// Hooks for login form processing
add_action('admin_post_custom_login', 'lu_procesar_login_form');
add_action('admin_post_nopriv_custom_login', 'lu_procesar_login_form');

require_once plugin_dir_path(__FILE__) . 'lu-jwt.php';

/**
 * Procesa el formulario de login
 *
 * @return void
 */
function lu_procesar_login_form()
{
    global $wpdb;

    // Sanitizar datos
    $email = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $referer = !empty($_POST['current_url']) ? esc_url_raw($_POST['current_url']) : wp_get_referer();
    $login_url = get_option('be_login_url');
    if (empty($login_url)) {
        $login_url = $referer ? remove_query_arg(['login_error', 'login_email'], $referer) : site_url('/pagina-login');
    }

    if (empty($email) || empty($password)) {
        wp_safe_redirect(add_query_arg(
            [
                'login_error' => urlencode('Debes completar todos los campos.'),
                'login_email' => urlencode($email)
            ],
            $login_url
        ));
        exit;
    }

    // 1. Buscar en tu tabla personalizada
    $table = $wpdb->prefix . 'aess_users';
    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
        if (function_exists('be_create_tables')) {
            be_create_tables();
        }
    }
    $user = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table WHERE email = %s", $email)
    );

    if (!$user) {
        wp_safe_redirect(add_query_arg(
            [
                'login_error' => urlencode('Usuario o contraseña inválido.'),
                'login_email' => urlencode($email)
            ],
            $login_url
        ));
        exit;
    }

    // 2. Validar contraseña
    if (!verify_password($password, $user->contrasena)) {
        wp_safe_redirect(add_query_arg(
            [
                'login_error' => urlencode('Usuario o contraseña inválido.'),
                'login_email' => urlencode($email)
            ],
            $login_url
        ));
        exit;
    }

    // 3. Validar estado del email
    if ((int) $user->email_verificado !== 1) {
        wp_safe_redirect(add_query_arg(
            [
                'login_error' => urlencode('Debes verificar tu correo antes de iniciar sesión.'),
                'login_email' => urlencode($email)
            ],
            $login_url
        ));
        exit;
    }


    // 4. Revisar si existe en wp_users
    $wp_user = get_user_by('email', $email);

    $desired_role = strtolower(trim($user->user_role ?: 'estudiante'));
    if (!in_array($desired_role, ['secretario', 'talento_humano', 'estudiante'], true)) {
        $desired_role = 'estudiante';
    }

    $is_admin_role = $wp_user && in_array('administrator', (array) $wp_user->roles, true);

    if (!$wp_user) {
        $username_base = sanitize_user(strpos($email, '@') !== false ? strstr($email, '@', true) : $email, true);
        if (empty($username_base)) {
            $username_base = 'user' . time();
        }

        $username = $username_base;
        $suffix = 1;
        while (username_exists($username)) {
            $username = $username_base . $suffix;
            $suffix++;
        }

        $user_id = wp_create_user($username, wp_generate_password(), $email);

        if (is_wp_error($user_id)) {
            wp_safe_redirect(add_query_arg(
                [
                    'login_error' => urlencode('Error al crear el usuario en WordPress. Por favor intenta de nuevo.'),
                    'login_email' => urlencode($email)
                ],
                $login_url
            ));
            exit;
        }

        $wp_user_obj = new WP_User($user_id);
        $wp_user_obj->set_role($desired_role);
        $wp_user = get_user_by('id', $user_id);
    } else {
        if (!$is_admin_role) {
            $wp_user_obj = new WP_User($wp_user->ID);
            if (!in_array($desired_role, (array) $wp_user->roles, true)) {
                $wp_user_obj->set_role($desired_role);
            }
        }
    }

    // El rol secretario y talento_humano solo deben ingresar por WP-admin.
    if (in_array($desired_role, ['secretario', 'talento_humano'], true)) {
        $redirect_to_admin = admin_url();
        wp_safe_redirect($redirect_to_admin);
        exit;
    }

    // 5. Forzar login (sin chequear otra vez la pass en WP)
    wp_set_current_user($wp_user->ID);
    wp_set_auth_cookie($wp_user->ID, true); // true = remember me
    do_action('wp_login', $wp_user->user_login, $wp_user);

    // 6. Redirigir al home (o a donde se configure)
    $redirect_url = get_option('be_login_redirect_url');
    if (empty($redirect_url)) {
        $redirect_url = home_url('/');
    }
    wp_safe_redirect($redirect_url);
    exit;
}


// Shortcode para mostrar la página de recuperación de contraseña
function lu_forgot_password_shortcode()
{
    ob_start();
    include plugin_dir_path(__FILE__) . 'lu-forgot.php';
    return ob_get_clean();
}
add_shortcode('lu_forgot_password', 'lu_forgot_password_shortcode');

function be_logout_shortcode()
{
    if (!is_user_logged_in()) {
        return '';
    }

    $logout_url = wp_logout_url(get_option('be_login_url', site_url('/pagina-login')));
    return '<div class="be-logout-container" style="margin:1rem 0;"><a class="button button-secondary" href="' . esc_url($logout_url) . '">Cerrar sesión</a></div>';
}
add_shortcode('be_logout', 'be_logout_shortcode');

function be_login_status_shortcode()
{
    if (!is_user_logged_in()) {
        return '';
    }

    $current_user = wp_get_current_user();
    $logout_url = wp_logout_url(get_option('be_login_url', site_url('/pagina-login')));
    return '<div class="be-login-status" style="margin:1rem 0; padding:1rem; background:#f7f7f7; border:1px solid #ddd; border-radius:6px;">Has iniciado sesión como <strong>' . esc_html($current_user->display_name ? $current_user->display_name : $current_user->user_login) . '</strong>. <a href="' . esc_url($logout_url) . '">Cerrar sesión</a></div>';
}
add_shortcode('be_login_status', 'be_login_status_shortcode');





// Hooks para procesar el action=custom_login
add_action('admin_post_nopriv_custom_login', 'lu_procesar_login_form');
add_action('admin_post_custom_login', 'lu_procesar_login_form');

// Bloquear acceso al dashboard de WordPress para estudiantes
function be_block_admin_access()
{
    if (is_admin() && !wp_doing_ajax()) {
        if (!current_user_can('manage_options') && !current_user_can('secretario') && !current_user_can('talento_humano')) {
            $redirect_url = get_option('be_login_redirect_url');
            if (empty($redirect_url)) {
                $redirect_url = home_url('/');
            }
            wp_safe_redirect($redirect_url);
            exit;
        }
    }
}
add_action('admin_init', 'be_block_admin_access');

// Ocultar la barra de administración en el frontend para estudiantes
function be_hide_admin_bar($show)
{
    if (!current_user_can('manage_options') && !current_user_can('secretario') && !current_user_can('talento_humano')) {
        return false;
    }
    return $show;
}
add_filter('show_admin_bar', 'be_hide_admin_bar');
add_action('wp_ajax_nopriv_be_ajax_login', 'be_ajax_login_handler');
add_action('wp_ajax_be_ajax_login', 'be_ajax_login_handler');
function be_ajax_login_handler() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ru_seg')) {
        wp_send_json_error('Sesión inválida, por favor recarga la página.');
    }

    $email = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        wp_send_json_error('Correo y contraseña son requeridos.');
    }

    global $wpdb;
    $table = $wpdb->prefix . 'aess_users';
    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
        if (function_exists('be_create_tables')) {
            be_create_tables();
        }
    }
    $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE email = %s", $email));

    if (!$user) {
        wp_send_json_error('Usuario o contraseña incorrectos.');
    }

    if (!function_exists('verify_password')) {
        include_once plugin_dir_path(__FILE__) . 'ru-password.php';
    }

    if (!verify_password($password, $user->contrasena)) {
        wp_send_json_error('Usuario o contraseña incorrectos.');
    }

    if ((int) $user->email_verificado !== 1) {
        wp_send_json_error('Debes verificar tu correo antes de iniciar sesión.');
    }

    // Asegurar que el usuario exista en wp_users
    $wp_user = get_user_by('email', $email);
    $desired_role = strtolower(trim($user->user_role ?: 'estudiante'));
    if (!in_array($desired_role, ['secretario', 'talento_humano', 'estudiante'], true)) {
        $desired_role = 'estudiante';
    }

    if (!$wp_user) {
        $username_base = sanitize_user(strpos($email, '@') !== false ? strstr($email, '@', true) : $email, true);
        if (empty($username_base)) {
            $username_base = 'user' . time();
        }
        $username = $username_base;
        $suffix = 1;
        while (username_exists($username)) {
            $username = $username_base . $suffix;
            $suffix++;
        }
        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            wp_send_json_error('Error al sincronizar la cuenta con WordPress.');
        }
        $wp_user_obj = new WP_User($user_id);
        $wp_user_obj->set_role($desired_role);
        $wp_user = get_user_by('id', $user_id);
    } else {
        $is_admin_role = in_array('administrator', (array) $wp_user->roles, true);
        if (!$is_admin_role) {
            $wp_user_obj = new WP_User($wp_user->ID);
            if (!in_array($desired_role, (array) $wp_user->roles, true)) {
                $wp_user_obj->set_role($desired_role);
            }
        }
        wp_set_password($password, $wp_user->ID);
    }

    // Iniciar sesión
    wp_set_current_user($wp_user->ID);
    wp_set_auth_cookie($wp_user->ID, true);
    do_action('wp_login', $wp_user->user_login, $wp_user);

    // Determinar redirección
    if (in_array($desired_role, ['secretario', 'talento_humano'], true) || in_array('administrator', (array) $wp_user->roles, true)) {
        $redirect_url = admin_url();
    } else {
        $redirect_url = get_option('be_login_redirect_url');
        if (empty($redirect_url)) {
            $redirect_url = site_url('/mi-hoja-de-vida');
        }
    }

    wp_send_json_success(['redirect_url' => $redirect_url]);
}
