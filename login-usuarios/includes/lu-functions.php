<?php

add_action('admin_menu', 'lu_Add_My_Admin_Link');

function lu_Add_My_Admin_Link(){
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


function lu_render_admin_page(){
    include_once plugin_dir_path(__FILE__) . 'lu-login-page.php';
}


function mostrar_login_usuarios() {
    ob_start();
    include_once plugin_dir_path(__FILE__) . 'lu-login-page.php';
    return ob_get_clean();
}


// Registrar el shortcode para mostrar el formulario de registro
add_shortcode('login-usuarios', 'mostrar_login_usuarios');