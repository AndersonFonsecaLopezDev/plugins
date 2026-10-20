<?php
/*
Plugin Name: Bolsa de empleo
Description: Plugin para consultar informacion de las hojas de vida
Version: 1.2
Author: Anderson Fonseca Lopez
*/

require_once plugin_dir_path(__FILE__) . 'includes/be-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/ru-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/lu-functions.php';

// Hook de activación
register_activation_hook(__FILE__, 'be_plugin_activate');

function be_plugin_activate() {
    global $wpdb;

    // Asegurar roles y opciones por defecto al activar el plugin.
    if (function_exists('be_setup_plugin_defaults')) {
        be_setup_plugin_defaults();
    }

    // Crear/Modificar tabla wp_aess_users
    $table_name = $wpdb->prefix . 'aess_users';
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        first_name varchar(100) NOT NULL,
        middle_name varchar(100) DEFAULT '',
        last_name varchar(100) NOT NULL,
        second_last_name varchar(100) DEFAULT '',
        national_id varchar(50) NOT NULL,
        gender varchar(50) DEFAULT '',
        birth_date date,
        departamento varchar(100) DEFAULT '',
        city varchar(100) DEFAULT '',
        phone_number varchar(50) DEFAULT '',
        email varchar(100) NOT NULL,
        address varchar(255) DEFAULT '',
        modalidad varchar(50) DEFAULT '' NOT NULL,
        sede varchar(150) DEFAULT '' NOT NULL,
        curso_realizado varchar(200) DEFAULT '' NOT NULL,
        ciclo_curso varchar(100) DEFAULT '' NOT NULL,
        fecha_acta_curso date DEFAULT NULL,
        fecha_vencimiento_curso date DEFAULT NULL,
        codigo_activacion varchar(20) DEFAULT '' NOT NULL,
        fecha_activacion datetime,
        email_verificado tinyint(1) DEFAULT 0,
        academico varchar(255) DEFAULT '',
        profesion varchar(255) DEFAULT '',
        estado_laboral varchar(100) DEFAULT '',
        user_role varchar(50) DEFAULT 'estudiante',
        contrasena varchar(255) NOT NULL,
        libreta_militar varchar(100) DEFAULT '',
        experiencia text,
        diploma varchar(255) DEFAULT '',
        estatura varchar(50) DEFAULT '',
        barrio varchar(255) DEFAULT '',
        cv_file varchar(255) DEFAULT '',
        cv_aprobado tinyint(1) DEFAULT 0,
        fecha_creacion datetime DEFAULT CURRENT_TIMESTAMP,
        reset_code varchar(10) DEFAULT '',
        reset_code_expires datetime DEFAULT NULL,
        PRIMARY KEY  (ID),
        UNIQUE KEY email (email)
    ) $charset_collate;";

    dbDelta($sql);

    if (function_exists('be_maybe_upgrade_database')) {
        be_maybe_upgrade_database();
    }
}
