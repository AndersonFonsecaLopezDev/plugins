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
}


function ru_render_admin_page() {
  include_once plugin_dir_path(__FILE__) . 'ru-first-page.php';
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
