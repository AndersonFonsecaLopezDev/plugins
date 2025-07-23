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
  include plugin_dir_path(__FILE__) . 'ru-first-page.php';
}


function mostrar_registro_usuarios() {
  ob_start();
  include plugin_dir_path(__FILE__) . 'ru-first-page.php';
  return ob_get_clean();
}

// Registrar el shortcode
add_shortcode('registro-usuarios', 'mostrar_registro_usuarios');
