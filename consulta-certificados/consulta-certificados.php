<?php

/*
Plugin Name: Consulta Certificados
Description: Plugin para consultar informacion desde un archivo Excel.
Version: 0.1
Author: Anderson Fonseca Lopez
*/

// Include cc-functions.php, use require_once to stop the script if cc-functions.php is not found

function Activar(){
    // En caso de hacer un desarrollo especifico se puede generar una tabla
    // global $wpdb;
    // $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}cc_ certificados(
    // `idCertificado` INT NOT NULL ,
    // `razon_social` VARCHAR NOT NULL ,
    // )";
    // $wpdb->query($sql);
}

function Desactivar(){
    flush_rewrite_rules();
}

register_activation_hook(__FILE__,'Activar');
register_deactivation_hook(__FILE__,'Desactivar');

require_once plugin_dir_path(__FILE__) . 'class/shortcode.class.php';
require_once plugin_dir_path(__FILE__) . 'includes/cc-functions.php';