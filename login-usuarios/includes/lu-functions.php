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
    add_menu_page(
        'Login usuarios',
        'Login usuarios',
        'manage_options',
        'login-usuarios',
        'lu_render_admin_page',
        'dashicons-admin-users',
        1
    );
}

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
 * Procesar login desde el formulario
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

    // 3. Validar estado
    if (isset($user->estado) && $user->estado !== 'activo') {
        wp_die('Tu cuenta no está activa.');
    }

    // 4. Revisar si existe en wp_users
    $wp_user = get_user_by('email', $email);

    if (!$wp_user) {
        // Si no existe → lo creamos como subscriber
        $username = sanitize_user(current(explode('@', $email)));
        $user_id  = wp_create_user($username, wp_generate_password(), $email);

        if (is_wp_error($user_id)) {
            wp_die('Error creando usuario en WordPress: ' . $user_id->get_error_message());
        }

        wp_update_user([
            'ID'   => $user_id,
            'role' => 'subscriber',
        ]);

        $wp_user = get_user_by('id', $user_id);
    }

    // 5. Forzar login (sin chequear otra vez la pass en WP)
    wp_set_current_user($wp_user->ID);
    wp_set_auth_cookie($wp_user->ID, true); // true = remember me
    do_action('wp_login', $wp_user->user_login, $wp_user);

    // 6. Redirigir al home (o a donde quieras)
    wp_safe_redirect(home_url('/'));
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
