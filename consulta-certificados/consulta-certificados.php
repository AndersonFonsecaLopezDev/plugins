<?php
/*
Plugin Name: Consulta Certificados
Description: Plugin para consultar informacion desde un archivo Excel.
Version: 0.1
Author: Anderson Fonseca Lopez
*/

function Activar() {
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'cc_certificados';

    $sql = "CREATE TABLE $table_name (
        idCertificado INT(11) NOT NULL AUTO_INCREMENT,
        razon_social VARCHAR(255) NOT NULL,
        nit VARCHAR(100) NOT NULL,
        resolucion VARCHAR(100) DEFAULT NULL,
        fecha_resolucion DATE DEFAULT NULL,
        apellido1 VARCHAR(100) DEFAULT NULL,
        apellido2 VARCHAR(100) DEFAULT NULL,
        nombre1 VARCHAR(100) DEFAULT NULL,
        nombre2 VARCHAR(100) DEFAULT NULL,
        cedula VARCHAR(50) DEFAULT NULL,
        numerodiploma VARCHAR(100) DEFAULT NULL,
        curso VARCHAR(255) DEFAULT NULL,
        acta VARCHAR(100) DEFAULT NULL,
        fecha_acta DATE DEFAULT NULL,
        estado VARCHAR(20) DEFAULT 'activo',
        PRIMARY KEY (idCertificado),
        UNIQUE (numerodiploma)
    ) $charset_collate;";

    dbDelta($sql);
}


function Desactivar() {
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'Activar');
register_deactivation_hook(__FILE__, 'Desactivar');

require_once plugin_dir_path(__FILE__) . 'class/shortcode.class.php';
require_once plugin_dir_path(__FILE__) . 'includes/cc-functions.php';
