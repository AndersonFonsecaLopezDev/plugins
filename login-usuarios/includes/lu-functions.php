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

    // Sanitizar datos del formulario
    $email = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo "<script>alert('Debes completar todos los campos');window.history.back();</script>";
        exit;
    }

    // Buscar en la tabla personalizada
    $table = $wpdb->prefix . 'aess_users';
    $user = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table WHERE email = %s", $email)
    );

    if (!$user) {
        echo "<script>alert('Usuario no encontrado');window.history.back();</script>";
        exit;
    }

    // Verificar contraseña
    if (!verify_password($password, $user->contrasena)) {
        echo "<script>alert('Contraseña incorrecta');window.history.back();</script>";
        exit;
    }

    // Verificar estado si existe
    if (isset($user->estado) && $user->estado !== 'activo') {
        echo "<script>alert('Tu cuenta no está activa');window.history.back();</script>";
        exit;
    }

    // Si todo bien → login exitoso
    $token = LU_JWT::generar_token($user);

    // Guardar en cookie segura
    setcookie('jwt_token', $token, [
        'expires'  => time() + 3600,
        'path'     => '/',
        'domain'   => COOKIE_DOMAIN,
        'secure'   => is_ssl(),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    
    // Solo para pruebas → mostrar token
    echo "<script>alert('✅ Login exitoso. Token generado: $token');window.history.back();</script>";
    exit;
}

// Hooks para procesar el action=custom_login
add_action('admin_post_nopriv_custom_login', 'lu_procesar_login_form');
add_action('admin_post_custom_login', 'lu_procesar_login_form');
