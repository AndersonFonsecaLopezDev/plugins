<?php

add_action('admin_menu', 'ad_Add_My_Admin_Link');

function ad_Add_My_Admin_Link()
{
  add_menu_page(
    'Asistencia diaria',               // Título de la página
    'Asistencia diaria',               // Texto en el menú
    'manage_options',                  // Capacidad requerida
    'asistencia-diaria',              // SLUG (clave única)
    'ad_render_admin_page',           // Función callback que imprime la página
    'dashicons-list-view',
    1
  );


}



// Esta función se ejecuta cuando se carga la página del admin
function ad_render_admin_page() {
  include plugin_dir_path(__FILE__) . 'dashboard.php';
}

function cargar_bootstrap_en_admin($hook) {
    // Verifica que sea tu página de plugin
    if ($hook !== 'toplevel_page_asistencia-diaria') {
        return;
    }

    wp_enqueue_style(
        'bootstrap-css-admin',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'
    );

    wp_enqueue_script(
        'bootstrap-js-admin',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
        array('jquery'),
        null,
        true
    );
}
add_action('admin_enqueue_scripts', 'cargar_bootstrap_en_admin');
