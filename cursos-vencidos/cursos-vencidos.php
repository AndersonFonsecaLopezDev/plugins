<?php
/*
Plugin Name: Cursos vencidos
Description: Plugin para consultar informacion desde un archivo Excel.
Version: 0.1
Author: Anderson Fonseca Lopez
*/

function Activar() {
}


function Desactivar() {
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'Activar');
register_deactivation_hook(__FILE__, 'Desactivar');

require_once plugin_dir_path(__FILE__) . 'class/shortcode.class.php';
require_once plugin_dir_path(__FILE__) . 'includes/cv-functions.php';
