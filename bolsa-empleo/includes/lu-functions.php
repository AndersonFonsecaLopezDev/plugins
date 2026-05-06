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

function lu_Add_My_Admin_Link() {
    // add_menu_page removed as per user request to not show in admin
}

/**
 * Renderiza la página de login
 *
 * @return void
 */
function lu_render_admin_page() {
    include_once plugin_dir_path(__FILE__) . 'lu-login-page.php';
}

/**
 * Shortcode [login-usuarios]
 */
function mostrar_login_usuarios() {
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'lu-login-page.php';
    return ob_get_clean();
}
add_shortcode('login-usuarios', 'mostrar_login_usuarios');

require_once plugin_dir_path(__FILE__) . 'lu-jwt.php';
 
/**
 * Procesa el formulario de login
 *
 * @return void
 */
function lu_procesar_login_form() {
    global $wpdb;

    // Sanitizar datos
    $email    = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        wp_die('Debes completar todos los campos.');
    }

    // 1. Buscar en tu tabla personalizada
    $table = $wpdb->prefix . 'aess_users';
    $user  = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table WHERE email = %s", $email)
    );

    if (!$user) {
        wp_die('Usuario no encontrado.');
    }

    // 2. Validar contraseña
    if (!verify_password($password, $user->contrasena)) {
        wp_die('Contraseña incorrecta.');
    }

    // 3. Validar estado del email
    if ((int) $user->email_verificado !== 1) {
        wp_die('Debes verificar tu correo antes de iniciar sesión.');
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
            wp_die('Error creando usuario en WordPress: ' . $user_id->get_error_message());
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
function lu_forgot_password_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'lu-forgot.php';
    return ob_get_clean();
}
add_shortcode('lu_forgot_password', 'lu_forgot_password_shortcode');



// Hooks para procesar el action=custom_login
add_action('admin_post_nopriv_custom_login', 'lu_procesar_login_form');
add_action('admin_post_custom_login', 'lu_procesar_login_form');

// Bloquear acceso al dashboard de WordPress para estudiantes
function be_block_admin_access() {
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
function be_hide_admin_bar($show) {
    if (!current_user_can('manage_options') && !current_user_can('secretario') && !current_user_can('talento_humano')) {
        return false;
    }
    return $show;
}
add_filter('show_admin_bar', 'be_hide_admin_bar');
