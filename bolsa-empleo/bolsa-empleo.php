<?php
/*
Plugin Name: Bolsa de empleo
Description: Plugin para consultar informacion de las hojas de vida
Version: 1.3
Author: Anderson Fonseca Lopez
*/

require_once plugin_dir_path(__FILE__) . 'includes/be-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/ru-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/lu-functions.php';

// Hook de activación
register_activation_hook(__FILE__, 'be_plugin_activate');

function be_plugin_activate() {
    // Asegurar roles y opciones por defecto al activar el plugin.
    if (function_exists('be_setup_plugin_defaults')) {
        be_setup_plugin_defaults();
    }

    // Crear/Verificar tabla wp_aess_users
    if (function_exists('be_create_tables')) {
        be_create_tables();
    }

    if (function_exists('be_maybe_upgrade_database')) {
        be_maybe_upgrade_database();
    }
}
